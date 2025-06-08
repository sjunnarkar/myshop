@php
    $isEdit = isset($coupon);
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Coupon Details</h5>

                <div class="mb-3">
                    <label for="code" class="form-label">Coupon Code</label>
                    <input type="text" 
                           class="form-control @error('code') is-invalid @enderror" 
                           id="code" 
                           name="code"
                           value="{{ old('code', $coupon->code ?? '') }}"
                           required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="2">{{ old('description', $coupon->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Discount Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected' : '' }}>
                                    Percentage Discount
                                </option>
                                <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>
                                    Fixed Amount Discount
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="value" class="form-label">Discount Value</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('value') is-invalid @enderror" 
                                       id="value" 
                                       name="value"
                                       value="{{ old('value', $coupon->value ?? '') }}"
                                       step="0.01"
                                       min="0"
                                       required>
                                <span class="input-group-text" id="value-addon">
                                    <span class="percentage-symbol">%</span>
                                    <span class="fixed-symbol" style="display: none;">$</span>
                                </span>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="minimum_spend" class="form-label">Minimum Spend (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control @error('minimum_spend') is-invalid @enderror" 
                                       id="minimum_spend" 
                                       name="minimum_spend"
                                       value="{{ old('minimum_spend', $coupon->minimum_spend ?? '') }}"
                                       step="0.01"
                                       min="0">
                                @error('minimum_spend')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="maximum_discount" class="form-label">Maximum Discount (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control @error('maximum_discount') is-invalid @enderror" 
                                       id="maximum_discount" 
                                       name="maximum_discount"
                                       value="{{ old('maximum_discount', $coupon->maximum_discount ?? '') }}"
                                       step="0.01"
                                       min="0">
                                @error('maximum_discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="starts_at" class="form-label">Start Date (Optional)</label>
                            <input type="datetime-local" 
                                   class="form-control @error('starts_at') is-invalid @enderror" 
                                   id="starts_at" 
                                   name="starts_at"
                                   value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expiry Date (Optional)</label>
                            <input type="datetime-local" 
                                   class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" 
                                   name="expires_at"
                                   value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Usage Restrictions</h5>

                <div class="mb-3">
                    <label for="usage_limit" class="form-label">Usage Limit (Optional)</label>
                    <input type="number" 
                           class="form-control @error('usage_limit') is-invalid @enderror" 
                           id="usage_limit" 
                           name="usage_limit"
                           value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                           min="1">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Leave empty for unlimited uses</div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="first_order_only"
                               name="first_order_only" 
                               value="1"
                               {{ old('first_order_only', $coupon->first_order_only ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="first_order_only">
                            First Order Only
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Applicable Products (Optional)</label>
                    <select class="form-select @error('applicable_products') is-invalid @enderror" 
                            name="applicable_products[]" 
                            multiple 
                            data-placeholder="Select products">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ in_array($product->id, old('applicable_products', $coupon->applicable_products ?? [])) ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('applicable_products')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Leave empty to apply to all products</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Applicable Categories (Optional)</label>
                    <select class="form-select @error('applicable_categories') is-invalid @enderror" 
                            name="applicable_categories[]" 
                            multiple 
                            data-placeholder="Select categories">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ in_array($category->id, old('applicable_categories', $coupon->applicable_categories ?? [])) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('applicable_categories')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Leave empty to apply to all categories</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Status</h5>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active"
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? 'Update Coupon' : 'Create Coupon' }}
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        @if($isEdit)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Usage Statistics</h5>
                    <div class="mb-3">
                        <label class="form-label">Total Uses</label>
                        <div class="h4">{{ $coupon->used_count }}</div>
                    </div>
                    @if($coupon->usage_limit)
                        <div class="mb-3">
                            <label class="form-label">Remaining Uses</label>
                            <div class="h4">{{ max(0, $coupon->usage_limit - $coupon->used_count) }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const percentageSymbol = document.querySelector('.percentage-symbol');
        const fixedSymbol = document.querySelector('.fixed-symbol');
        const valueInput = document.getElementById('value');

        function updateSymbols() {
            if (typeSelect.value === 'percentage') {
                percentageSymbol.style.display = 'inline';
                fixedSymbol.style.display = 'none';
                valueInput.setAttribute('max', '100');
            } else {
                percentageSymbol.style.display = 'none';
                fixedSymbol.style.display = 'inline';
                valueInput.removeAttribute('max');
            }
        }

        typeSelect.addEventListener('change', updateSymbols);
        updateSymbols();
    });
</script>
@endpush 