@extends('admin.layouts.app')

@push('css')
<style>
    .hr-hero {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 2rem;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(245, 158, 11, 0.2);
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
        color: #d97706;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .hr-card {
        border: none;
        border-radius: 1.5rem;
        transition: all 0.25s ease;
        background: white;
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        border: 1px solid #fef9e3;
        height: 100%;
        overflow: hidden;
    }
    .hr-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 30px -12px rgba(0,0,0,0.1);
        border-color: #fcd34d;
    }
    .card-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #fffbeb;
        color: #d97706;
        font-size: 1.6rem;
    }
    .badge-subtle {
        background: #fef3c7;
        color: #b45309;
        border-radius: 30px;
        padding: 0.25rem 0.8rem;
        font-size: 0.7rem;
    }
    .submenu-list {
        list-style: none;
        padding-left: 0;
        margin-top: 0.75rem;
    }
    .submenu-list li {
        margin-bottom: 0.5rem;
    }
    .submenu-list a {
        text-decoration: none;
        font-size: 0.8rem;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: 0.2s;
    }
    .submenu-list a:hover {
        color: #d97706;
        transform: translateX(3px);
    }
    .info-row {
        border-top: 1px solid #fef3c7;
        margin-top: 0.8rem;
        padding-top: 0.8rem;
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Hero Section : Sambutan Hangat -->
    <div class="hr-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-4">
            <div class="greeting-icon">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1" style="color: #78350f;">Human Resources</h2>
                <p class="mb-0 text-secondary">Halo, <strong>{{ auth()->user()->name }}</strong>! Kelola talenta, kehadiran, dan kesejahteraan karyawan.</p>
                <div class="mt-2 small text-muted">
                    <i class="fas fa-user-tie me-1"></i> KITA bantu ciptakan lingkungan kerja yang produktif.
                </div>
            </div>
        </div>
        <div class="text-muted">
            <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Statistik Cepat (contoh, bisa diganti data real) -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6">
            <div class="card hr-card p-3 text-center">
                <h6 class="text-muted">Total Karyawan</h6>
                <h3 class="fw-bold text-warning">124</h3>
                <small class="text-muted">+5 bulan ini</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card hr-card p-3 text-center">
                <h6 class="text-muted">Hadir Hari Ini</h6>
                <h3 class="fw-bold text-success">98</h3>
                <small class="text-muted">dari 124</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card hr-card p-3 text-center">
                <h6 class="text-muted">Izin / Sakit</h6>
                <h3 class="fw-bold text-danger">12</h3>
                <small class="text-muted">hari ini</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card hr-card p-3 text-center">
                <h6 class="text-muted">Lowongan Aktif</h6>
                <h3 class="fw-bold text-primary">6</h3>
                <small class="text-muted">posisi</small>
            </div>
        </div>
    </div>

    <!-- Menu HR Groups -->
    <div class="row g-4">
        <!-- Presences (Absensi) -->
        <div class="col-xl-4 col-md-6">
            <div class="card hr-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="card-icon mb-2"><i class="fas fa-fingerprint"></i></div>
                        <span class="badge-subtle">Kehadiran</span>
                    </div>
                    <h5 class="fw-bold">Presences</h5>
                    <p class="small text-secondary">Rekam absensi, izin, cuti, dan lembur karyawan.</p>
                    <div class="info-row">
                        <a href="#" class="small text-warning fw-semibold">Kelola Kehadiran <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-xl-4 col-md-6">
            <div class="card hr-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="card-icon mb-2"><i class="fas fa-chart-line"></i></div>
                        <span class="badge-subtle">Laporan</span>
                    </div>
                    <h5 class="fw-bold">Reports</h5>
                    <p class="small text-secondary">Laporan kehadiran, payroll, turnover, dan rekap karyawan.</p>
                    <div class="info-row">
                        <a href="#" class="small text-warning fw-semibold">Lihat Laporan <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SOP -->
        <div class="col-xl-4 col-md-6">
            <div class="card hr-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="card-icon mb-2"><i class="fas fa-file-alt"></i></div>
                        <span class="badge-subtle">Standar</span>
                    </div>
                    <h5 class="fw-bold">SOP</h5>
                    <p class="small text-secondary">Kelola prosedur operasional standar dan kebijakan perusahaan.</p>
                    <div class="info-row">
                        <a href="#" class="small text-warning fw-semibold">Kelola SOP <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll -->
        <div class="col-xl-4 col-md-6">
            <div class="card hr-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="card-icon mb-2"><i class="fas fa-coins"></i></div>
                        <span class="badge-subtle">Penggajian</span>
                    </div>
                    <h5 class="fw-bold">Payroll</h5>
                    <p class="small text-secondary">Hitung gaji, potongan, bonus, dan slip gaji karyawan.</p>
                    <div class="info-row">
                        <a href="#" class="small text-warning fw-semibold">Proses Payroll <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recruitments -->
        <div class="col-xl-4 col-md-6">
            <div class="card hr-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="card-icon mb-2"><i class="fas fa-user-plus"></i></div>
                        <span class="badge-subtle">Rekrutmen</span>
                    </div>
                    <h5 class="fw-bold">Recruitments</h5>
                    <p class="small text-secondary">Proses rekrutmen dari karir, interview hingga onboarding.</p>
                    <ul class="submenu-list">
                        <li><a href="#"><i class="fas fa-briefcase fa-fw"></i> Career</a></li>
                        <li><a href="#"><i class="fas fa-comments fa-fw"></i> Interview</a></li>
                        <li><a href="#"><i class="fas fa-user-check fa-fw"></i> Employee</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Motivasi KITA -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-light border rounded-4 shadow-sm d-flex align-items-center gap-3" style="background: #ffffff;">
                <i class="fas fa-heart text-danger fa-2x"></i>
                <div>
                    <strong class="d-block">💡 KITA — Kernel of Inventory, Talent & Asset</strong>
                    <span class="small text-secondary">Karyawan adalah aset terpenting. Kelola kehadiran, rekrutmen, dan kesejahteraan mereka dengan baik. KITA siap membantu transformasi SDM Anda!</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection