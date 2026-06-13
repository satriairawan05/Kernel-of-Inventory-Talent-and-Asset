@extends('admin.layouts.app')

@push('css')
    <style>
        .form-icon {
            pointer-events: none;
        }
        
        .currency-input {
            position: relative;
        }
        
        .currency-input::before {
            content: "Rp";
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            color: #6c757d;
            font-weight: 500;
        }
        
        .currency-input input {
            padding-left: 40px;
        }
        
        .total-amount-display {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .form-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-section-title {
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto calculate total amount
            const pulsaInput = document.getElementById('pulsa_amount');
            const accessoriesInput = document.getElementById('accessories_amount');
            const serviceInput = document.getElementById('service_amount');
            const totalAmountDisplay = document.getElementById('total_amount_display');
            const totalAmountInput = document.getElementById('total_amount');

            function calculateTotal() {
                let pulsa = parseFloat(pulsaInput?.value) || 0;
                let accessories = parseFloat(accessoriesInput?.value) || 0;
                let service = parseFloat(serviceInput?.value) || 0;
                
                let total = pulsa + accessories + service;
                
                if (totalAmountDisplay) {
                    totalAmountDisplay.textContent = formatRupiah(total);
                }
                
                if (totalAmountInput) {
                    totalAmountInput.value = total.toFixed(2);
                }
            }

            function formatRupiah(angka) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
            }

            if (pulsaInput) pulsaInput.addEventListener('input', calculateTotal);
            if (accessoriesInput) accessoriesInput.addEventListener('input', calculateTotal);
            if (serviceInput) serviceInput.addEventListener('input', calculateTotal);

            calculateTotal();
        });
    </script>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Add New Sales Report</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('pos.report.create') }}" method="POST">
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
                        <small class="text-muted">Optional: Leave empty for general report</small>
                    </div>
                </div>

                <!-- Date Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-calendar-alt"></i> Date Information
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="report_date">
                                Report Date
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                                <input type="date" id="report_date" name="report_date" 
                                    value="{{ old('report_date', date('Y-m-d')) }}"
                                    class="form-control @error('report_date') is-invalid @enderror">
                            </div>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="arrived_date">
                                Arrived Date
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-truck"></i>
                                </span>
                                <input type="date" id="arrived_date" name="arrived_date" 
                                    value="{{ old('arrived_date') }}"
                                    class="form-control @error('arrived_date') is-invalid @enderror">
                            </div>
                            @error('arrived_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">When the goods arrived (optional)</small>
                        </div>
                    </div>
                </div>

                <!-- Amount Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-money-bill-wave"></i> Transaction Amounts
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="pulsa_amount">
                                <i class="fas fa-mobile-alt"></i> Pulsa Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" id="pulsa_amount" name="pulsa_amount" 
                                    value="{{ old('pulsa_amount', 0) }}"
                                    class="form-control currency-input @error('pulsa_amount') is-invalid @enderror" 
                                    placeholder="0.00">
                            </div>
                            @error('pulsa_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="accessories_amount">
                                <i class="fas fa-headphones"></i> Accessories Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" id="accessories_amount" name="accessories_amount" 
                                    value="{{ old('accessories_amount', 0) }}"
                                    class="form-control currency-input @error('accessories_amount') is-invalid @enderror" 
                                    placeholder="0.00">
                            </div>
                            @error('accessories_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="service_amount">
                                <i class="fas fa-tools"></i> Service Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" id="service_amount" name="service_amount" 
                                    value="{{ old('service_amount', 0) }}"
                                    class="form-control currency-input @error('service_amount') is-invalid @enderror" 
                                    placeholder="0.00">
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
                                <h5 class="mb-0">Total Amount:</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <h3 class="mb-0 text-primary" id="total_amount_display">
                                    Rp 0
                                </h3>
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

                    <div class="mb-3">
                        <label class="form-label" for="notes">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="4" 
                            class="form-control @error('notes') is-invalid @enderror" 
                            placeholder="Enter any additional notes or remarks...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Optional: Add any important information about this transaction</small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('pos.report.daily') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add New Report
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection