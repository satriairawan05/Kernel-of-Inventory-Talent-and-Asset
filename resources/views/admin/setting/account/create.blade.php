@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #38bdf8 100%); color:#fff; box-shadow: 0 18px 40px rgba(15,23,42,.18); }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-card .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .form-label { font-weight: 600; color: #334155; }
    .form-control, .form-select { border-radius: 14px; border-color: #cbd5e1; padding: .72rem .9rem; }
    .form-control:focus, .form-select:focus { border-color: #2563eb; box-shadow: 0 0 0 .18rem rgba(37,99,235,.15); }
    .input-group-text { border-radius: 0 14px 14px 0; }
    .password-toggle { cursor: pointer; }
    .action-bar { border-top: 1px solid rgba(148,163,184,.18); margin-top: 1rem; padding-top: 1rem; }
    @media (max-width: 768px) { .page-hero { padding: 18px; } .action-bar .btn { width: 100%; } .action-bar { flex-direction: column-reverse; } }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.password-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('.fa-eye, .fa-eye-slash');
                if (!input || !icon) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
</script>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Settings</p>
            <h2 class="mb-1">Add New Account</h2>
        </div>
        <span class="badge bg-white text-primary fs-6 px-3 py-2"><i class="fas fa-user-plus me-1"></i> New account</span>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header">
            <h4 class="mb-1">Account Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('setting.account.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Nazril Ahmad">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="nazril@aam-group.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" placeholder="********">
                            <span class="input-group-text password-toggle"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="********">
                            <span class="input-group-text password-toggle"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 action-bar">
                    <a href="{{ route('setting.account.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
