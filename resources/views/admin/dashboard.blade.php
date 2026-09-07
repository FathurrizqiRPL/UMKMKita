@extends('layouts.admin')

@section('content')
    <div class="admin-heading">
        <span class="admin-eyebrow">DASHBOARD ADMIN</span>

        <h1>Ringkasan <em>UMKMKita.</em></h1>

        <p>Pantau pengguna, UMKM, serta produk dan layanan yang terdaftar di platform.</p>
    </div>

    <div class="admin-stats">
        <div class="admin-stat">
            <span>TOTAL USER</span>
            <strong>{{ $totalUsers }}</strong>
            <small>Pengguna terdaftar</small>
        </div>

        <div class="admin-stat">
            <span>TOTAL UMKM</span>
            <strong>{{ $totalUmkms }}</strong>
            <small>{{ $activeUmkms }} aktif</small>
        </div>

        <div class="admin-stat">
            <span>PRODUK / LAYANAN</span>
            <strong>{{ $totalItems }}</strong>
            <small>Konten dari seluruh UMKM</small>
        </div>

        <div class="admin-stat">
            <span>DINONAKTIFKAN</span>
            <strong>{{ $suspendedUmkms }}</strong>
            <small>UMKM disuspend</small>
        </div>
    </div>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">TERBARU</span>
                <h2>UMKM yang baru bergabung</h2>
            </div>

            <a href="{{ route('admin.umkms') }}">Lihat Semua →</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>UMKM</th>
                        <th>Pemilik</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($latestUmkms as $umkm)
                        <tr>
                            <td><strong>{{ $umkm->name }}</strong></td>
                            <td>{{ $umkm->user->name }}</td>
                            <td>{{ $umkm->category }}</td>
                            <td>
                                <span class="status {{ $umkm->status }}">{{ ucfirst($umkm->status) }}</span>
                            </td>
                            <td>{{ $umkm->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-table">Belum ada UMKM.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection