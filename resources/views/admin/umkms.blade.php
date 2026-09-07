@extends('layouts.admin')

@section('content')
    <div class="admin-heading">
        <span class="admin-eyebrow">MANAJEMEN UMKM</span>
        <h1>Kelola <em>UMKM.</em></h1>
        <p>Lihat dan moderasi UMKM yang terdaftar di platform.</p>
    </div>

    <form method="GET" action="{{ route('admin.umkms') }}" class="admin-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari UMKM, kategori, pemilik...">

        <select name="status">
            <option value="">Semua status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
        </select>

        <button type="submit">Cari</button>
    </form>

    <section class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>UMKM</th>
                        <th>Pemilik</th>
                        <th>Kategori</th>
                        <th>Item</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($umkms as $umkm)
                        <tr>
                            <td>
                                <strong>{{ $umkm->name }}</strong>
                                <small>{{ $umkm->slug }}</small>
                            </td>

                            <td>
                                {{ $umkm->user->name }}
                                <small>{{ $umkm->user->email }}</small>
                            </td>

                            <td>{{ $umkm->category }}</td>
                            <td>{{ $umkm->items_count }}</td>

                            <td>
                                <span class="status {{ $umkm->status }}">{{ ucfirst($umkm->status) }}</span>
                            </td>

                            <td>
                                <div class="table-actions">
                                    @if($umkm->status === 'active')
                                        <a href="{{ route('umkm.show', $umkm->slug) }}" target="_blank">Lihat ↗</a>
                                    @endif

                                    <form method="POST" action="{{ route('admin.umkms.status', $umkm) }}">
                                        @csrf
                                        @method('PATCH')

                                        @if($umkm->status === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="danger-action">Suspend</button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="success-action">Aktifkan</button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-table">UMKM tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $umkms->links() }}
        </div>
    </section>
@endsection