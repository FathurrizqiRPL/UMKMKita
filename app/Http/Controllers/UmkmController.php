<?php

namespace App\Http\Controllers;

use App\Models\DeletedUmkm;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UmkmController extends Controller
{



public function showw(string $slug)
{
    $umkm = Umkm::with(['items', 'locations', 'posters'])->where('slug', $slug)->firstOrFail();

    if ($umkm->status !== 'active') {
        abort(403, 'Website UMKM ini sedang ditangguhkan.');
    }

    return view('umkm.show', compact('umkm'));
}
public function toggleWebsiteStatus(Request $request)
{
    $umkm = $request->user()->umkm;

    if ($umkm) {
        $umkm->status = $umkm->status === 'active' ? 'suspended' : 'active';
        $umkm->save();
    }

    return back()->with('success', 'Status website berhasil diperbarui.');
}
    public function dashboard(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $umkm = $request->user()->umkm()->with(['items', 'locations', 'posters'])->first();
        return view('dashboard', compact('umkm'));
    }

    public function index(Request $request)
    {
        $categories = ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Kecantikan', 'Otomotif', 'Lainnya'];

        $query = Umkm::withCount('locations')->where('status', 'active');


        $query = Umkm::withCount('locations')
            ->with(['locations' => fn ($q) => $q->orderBy('sort_order')])
            ->where('status', 'active');


        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('locations', fn ($location) => $location->where('address', 'like', "%{$search}%"));
            });
        }

        if (($category = $request->query('category')) && in_array($category, $categories)) {
            $query->where('category', $category);
        }

        if (($type = $request->query('type')) && in_array($type, ['tetap', 'keliling'])) {
            $query->where('business_type', $type);
        }

        $umkms = $query->latest()->paginate(12)->withQueryString();

        return view('umkm.lihatumkm', compact('umkms', 'categories'));
    }

    public function create(Request $request)
    {
        abort_if(
            $request->user()->umkm()->exists(),
            403,
            'Kamu sudah memiliki website UMKM.'
        );

        return view('umkm.create');
    }

   public function store(Request $request)
    {
        abort_if(
            $request->user()->umkm()->exists(),
            403,
            'Kamu sudah memiliki website UMKM.'
        );

        $request->merge([
            'slug' => Str::slug($request->slug),
        ]);

        $data = $request->validate(
            $this->rules(),
            $this->messages()
        );

        $locations = $data['locations'] ?? [];
        $posters = $data['posters'] ?? [];

        unset(
            $data['locations'],
            $data['posters'],
            $data['existing_posters'],
            $data['delete_posters']
        );

        $data['user_id'] = $request->user()->id;

        $this->normalizeBusinessData($data);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('umkm', 'public');
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('umkm', 'public');
        }

        DB::transaction(function () use ($data, $locations, $posters) {
            $umkm = Umkm::create($data);

            $this->saveLocations($umkm, $locations);
            $this->saveNewPosters($umkm, $posters);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Website UMKM berhasil dibuat.');
    }

    public function edit(Request $request)
    {
        $umkm = $request->user()
            ->umkm()
            ->with(['items', 'locations', 'posters'])
            ->firstOrFail();

        return view('umkm.edit', compact('umkm'));
    }

    public function update(Request $request)
    {
        $umkm = $request->user()
            ->umkm()
            ->with(['locations', 'posters'])
            ->firstOrFail();

        $data = $request->validate(
            $this->updateRules(),
            $this->messages()
        );

        $locations = $data['locations'] ?? [];
        $posters = $data['posters'] ?? [];
        $existingPosters = $data['existing_posters'] ?? [];
        $deletePosters = $data['delete_posters'] ?? [];

        unset(
            $data['locations'],
            $data['posters'],
            $data['existing_posters'],
            $data['delete_posters']
        );

        /*
         * Slug sengaja tidak diubah saat edit.
         * Jadi URL website tetap sama walaupun nama UMKM berubah.
         */
        $this->normalizeBusinessData($data);

        if ($request->hasFile('logo')) {
            if ($umkm->logo) {
                Storage::disk('public')->delete($umkm->logo);
            }

            $data['logo'] = $request->file('logo')->store('umkm', 'public');
        }

        if ($request->hasFile('cover')) {
            if ($umkm->cover) {
                Storage::disk('public')->delete($umkm->cover);
            }

            $data['cover'] = $request->file('cover')->store('umkm', 'public');
        }

        DB::transaction(function () use (
            $umkm,
            $data,
            $locations,
            $posters,
            $existingPosters,
            $deletePosters
        ) {
            $umkm->update($data);

            $umkm->locations()->delete();
            $this->saveLocations($umkm, $locations);

            $this->updateExistingPosters(
                $umkm,
                $existingPosters,
                $deletePosters
            );

            $nextPosterOrder = ($umkm->posters()->max('sort_order') ?? -1) + 1;

            $this->saveNewPosters(
                $umkm,
                $posters,
                $nextPosterOrder
            );
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil UMKM berhasil diperbarui.');
    }

    public function delete(Request $request)
    {
        $umkm = $request->user()->umkm()->firstOrFail();

        return view('umkm.hapus', compact('umkm'));
    }

    public function destroy(Request $request)
    {
        $umkm = $request->user()
            ->umkm()
            ->with(['items', 'locations', 'posters'])
            ->firstOrFail();

        DeletedUmkm::updateOrCreate(
            ['slug' => $umkm->slug],
            [
                'name' => $umkm->name,
                'deleted_at' => now(),
            ]
        );

        foreach ($umkm->items as $item) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
        }

        foreach ($umkm->posters as $poster) {
            if ($poster->image) {
                Storage::disk('public')->delete($poster->image);
            }
        }

        if ($umkm->logo) {
            Storage::disk('public')->delete($umkm->logo);
        }

        if ($umkm->cover) {
            Storage::disk('public')->delete($umkm->cover);
        }

        $umkm->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Website UMKM berhasil dihapus.');
    }

    public function show(string $slug)
    {
        $umkm = Umkm::with(['items', 'locations', 'posters'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if ($umkm) {
            return view('umkm.show', compact('umkm'));
        }

        $deletedUmkm = DeletedUmkm::where('slug', $slug)->first();

        if ($deletedUmkm) {
            return response()->view(
                'umkm.unavailable',
                compact('deletedUmkm'),
                410
            );
        }

        abort(404);
    }

 public function toggleLike(Request $request, $id)
{
    $umkm = Umkm::findOrFail($id);
    $user = auth()->user();

    $existingLike = $umkm->likes()->where('user_id', $user->id)->first();

    if ($existingLike) {
        $existingLike->delete();
        $action = 'unlike';
    } else {
        $umkm->likes()->create([
            'user_id' => $user->id
        ]);
        $action = 'like';
    }

    return response()->json([
        'success' => true,
        'action' => $action,
        'likes_count' => $umkm->likes()->count()
    ]);
}

    public function toggleStatus(Request $request)
    {
        $umkm = $request->user()->umkm;

        if (!$umkm) {
            return redirect()->route('dashboard')->with('error', 'UMKM tidak ditemukan.');
        }

        $umkm->is_manual_closed = !$umkm->is_manual_closed;
        $umkm->save();

        return redirect()->route('dashboard')->with(
            'success',
            $umkm->is_manual_closed ? 'Toko berhasil ditutup.' : 'Toko berhasil dibuka.'
        );
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],

            /*
             * Slug hanya diperlukan saat membuat website.
             */
            'slug' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:umkms,slug',
            ],

            'category' => ['required', 'string', 'max:50'],
            'business_type' => ['required', Rule::in(['tetap', 'keliling'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],

            'address' => [
                'nullable',
                'required_if:business_type,tetap',
                'string',
                'max:255',
            ],

            'landmark' => [
                'nullable',
                'string',
                'max:255',
            ],

            'latitude' => [
                'nullable',
                'required_if:business_type,tetap',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'required_if:business_type,tetap',
                'numeric',
                'between:-180,180',
            ],

            'opening_time' => [
                'nullable',
                'required_if:business_type,tetap',
                'date_format:H:i',
            ],

            'closing_time' => [
                'nullable',
                'required_if:business_type,tetap',
                'date_format:H:i',
                'after:opening_time',
            ],

            'locations' => [
                'nullable',
                'required_if:business_type,keliling',
                'array',
                'min:1',
            ],

            'locations.*.address' => [
                'required_if:business_type,keliling',
                'string',
                'max:255',
            ],

            'locations.*.landmark' => [
                'nullable',
                'string',
                'max:255',
            ],

            'locations.*.latitude' => [
                'required_if:business_type,keliling',
                'numeric',
                'between:-90,90',
            ],

            'locations.*.longitude' => [
                'required_if:business_type,keliling',
                'numeric',
                'between:-180,180',
            ],

            'locations.*.start_time' => [
                'required_if:business_type,keliling',
                'date_format:H:i',
            ],

            'locations.*.end_time' => [
                'required_if:business_type,keliling',
                'date_format:H:i',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'posters' => ['nullable', 'array'],

            'posters.*.title' => [
                'nullable',
                'string',
                'max:100',
            ],

            'posters.*.image' => [
                'nullable',
                'required_with:posters.*.title',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],

            /*
             * Tidak ada rule slug di sini.
             * Form edit tidak perlu mengirim slug.
             */

            'category' => ['required', 'string', 'max:50'],
            'business_type' => ['required', Rule::in(['tetap', 'keliling'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],

            'address' => [
                'nullable',
                'required_if:business_type,tetap',
                'string',
                'max:255',
            ],

            'landmark' => [
                'nullable',
                'string',
                'max:255',
            ],

            'latitude' => [
                'nullable',
                'required_if:business_type,tetap',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'required_if:business_type,tetap',
                'numeric',
                'between:-180,180',
            ],

            'opening_time' => [
                'nullable',
                'required_if:business_type,tetap',
                'date_format:H:i',
            ],

            'closing_time' => [
                'nullable',
                'required_if:business_type,tetap',
                'date_format:H:i',
                'after:opening_time',
            ],

            'locations' => [
                'nullable',
                'required_if:business_type,keliling',
                'array',
                'min:1',
            ],

            'locations.*.address' => [
                'required_if:business_type,keliling',
                'string',
                'max:255',
            ],

            'locations.*.landmark' => [
                'nullable',
                'string',
                'max:255',
            ],

            'locations.*.latitude' => [
                'required_if:business_type,keliling',
                'numeric',
                'between:-90,90',
            ],

            'locations.*.longitude' => [
                'required_if:business_type,keliling',
                'numeric',
                'between:-180,180',
            ],

            'locations.*.start_time' => [
                'required_if:business_type,keliling',
                'date_format:H:i',
            ],

            'locations.*.end_time' => [
                'required_if:business_type,keliling',
                'date_format:H:i',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'posters' => ['nullable', 'array'],

            'posters.*.title' => [
                'nullable',
                'string',
                'max:100',
            ],

            'posters.*.image' => [
                'nullable',
                'required_with:posters.*.title',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'existing_posters' => [
                'nullable',
                'array',
            ],

            'existing_posters.*.title' => [
                'nullable',
                'string',
                'max:100',
            ],

            'existing_posters.*.image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'delete_posters' => [
                'nullable',
                'array',
            ],

            'delete_posters.*' => [
                'integer',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'slug.unique' => 'URL UMKM tersebut sudah digunakan oleh orang lain. Silakan pilih URL yang lain.',
            'slug.alpha_dash' => 'URL hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',

            'posters.*.image.required_with' => 'Pilih gambar untuk poster yang sudah diberi judul.',
            'posters.*.image.max' => 'Ukuran setiap poster maksimal 5 MB.',
            'existing_posters.*.image.max' => 'Ukuran setiap poster maksimal 5 MB.',
        ];
    }

    private function normalizeBusinessData(array &$data): void
    {
        if ($data['business_type'] !== 'keliling') {
            return;
        }

        $data['address'] = null;
        $data['landmark'] = null;
        $data['latitude'] = null;
        $data['longitude'] = null;
        $data['opening_time'] = null;
        $data['closing_time'] = null;
    }

    private function saveLocations(
        Umkm $umkm,
        array $locations
    ): void {
        if ($umkm->business_type !== 'keliling') {
            return;
        }

        foreach ($locations as $index => $location) {
            $umkm->locations()->create([
                'address' => $location['address'],
                'landmark' => $location['landmark'] ?? null,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'start_time' => $location['start_time'],
                'end_time' => $location['end_time'],
                'sort_order' => $index,
            ]);
        }
    }

    private function saveNewPosters(
        Umkm $umkm,
        array $posters,
        int $startOrder = 0
    ): void {
        foreach ($posters as $poster) {
            if (empty($poster['image'])) {
                continue;
            }

            $umkm->posters()->create([
                'title' => $poster['title'] ?? null,
                'image' => $poster['image']
                    ->store('umkm/posters', 'public'),
                'sort_order' => $startOrder++,
            ]);
        }
    }

    private function updateExistingPosters(
        Umkm $umkm,
        array $existing,
        array $deleteIds
    ): void {
        $deleteIds = array_map('intval', $deleteIds);

        foreach ($umkm->posters()->get() as $poster) {
            if (in_array($poster->id, $deleteIds, true)) {
                Storage::disk('public')->delete($poster->image);
                $poster->delete();
                continue;
            }

            $payload = $existing[$poster->id] ?? [];

            if (array_key_exists('title', $payload)) {
                $poster->title = $payload['title'];
            }

            if (!empty($payload['image'])) {
                Storage::disk('public')->delete($poster->image);

                $poster->image = $payload['image']
                    ->store('umkm/posters', 'public');
            }

            $poster->save();
        }
    }
}
