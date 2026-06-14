@extends('admin.layouts.app')

@push('css')
    <style>
        .setting-hero {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border-radius: 2rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(37, 99, 235, 0.15);
        }

        .greeting-icon {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #2563eb;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .setting-card {
            border: none;
            border-radius: 1.5rem;
            transition: all 0.25s ease;
            background: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #eef2ff;
            height: 100%;
        }

        .setting-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.1);
            border-color: #cbdffc;
        }

        .card-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 1.6rem;
        }

        .badge-subtle {
            background: #eef2ff;
            color: #1e40af;
            border-radius: 30px;
            padding: 0.25rem 0.8rem;
            font-size: 0.7rem;
        }

        .info-row {
            border-top: 1px solid #f0f4fe;
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-3">
        <!-- Hero Section : Sambutan Hangat -->
        <div class="setting-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-4">
                <div class="greeting-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1" style="color: #0f172a;">System Settings</h2>
                    <p class="mb-0 text-secondary">Halo, <strong>{{ auth()->user()->name }}</strong>! Kelola fondasi
                        <strong>KITA</strong> dari sini.</p>
                    <div class="mt-2 small text-muted">
                        <i class="fas fa-cog me-1"></i> Atur outlet, shift, unit, roles, dan akun pengguna.
                    </div>
                </div>
            </div>
            <div class="text-muted">
                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <!-- Menu System Settings (5 menu) -->
        <div class="row g-4">
            <!-- Outlets -->
            <div class="col-xl-4 col-md-6">
                <div class="card setting-card p-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-icon"><i class="fas fa-store"></i></div>
                            <span class="badge-subtle">Perusahaan</span>
                        </div>
                        <h5 class="fw-bold mt-3 mb-2">Outlets</h5>
                        <p class="small text-secondary">Kelola data cabang/outlet, alamat, kontak, logo, dan tipe bisnis
                            (Rumah Makan, Counter HP, dll).</p>
                        <div class="info-row d-flex justify-content-between">
                            <span><i class="fas fa-check-circle text-success me-1"></i> Aktif</span>
                            <a href="{{ route('setting.company.index') }}" class="text-primary fw-semibold small">Kelola <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shift -->
            <div class="col-xl-4 col-md-6">
                <div class="card setting-card p-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-icon"><i class="fas fa-clock"></i></div>
                            <span class="badge-subtle">Jam Kerja</span>
                        </div>
                        <h5 class="fw-bold mt-3 mb-2">Shift</h5>
                        <p class="small text-secondary">Atur jadwal shift kerja, jam masuk, jam pulang, dan hari libur untuk
                            setiap outlet.</p>
                        <div class="info-row d-flex justify-content-between">
                            <span><i class="fas fa-calendar-week me-1"></i> Fleksibel</span>
                            <a href="{{ route('setting.shift.index') }}" class="text-primary fw-semibold small">Kelola <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unit -->
            <div class="col-xl-4 col-md-6">
                <div class="card setting-card p-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-icon"><i class="fas fa-ruler-combined"></i></div>
                            <span class="badge-subtle">Satuan</span>
                        </div>
                        <h5 class="fw-bold mt-3 mb-2">Unit</h5>
                        <p class="small text-secondary">Buat satuan barang (pcs, kg, liter, box, dll) yang akan digunakan di
                            modul Inventory.</p>
                        <div class="info-row d-flex justify-content-between">
                            <span><i class="fas fa-boxes me-1"></i> Konsistensi stok</span>
                            <a href="{{ route('setting.unit.index') }}" class="text-primary fw-semibold small">Kelola <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles -->
            <div class="col-xl-4 col-md-6">
                <div class="card setting-card p-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-icon"><i class="fas fa-user-cog"></i></div>
                            <span class="badge-subtle">Hak Akses</span>
                        </div>
                        <h5 class="fw-bold mt-3 mb-2">Roles</h5>
                        <p class="small text-secondary">Buat peran (Admin, Kasir, Manajer, HRD) dan atur izin akses ke
                            setiap modul KITA.</p>
                        <div class="info-row d-flex justify-content-between">
                            <span><i class="fas fa-shield-alt me-1"></i> RBAC</span>
                            <a href="#" class="text-primary fw-semibold small">Kelola <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accounts -->
            <div class="col-xl-4 col-md-6">
                <div class="card setting-card p-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-icon"><i class="fas fa-user-circle"></i></div>
                            <span class="badge-subtle">Pengguna</span>
                        </div>
                        <h5 class="fw-bold mt-3 mb-2">Accounts</h5>
                        <p class="small text-secondary">Kelola akun pengguna, reset password, aktivasi/nonaktifkan user, dan
                            tautkan ke role.</p>
                        <div class="info-row d-flex justify-content-between">
                            <span><i class="fas fa-users me-1"></i> Semua user</span>
                            <a href="{{ route('setting.account.index') }}" class="text-primary fw-semibold small">Kelola <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pesan Motivasi KITA -->
        <div class="row mt-4">
            <div class="col-12">    
                <div class="alert alert-light border rounded-4 shadow-sm d-flex align-items-center gap-3"
                    style="background: #ffffff;">
                    <i class="fas fa-heart text-danger fa-2x"></i>
                    <div>
                        <strong class="d-block">💡 KITA — Kernel of Inventory, Talent & Asset</strong>
                        <span class="small text-secondary">Pengaturan adalah fondasi kesuksesan operasional. Pastikan
                            Outlet, Shift, Unit, Roles, dan Accounts sudah sesuai dengan kebutuhan bisnis Anda.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
