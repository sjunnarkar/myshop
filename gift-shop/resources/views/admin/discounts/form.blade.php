@php
    $isEdit = isset($discount);
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Discount Details</h5>

                <div class="mb-3">
                    <label for="name" class="form-label">Discount Name</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name"
                           value="{{ old('name', $discount->name ?? '') }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="2">{{ old('description', $discount->description ?? '') }}</textarea>
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
                                <option value="percentage" {{ old('type', $discount->type ?? '') === 'percentage' ? 'selected' : '' }}>
                                    Percentage Discount
                                </option>
                                <option value="fixed" {{ old('type', $discount->type ?? '') === 'fixed' ? 'selected' : '' }}>
                                    Fixed Amount Discount
                                </option>
                                <option value="buy_x_get_y" {{ old('type', $discount->type ?? '') === 'buy_x_get_y' ? 'selected' : '' }}>
                                    Buy X Get Y Free
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 value-input">
                            <label for="value" class="form-label">Discount Value</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('value') is-invalid @enderror" 
                                       id="value" 
                                       name="value"
                                       value="{{ old('value', $discount->value ?? '') }}"
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
                        <div class="mb-3 buy-x-get-y-inputs" style="display: none;">
                            <div class="row">
                                <div class="col">
                                    <label for="buy_x" class="form-label">Buy X</label>
                                    <input type="number" 
                                           class="form-control @error('buy_x') is-invalid @enderror" 
                                           id="buy_x" 
                                           name="buy_x"
                                           value="{{ old('buy_x', $discount->buy_x ?? '') }}"
                                           min="1">
                                    @error('buy_x')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="get_y" class="form-label">Get Y Free</label>
                                    <input type="number" 
                                           class="form-control @error('get_y') is-invalid @enderror" 
                                           id="get_y" 
                                           name="get_y"
                                           value="{{ old('get_y', $discount->get_y ?? '') }}"
                                           min="1">
                                    @error('get_y')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                       value="{{ old('minimum_spend', $discount->minimum_spend ?? '') }}"
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
                                       value="{{ old('maximum_discount', $discount->maximum_discount ?? '') }}"
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
                                   value="{{ old('starts_at', $discount->starts_at ? $discount->starts_at->format('Y-m-d\TH:i') : '') }}">
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
                                   value="{{ old('expires_at', $discount->expires_at ? $discount->expires_at->format('Y-m-d\TH:i') : '') }}">
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
                    <label for="usage_limit_per_user" class="form-label">Usage Limit Per User (Optional)</label>
                    <input type="number" 
                           class="form-control @error('usage_limit_per_user') is-invalid @enderror" 
                           id="usage_limit_per_user" 
                           name="usage_limit_per_user"
                           value="{{ old('usage_limit_per_user', $discount->usage_limit_per_user ?? '') }}"
                           min="1">
                    @error('usage_limit_per_user')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Leave empty for unlimited uses per user</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Applicable Products (Optional)</label>
                    <select class="form-select @error('applicable_products') is-invalid @enderror" 
                            name="applicable_products[]" 
                            multiple 
                            data-placeholder="Select products">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ in_array($product->id, old('applicable_products', $discount->applicable_products ?? [])) ? 'selected' : '' }}>
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
                                {{ in_array($category->id, old('applicable_categories', $discount->applicable_categories ?? [])) ? 'selected' : '' }}>
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
                <h5 class="card-title mb-4">Settings</h5>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active"
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $discount->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="stackable"
                               name="stackable" 
                               value="1"
                               {{ old('stackable', $discount->stackable ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="stackable">
                            Stackable with Other Discounts
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="priority" class="form-label">Priority</label>
                    <input type="number" 
                           class="form-control @error('priority') is-invalid @enderror" 
                           id="priority" 
                           name="priority"
                           value="{{ old('priority', $discount->priority ?? 0) }}"
                           min="0">
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Higher priority discounts are applied first</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? 'Update Discount' : 'Create Discount' }}
                    </button>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const valueInput = document.querySelector('.value-input');
        const buyXGetYInputs = document.querySelector('.buy-x-get-y-inputs');
        const percentageSymbol = document.querySelector('.percentage-symbol');
        const fixedSymbol = document.querySelector('.fixed-symbol');
        const valueNumberInput = document.getElementById('value');

        function updateInputs() {
            if (typeSelect.value === 'buy_x_get_y') {
                valueInput.style.display = 'none';
                buyXGetYInputs.style.display = 'block';
                valueNumberInput.value = '100';
            } else {
                valueInput.style.display = 'block';
                buyXGetYInputs.style.display = 'none';
                
                if (typeSelect.value === 'percentage') {
                    percentageSymbol.style.display = 'inline';
                    fixedSymbol.style.display = 'none';
                    valueNumberInput.setAttribute('max', '100');
                } else {
                    percentageSymbol.style.display = 'none';
                    fixedSymbol.style.display = 'inline';
                    valueNumberInput.removeAttribute('max');
                }
            }
        }

        typeSelect.addEventListener('change', updateInputs);
        updateInputs();
    });
</script>
@endpush 