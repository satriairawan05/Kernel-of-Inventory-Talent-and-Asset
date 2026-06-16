@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color:#fff; box-shadow: 0 18px 40px rgba(15,23,42,.18); }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-card .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .form-label { font-weight: 600; color: #334155; }
    .form-control { border-radius: 14px; border-color: #cbd5e1; padding: .72rem .9rem; }
    .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 .18rem rgba(37,99,235,.15); }
    .form-check-input { cursor: pointer; width: 1.2em; height: 1.2em; }
    .action-bar { border-top: 1px solid rgba(148,163,184,.18); margin-top: 1rem; padding-top: 1rem; }
    @media (max-width: 768px) { .page-hero { padding: 18px; } .action-bar .btn { width:100%; } .action-bar { flex-direction:column-reverse; } }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h2 class="mb-1 text-white">Add New Role</h2>
        </div>
        <span class="badge bg-white text-primary fs-6 px-3 py-2"><i class="fas fa-user-shield me-1"></i> New Role</span>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header">
            <h4 class="mb-1">Role & Permissions Configuration</h4>
        </div>
        <div class="card-body">
            {{-- Panggil form partial tanpa formMethod karena default-nya POST --}}
            @include('admin.setting.role._form', [
                'formAction' => route('setting.role.store')
            ])
        </div>
    </div>
</div>
@endsection