@extends('admin.layouts.app')

@push('css')
<style>
    .open-page {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8faff 0%, #eef4ff 100%);
    }
    .open-card {
        max-width: 480px;
        width: 100%;
        border: none;
        border-radius: 28px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }
    .open-card .card-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        padding: 28px 32px;
        border-bottom: none;
        text-align: center;
    }
    .open-card .card-header .icon-wrapper {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.10);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .open-card .card-header .icon-wrapper i {
        font-size: 36px;
        color: #ffd700;
    }
    .open-card .card-header h3 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .open-card .card-header p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        margin: 0;
    }
    .open-card .card-body {
        padding: 32px 32px 40px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f4ff;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-row .label {
        color: #64748b;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .info-row .value {
        color: #1e293b;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .info-row .value.highlight {
        color: #0f3460;
        font-size: 1.1rem;
        font-weight: 700;
    }
    .btn-open {
        background: linear-gradient(145deg, #0f3460, #1a4a7a);
        border: none;
        color: #fff;
        font-weight: 700;
        padding: 14px 32px;
        border-radius: 60px;
        font-size: 1.05rem;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 28px rgba(15, 52, 96, 0.35);
        width: 100%;
    }
    .btn-open:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(15, 52, 96, 0.45);
        background: linear-gradient(145deg, #1a4a7a, #0f3460);
        color: #fff;
    }
    .btn-open:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .btn-open .spinner {
        display: none;
    }
    .btn-open.loading .spinner {
        display: inline-block;
    }
    .btn-open.loading .btn-text {
        display: none;
    }
    .alert-message {
        margin-top: 16px;
        border-radius: 12px;
    }
</style>
@endpush

@section('content')
<div class="open-page">
    <div class="open-card card">
        <div class="card-header">
            <div class="icon-wrapper">
                <i class="fas fa-cash-register"></i>
            </div>
            <h3>Open Cashier</h3>
            <p>Start your shift by opening the cashier session</p>
        </div>
        <div class="card-body">
            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show alert-message" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('failed') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show alert-message" role="alert">
                    <i class="fas fa-info-circle me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="info-row">
                <span class="label"><i class="fas fa-store me-2"></i>Outlet</span>
                <span class="value">{{ auth()->user()->company->company_name }}</span>
            </div>
            <div class="info-row">
                <span class="label"><i class="fas fa-user me-2"></i>Cashier</span>
                <span class="value">{{ auth()->user()->name }}</span>
            </div>
            <div class="info-row">
                <span class="label"><i class="fas fa-money-bill-wave me-2"></i>Opening Balance</span>
                <span class="value highlight">Rp {{ number_format($openingBalance, 0, ',', '.') }}</span>
            </div>

            <hr class="my-4">

            <form action="{{ route('pos.open.store') }}" method="POST" id="openForm">
                @csrf
                <button type="submit" class="btn-open" id="btnOpen">
                    <span class="spinner spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    <span class="btn-text"><i class="fas fa-unlock me-2"></i> Open Cashier</span>
                </button>
            </form>

            <p class="text-muted text-center mt-3 mb-0 small">
                <i class="fas fa-info-circle me-1"></i>
                Opening balance is configured in system settings per outlet.
            </p>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('openForm');
        const btn = document.getElementById('btnOpen');

        form.addEventListener('submit', function() {
            btn.classList.add('loading');
            btn.disabled = true;
        });
    });
</script>
@endpush
@endsection