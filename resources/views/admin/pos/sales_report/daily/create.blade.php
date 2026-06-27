@extends('admin.layouts.app')

@push('css')
    <style>
        /* ===== FORM CARD ===== */
        .form-card {
            border-radius: 1.5rem;
            overflow: hidden;
            border: 1px solid #d1fae5;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .form-card .card-header {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #065f46;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #d1fae5;
        }

        .form-card .card-header h4 {
            font-weight: 700;
            margin: 0;
        }

        .form-card .card-body {
            padding: 2rem;
        }

        /* ===== FORM SECTIONS ===== */
        .form-section {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #d1fae5;
        }

        .form-section-title {
            font-weight: 600;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #d1fae5;
            color: #065f46;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: #10b981;
        }

        /* ===== FORM ELEMENTS ===== */
        .form-label {
            font-weight: 600;
            color: #065f46;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #d1fae5;
            padding: 0.6rem 1rem;
            background: #ffffff;
            color: #1e293b;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .input-group-text {
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            color: #065f46;
            font-weight: 600;
        }

        /* ===== TOTAL DISPLAY ===== */
        .total-amount-display {
            background: #ecfdf5;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            margin-top: 1.5rem;
            border: 1px solid #d1fae5;
        }

        .total-amount-display h5 {
            color: #065f46;
            font-weight: 600;
        }

        .total-amount-display h3 {
            font-weight: 700;
            color: #065f46;
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: #065f46 !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 0.6rem 2rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #047857 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(6, 95, 70, 0.25);
        }

        .btn-secondary {
            background: rgba(6, 95, 70, 0.12) !important;
            border: 1px solid #d1fae5 !important;
            border-radius: 30px !important;
            padding: 0.6rem 1.5rem !important;
            color: #065f46 !important;
            font-weight: 600 !important;
        }

        .btn-secondary:hover {
            background: rgba(6, 95, 70, 0.2) !important;
            color: #065f46 !important;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .form-card .card-body {
                padding: 1rem;
            }

            .form-section {
                padding: 1rem;
            }

            .total-amount-display {
                padding: 1rem;
            }

            .total-amount-display h3 {
                font-size: 1.4rem;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==================== FORMAT & PARSE RUPIAH ====================
            function formatRupiah(angka) {
                if (!angka && angka !== 0) return '';
                var clean = String(angka).replace(/\D/g, '');
                if (clean === '') return '';
                var number = parseInt(clean, 10);
                if (isNaN(number)) return '';
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function parseRupiah(value) {
                if (!value) return 0;
                var clean = String(value).replace(/\D/g, '');
                return parseInt(clean, 10) || 0;
            }

            // ==================== TERAPKAN KE INPUT ====================
            var rupiahInputs = document.querySelectorAll('.rupiah-input');

            rupiahInputs.forEach(function(input) {
                // Set nilai awal jika ada value dari old()
                var initialValue = input.getAttribute('data-initial');
                if (initialValue) {
                    input.value = formatRupiah(initialValue);
                }

                // Event input untuk memformat saat mengetik
                input.addEventListener('input', function(e) {
                    var val = this.value;
                    var formatted = formatRupiah(val);
                    this.value = formatted;
                    calculateTotal();
                });

                // Saat blur, pastikan format tetap rapi
                input.addEventListener('blur', function(e) {
                    var val = this.value;
                    if (val) {
                        this.value = formatRupiah(val);
                    }
                });
            });

            // ==================== PERHITUNGAN TOTAL ====================
            var pulsaInput = document.getElementById('pulsa_amount');
            var accessoriesInput = document.getElementById('accessories_amount');
            var serviceInput = document.getElementById('service_amount');
            var totalAmountDisplay = document.getElementById('total_amount_display');
            var totalAmountInput = document.getElementById('total_amount');

            function calculateTotal() {
                var pulsa = parseRupiah(pulsaInput ? pulsaInput.value : '');
                var accessories = parseRupiah(accessoriesInput ? accessoriesInput.value : '');
                var service = parseRupiah(serviceInput ? serviceInput.value : '');

                var total = pulsa + accessories + service;

                if (totalAmountDisplay) {
                    totalAmountDisplay.textContent = 'Rp ' + formatRupiah(total);
                }

                if (totalAmountInput) {
                    totalAmountInput.value = total;
                }
            }

            // ==================== SEBELUM SUBMIT ====================
            var form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Ubah nilai input yang diformat menjadi angka murni
                    var amountInputs = document.querySelectorAll('.rupiah-input');
                    amountInputs.forEach(function(input) {
                        var raw = parseRupiah(input.value);
                        input.value = raw;
                    });
                });
            }

            // ==================== INITIAL ====================
            calculateTotal();
        });
    </script>
@endpush

@section('content')
    <div class="card form-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-plus-circle me-2"></i> Add New Sales Report</h4>
            <span class="badge bg-light text-dark">New</span>
        </div>

        <div class="card-body">
            <form action="{{ route('pos.report.store') }}" method="POST">
                @csrf

                <!-- Company Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-building"></i> Company Information
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="company_id">
                            Company <span class="text-danger">*</span>
                        </label>
                        <select name="company_id" id="company_id"
                            class="form-select @error('company_id') is-invalid @enderror">
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Optional: Leave empty for general report</small>
                    </div>
                </div>

                <!-- Date Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-calendar-alt"></i> Date Information
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="report_date">
                                <i class="fas fa-calendar-day me-1"></i> Report Date
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                <input type="date" id="report_date" name="report_date"
                                    value="{{ old('report_date', date('Y-m-d')) }}"
                                    class="form-control @error('report_date') is-invalid @enderror">
                            </div>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="arrived_date">
                                <i class="fas fa-truck me-1"></i> Arrived Date
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-truck"></i></span>
                                <input type="date" id="arrived_date" name="arrived_date"
                                    value="{{ old('arrived_date') }}"
                                    class="form-control @error('arrived_date') is-invalid @enderror">
                            </div>
                            @error('arrived_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>When the goods arrived (optional)</small>
                        </div>
                    </div>
                </div>

                <!-- Amount Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-money-bill-wave"></i> Transaction Amounts
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="pulsa_amount">
                                <i class="fas fa-mobile-alt me-1"></i> Pulsa Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="pulsa_amount" name="pulsa_amount"
                                    class="form-control rupiah-input @error('pulsa_amount') is-invalid @enderror"
                                    placeholder="0"
                                    data-initial="{{ old('pulsa_amount') }}">
                            </div>
                            @error('pulsa_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="accessories_amount">
                                <i class="fas fa-headphones me-1"></i> Accessories Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="accessories_amount" name="accessories_amount"
                                    class="form-control rupiah-input @error('accessories_amount') is-invalid @enderror"
                                    placeholder="0"
                                    data-initial="{{ old('accessories_amount') }}">
                            </div>
                            @error('accessories_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="service_amount">
                                <i class="fas fa-tools me-1"></i> Service Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="service_amount" name="service_amount"
                                    class="form-control rupiah-input @error('service_amount') is-invalid @enderror"
                                    placeholder="0"
                                    data-initial="{{ old('service_amount') }}">
                            </div>
                            @error('service_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Total Amount Display -->
                    <div class="total-amount-display">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Total Amount</h5>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h3 class="mb-0" id="total_amount_display">Rp 0</h3>
                                <input type="hidden" name="total_amount" id="total_amount" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-sticky-note"></i> Additional Information
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="notes">
                            Notes <span class="text-muted">(optional)</span>
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Enter any additional notes or remarks...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Add any important information about this transaction</small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('pos.report.daily') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Add New Report
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection