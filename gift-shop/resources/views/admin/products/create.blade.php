@extends('layouts.admin')

@section('title', 'Create Product')

@section('admin_title')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-box"></i> Create Product
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>
@endsection

@section('admin_content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                            id="description" 
                            name="description" 
                            rows="3" 
                            required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="base_price" class="form-label">Base Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                        class="form-control @error('base_price') is-invalid @enderror" 
                                        id="base_price" 
                                        name="base_price" 
                                        value="{{ old('base_price') }}" 
                                        step="0.01" 
                                        min="0" 
                                        required>
                                    @error('base_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" 
                                    class="form-control @error('stock') is-invalid @enderror" 
                                    id="stock" 
                                    name="stock" 
                                    value="{{ old('stock', 0) }}" 
                                    min="0" 
                                    required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dimensions</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Width</span>
                                    <input type="number" 
                                        class="form-control @error('dimensions.width') is-invalid @enderror" 
                                        name="dimensions[width]" 
                                        value="{{ old('dimensions.width') }}" 
                                        step="0.1">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Height</span>
                                    <input type="number" 
                                        class="form-control @error('dimensions.height') is-invalid @enderror" 
                                        name="dimensions[height]" 
                                        value="{{ old('dimensions.height') }}" 
                                        step="0.1">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Length</span>
                                    <input type="number" 
                                        class="form-control @error('dimensions.length') is-invalid @enderror" 
                                        name="dimensions[length]" 
                                        value="{{ old('dimensions.length') }}" 
                                        step="0.1">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Printing Areas</label>
                        <div id="printing-areas">
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <input type="text" 
                                        class="form-control" 
                                        name="printing_areas[0][name]" 
                                        placeholder="Area Name (e.g., Front)">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" 
                                        class="form-control" 
                                        name="printing_areas[0][width]" 
                                        placeholder="Width (cm)" 
                                        step="0.1">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" 
                                        class="form-control" 
                                        name="printing_areas[0][height]" 
                                        placeholder="Height (cm)" 
                                        step="0.1">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addPrintingArea()">
                            <i class="bi bi-plus-lg"></i> Add Printing Area
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Customization Templates</label>
                        <div class="list-group">
                            @foreach($customizationTemplates as $template)
                                <div class="list-group-item">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                            type="checkbox" 
                                            name="customization_templates[]" 
                                            value="{{ $template->id }}" 
                                            id="template_{{ $template->id }}"
                                            {{ in_array($template->id, old('customization_templates', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="template_{{ $template->id }}">
                                            {{ $template->name }}
                                            <small class="text-muted d-block">{{ $template->description }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('customization_templates')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Customization Options</label>
                        <div id="customization-options">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <input type="text" 
                                        class="form-control" 
                                        name="customization_options[0][name]" 
                                        placeholder="Option Name (e.g., Text Color)">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" 
                                        class="form-control" 
                                        name="customization_options[0][values]" 
                                        placeholder="Values (comma-separated)">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCustomizationOption()">
                            <i class="bi bi-plus-lg"></i> Add Customization Option
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                            id="category_id" 
                            name="category_id" 
                            required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail</label>
                        <input type="file" 
                            class="form-control @error('thumbnail') is-invalid @enderror" 
                            id="thumbnail" 
                            name="thumbnail"
                            accept="image/*">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Recommended size: 800x800px</div>
                    </div>

                    <div class="mb-3">
                        <label for="additional_images" class="form-label">Additional Images</label>
                        <input type="file" 
                            class="form-control @error('additional_images.*') is-invalid @enderror" 
                            id="additional_images" 
                            name="additional_images[]"
                            accept="image/*"
                            multiple>
                        @error('additional_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">You can select multiple images</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Create Product
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Preview thumbnail before upload
    document.getElementById('thumbnail').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;
            img.classList.add('img-thumbnail', 'mt-2');
            img.style.maxHeight = '200px';
            
            const previewContainer = document.getElementById('thumbnail').parentNode;
            const oldPreview = previewContainer.querySelector('img');
            if (oldPreview) {
                oldPreview.remove();
            }
            previewContainer.appendChild(img);
        }
        reader.readAsDataURL(e.target.files[0]);
    });

    // Preview additional images before upload
    document.getElementById('additional_images').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('additional_images').parentNode;
        const oldPreviews = previewContainer.querySelectorAll('.additional-image-preview');
        oldPreviews.forEach(preview => preview.remove());

        const previewDiv = document.createElement('div');
        previewDiv.classList.add('additional-image-preview', 'mt-2', 'd-flex', 'gap-2', 'flex-wrap');

        Array.from(e.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;
                img.classList.add('img-thumbnail');
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                previewDiv.appendChild(img);
            }
            reader.readAsDataURL(file);
        });

        previewContainer.appendChild(previewDiv);
    });

    // Add printing area
    let printingAreaCount = 1;
    function addPrintingArea() {
        const container = document.getElementById('printing-areas');
        const newArea = document.createElement('div');
        newArea.classList.add('row', 'mb-2');
        newArea.innerHTML = `
            <div class="col-md-4">
                <input type="text" 
                    class="form-control" 
                    name="printing_areas[${printingAreaCount}][name]" 
                    placeholder="Area Name (e.g., Front)">
            </div>
            <div class="col-md-4">
                <input type="number" 
                    class="form-control" 
                    name="printing_areas[${printingAreaCount}][width]" 
                    placeholder="Width (cm)" 
                    step="0.1">
            </div>
            <div class="col-md-4">
                <input type="number" 
                    class="form-control" 
                    name="printing_areas[${printingAreaCount}][height]" 
                    placeholder="Height (cm)" 
                    step="0.1">
            </div>
        `;
        container.appendChild(newArea);
        printingAreaCount++;
    }

    // Add customization option
    let customizationOptionCount = 1;
    function addCustomizationOption() {
        const container = document.getElementById('customization-options');
        const newOption = document.createElement('div');
        newOption.classList.add('row', 'mb-2');
        newOption.innerHTML = `
            <div class="col-md-6">
                <input type="text" 
                    class="form-control" 
                    name="customization_options[${customizationOptionCount}][name]" 
                    placeholder="Option Name (e.g., Text Color)">
            </div>
            <div class="col-md-6">
                <input type="text" 
                    class="form-control" 
                    name="customization_options[${customizationOptionCount}][values]" 
                    placeholder="Values (comma-separated)">
            </div>
        `;
        container.appendChild(newOption);
        customizationOptionCount++;
    }
</script>
@endpush 