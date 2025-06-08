@extends('layouts.admin')

@section('title', 'Create Category')

@section('admin_title')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-grid"></i> Create Category
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Categories
        </a>
    </div>
@endsection

@section('admin_content')
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
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
                            rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" 
                            class="form-control @error('image') is-invalid @enderror" 
                            id="image" 
                            name="image"
                            accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Recommended size: 800x600px</div>
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" 
                            class="form-control @error('sort_order') is-invalid @enderror" 
                            id="sort_order" 
                            name="sort_order" 
                            value="{{ old('sort_order', 0) }}" 
                            min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                    <i class="bi bi-save"></i> Create Category
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Preview image before upload
    document.getElementById('image').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;
            img.classList.add('img-thumbnail', 'mt-2');
            img.style.maxHeight = '200px';
            
            const previewContainer = document.getElementById('image').parentNode;
            const oldPreview = previewContainer.querySelector('img');
            if (oldPreview) {
                oldPreview.remove();
            }
            previewContainer.appendChild(img);
        }
        reader.readAsDataURL(e.target.files[0]);
    });
</script>
@endpush 