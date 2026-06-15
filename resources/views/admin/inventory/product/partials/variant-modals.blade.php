@foreach ($product->variants as $variant)
    <div class="modal fade" id="editVariantModal{{ $variant->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Variant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('inventory.product.product-variant.update', [$product, $variant]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="variant_id" value="{{ $variant->id }}">

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Variant Name</label>
                            <input type="text" name="variant_name"
                                class="form-control @error('variant_name') is-invalid @enderror"
                                value="{{ old('variant_name', $variant->variant_name) }}">
                            @error('variant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Variant Code</label>
                            <input type="text" name="variant_code"
                                class="form-control @error('variant_code') is-invalid @enderror"
                                value="{{ old('variant_code', $variant->variant_code) }}">
                            @error('variant_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Variant Image</label>
                            <input type="file" name="image"
                                class="form-control @error('image') is-invalid @enderror"
                                accept="image/*"
                                onchange="previewImage(this, 'edit_image_preview_{{ $variant->id }}')">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>

                            <div class="image-preview-container text-start mt-3">
                                @php
                                    $imageSrc = $variant->image ? asset('storage/' . $variant->image) : '';
                                @endphp
                                <img id="edit_image_preview_{{ $variant->id }}"
                                    src="{{ $imageSrc }}"
                                    data-default-src="{{ $imageSrc }}"
                                    alt="Preview"
                                    style="{{ $imageSrc ? 'display:inline-block;max-height:100px;' : 'display:none;' }}">
                            </div>

                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Purchase Price</label>
                            <input type="text" name="purchase_price"
                                class="form-control price-format @error('purchase_price') is-invalid @enderror"
                                value="{{ old('purchase_price', $variant->purchase_price) }}"
                                data-raw="{{ old('purchase_price', $variant->purchase_price) }}">
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Selling Price</label>
                            <input type="text" name="selling_price"
                                class="form-control price-format @error('selling_price') is-invalid @enderror"
                                value="{{ old('selling_price', $variant->selling_price) }}"
                                data-raw="{{ old('selling_price', $variant->selling_price) }}">
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                name="is_active"
                                id="edit_is_active_{{ $variant->id }}"
                                value="1"
                                {{ old('is_active', $variant->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold"
                                for="edit_is_active_{{ $variant->id }}">Active Status</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteVariantModal{{ $variant->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Variant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center py-4">
                    <p class="mb-0">Yakin ingin menghapus variant <strong>{{ $variant->variant_name }}</strong>?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('inventory.product.product-variant.destroy', [$product, $variant]) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach