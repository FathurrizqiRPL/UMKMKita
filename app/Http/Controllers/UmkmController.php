<?php

namespace App\Http\Controllers;

use App\Models\DeletedUmkm;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UmkmController extends Controller
{
    public function dashboard(Request $request)
    {
        $umkm = $request->user()->umkm()->with(['items', 'locations'])->first();
        return view('dashboard', compact('umkm'));
    }

    public function index(Request $request)
    {
        $categories = ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Kecantikan', 'Otomotif', 'Lainnya'];

        $query = Umkm::withCount('locations')
            ->with(['locations' => fn ($q) => $q->orderBy('sort_order')])
            ->where('status', 'active');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('locations', function ($location) use ($search) {
                        $location->where('address', 'like', "%{$search}%");
                    });
            });
        }

        if ($category = $request->query('category')) {
            if (in_array($category, $categories)) {
                $query->where('category', $category);
            }
        }

        if ($type = $request->query('type')) {
            if (in_array($type, ['tetap', 'keliling'])) {
                $query->where('business_type', $type);
            }
        }

        $umkms = $query->latest()->paginate(12)->withQueryString();

        return view('umkm.lihatumkm', compact('umkms', 'categories'));
    }

    public function create(Request $request)
    {
        abort_if($request->user()->umkm()->exists(), 403, 'Kamu sudah memiliki website UMKM.');
        return view('umkm.create');
    }

    public function store(Request $request)
    {
        abort_if($request->user()->umkm()->exists(), 403, 'Kamu sudah memiliki website UMKM.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:umkms,slug'],
            'category' => ['required', 'string', 'max:50'],
            'business_type' => ['required', Rule::in(['tetap', 'keliling'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'required_if:business_type,tetap', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_if:business_type,tetap', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_if:business_type,tetap', 'numeric', 'between:-180,180'],
            'opening_time' => ['nullable', 'required_if:business_type,tetap', 'date_format:H:i'],
            'closing_time' => ['nullable', 'required_if:business_type,tetap', 'date_format:H:i', 'after:opening_time'],
            'locations' => ['nullable', 'required_if:business_type,keliling', 'array', 'min:1'],
            'locations.*.address' => ['required_if:business_type,keliling', 'string', 'max:255'],
            'locations.*.landmark' => ['nullable', 'string', 'max:255'],
            'locations.*.latitude' => ['required_if:business_type,keliling', 'numeric', 'between:-90,90'],
            'locations.*.longitude' => ['required_if:business_type,keliling', 'numeric', 'between:-180,180'],
            'locations.*.start_time' => ['required_if:business_type,keliling', 'date_format:H:i'],
            'locations.*.end_time' => ['required_if:business_type,keliling', 'date_format:H:i'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ], [
            'slug.unique' => 'URL UMKM tersebut sudah digunakan oleh orang lain. Silakan pilih URL yang lain.',
            'slug.alpha_dash' => 'URL hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
        ]);

        $locations = $data['locations'] ?? [];
        unset($data['locations']);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['slug']);

        if ($data['business_type'] === 'keliling') {
            $data['address'] = null;
            $data['landmark'] = null;
            $data['latitude'] = null;
            $data['longitude'] = null;
            $data['opening_time'] = null;
            $data['closing_time'] = null;
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('umkm', 'public');
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('umkm', 'public');
        }

        DB::transaction(function () use ($data, $locations) {
            $umkm = Umkm::create($data);

            if ($umkm->business_type === 'keliling') {
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
        });

        return redirect()->route('dashboard')->with('success', 'Website UMKM berhasil dibuat.');
    }

    public function edit(Request $request)
    {
        $umkm = $request->user()->umkm()->with(['items', 'locations'])->firstOrFail();
        return view('umkm.edit', compact('umkm'));
    }

    public function update(Request $request)
    {
        $umkm = $request->user()->umkm()->with('locations')->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('umkms', 'slug')->ignore($umkm->id)],
            'category' => ['required', 'string', 'max:50'],
            'business_type' => ['required', Rule::in(['tetap', 'keliling'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'required_if:business_type,tetap', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_if:business_type,tetap', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_if:business_type,tetap', 'numeric', 'between:-180,180'],
            'opening_time' => ['nullable', 'required_if:business_type,tetap', 'date_format:H:i'],
            'closing_time' => ['nullable', 'required_if:business_type,tetap', 'date_format:H:i', 'after:opening_time'],
            'locations' => ['nullable', 'required_if:business_type,keliling', 'array', 'min:1'],
            'locations.*.address' => ['required_if:business_type,keliling', 'string', 'max:255'],
            'locations.*.landmark' => ['nullable', 'string', 'max:255'],
            'locations.*.latitude' => ['required_if:business_type,keliling', 'numeric', 'between:-90,90'],
            'locations.*.longitude' => ['required_if:business_type,keliling', 'numeric', 'between:-180,180'],
            'locations.*.start_time' => ['required_if:business_type,keliling', 'date_format:H:i'],
            'locations.*.end_time' => ['required_if:business_type,keliling', 'date_format:H:i'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ], [
            'slug.unique' => 'URL UMKM tersebut sudah digunakan oleh orang lain. Silakan pilih URL yang lain.',
            'slug.alpha_dash' => 'URL hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
        ]);

        $locations = $data['locations'] ?? [];
        unset($data['locations']);

        $data['slug'] = Str::slug($data['slug']);

        if ($data['business_type'] === 'keliling') {
            $data['address'] = null;
            $data['landmark'] = null;
            $data['latitude'] = null;
            $data['longitude'] = null;
            $data['opening_time'] = null;
            $data['closing_time'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($umkm->logo) Storage::disk('public')->delete($umkm->logo);
            $data['logo'] = $request->file('logo')->store('umkm', 'public');
        }

        if ($request->hasFile('cover')) {
            if ($umkm->cover) Storage::disk('public')->delete($umkm->cover);
            $data['cover'] = $request->file('cover')->store('umkm', 'public');
        }

        DB::transaction(function () use ($umkm, $data, $locations) {
            $umkm->update($data);
            $umkm->locations()->delete();

            if ($umkm->business_type === 'keliling') {
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
        });

        return redirect()->route('dashboard')->with('success', 'Profil UMKM berhasil diperbarui.');
    }

    public function delete(Request $request)
    {
        $umkm = $request->user()->umkm()->firstOrFail();
        return view('umkm.hapus', compact('umkm'));
    }

    public function destroy(Request $request)
    {
        $umkm = $request->user()->umkm()->with(['items', 'locations'])->firstOrFail();

        DeletedUmkm::updateOrCreate(
            ['slug' => $umkm->slug],
            ['name' => $umkm->name, 'deleted_at' => now()]
        );

        foreach ($umkm->items as $item) {
            if ($item->image) Storage::disk('public')->delete($item->image);
        }

        if ($umkm->logo) Storage::disk('public')->delete($umkm->logo);
        if ($umkm->cover) Storage::disk('public')->delete($umkm->cover);

        $umkm->delete();

        return redirect()->route('dashboard')->with('success', 'Website UMKM berhasil dihapus.');
    }

    public function show(string $slug)
    {
        $umkm = Umkm::with(['items', 'locations'])->where('slug', $slug)->where('status', 'active')->first();

        if ($umkm) {
            return view('umkm.show', compact('umkm'));
        }

        $deletedUmkm = DeletedUmkm::where('slug', $slug)->first();

        if ($deletedUmkm) {
            return response()->view('umkm.unavailable', compact('deletedUmkm'), 410);
        }

        abort(404);
    }

    public function toggleLike(Request $request, $id)
    {
        $umkm = \App\Models\Umkm::findOrFail($id);
        
        if ($request->action === 'like') {
            $umkm->increment('likes_count');
        } else {
            $umkm->decrement('likes_count');
        }

        return response()->json([
            'success' => true, 
            'likes_count' => $umkm->likes_count
        ]);
    }

    public function toggleStatus()
    {
        $umkm = auth()->user()->umkm;
        
        if (!$umkm) {
            return back()->with('error', 'UMKM tidak ditemukan.');
        }

        $umkm->is_manual_closed = !$umkm->is_manual_closed;
        $umkm->save();

        return back()->with('success', 'Status operasional berhasil diperbarui.');
    }
}