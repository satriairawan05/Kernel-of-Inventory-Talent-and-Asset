@extends('admin.layouts.app')

@section('content')
    <div class="card">

        <div class="card-header">
            <h4>Add New Category</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('inventory.category.store') }}" method="POST">
                @csrf
                <div class="row">

                    {{-- Company Relation --}}
                    <div class="col-md-4">

                        <div class="mb-3">

                            <label class="form-label" for="company_id">
                                Company
                            </label>

                            <select 
                                id="company_id" 
                                name="company_id" 
                                class="form-control @error('company_id') is-invalid @enderror">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }} {{-- Sesuaikan dengan nama kolom nama perusahaan Anda --}}
                                    </option>
                                @endforeach
                            </select>

                            @error('company_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    {{-- Category Name --}}
                    <div class="col-md-8">

                        <div class="mb-3">

                            <label class="form-label" for="category_name">
                                Category Name
                            </label>

                            <input
                                type="text"
                                id="category_name"
                                name="category_name"
                                value="{{ old('category_name') }}"
                                class="form-control @error('category_name') is-invalid @enderror"
                                placeholder="Example: Electronics, Furniture, etc.">

                            @error('category_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>

                {{-- Description --}}
                <div class="mb-4">

                    <label class="form-label" for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Example: Category for IT equipment and assets.">{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('inventory.category.index') }}"
                        class="btn btn-secondary">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Add Category
                    </button>

                </div>

            </form>

        </div>

    </div>
@endsection