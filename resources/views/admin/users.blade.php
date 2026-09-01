@extends('layouts.admin')

@section('content')
    <div class="admin-heading">
        <span class="admin-eyebrow">PENGGUNA</span>
        <h1>Pengguna <em>UMKMKita.</em></h1>
        <p>Lihat akun pengguna dan UMKM yang mereka miliki.</p>
    </div>

    <form method="GET" action="{{ route('admin.users') }}" class="admin-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email...">
        <button type="submit">Cari</button>
    </form>

    <section class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>UMKM</th>
                        <th>Bergabung</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>

                            <td>
                                @if($user->umkm)
                                    {{ $user->umkm->name }}
                                @else
                                    <span class="muted">Belum punya UMKM</span>
                                @endif
                            </td>

                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-table">User tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $users->links() }}
        </div>
    </section>
@endsection