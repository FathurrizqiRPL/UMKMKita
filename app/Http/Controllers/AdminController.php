<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalUmkms = Umkm::count();
        $activeUmkms = Umkm::where('status', 'active')->count();
        $suspendedUmkms = Umkm::where('status', 'suspended')->count();
        $totalItems = Item::count();

        $latestUmkms = Umkm::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalUmkms',
            'activeUmkms',
            'suspendedUmkms',
            'totalItems',
            'latestUmkms'
        ));
    }

    public function umkms(Request $request)
    {
        $query = Umkm::with('user')
            ->withCount('items')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $umkms = $query->paginate(15)->withQueryString();

        return view('admin.umkms', compact('umkms'));
    }

    public function updateUmkmStatus(Request $request, Umkm $umkm)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        $umkm->status = $data['status'];
        $umkm->save();

        return back()->with(
            'success',
            $umkm->status === 'active'
                ? 'UMKM berhasil diaktifkan.'
                : 'UMKM berhasil dinonaktifkan.'
        );
    }

    public function users(Request $request)
    {
        $query = User::with('umkm')
            ->where('role', 'user')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }
}