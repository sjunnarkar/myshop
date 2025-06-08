@extends('layouts.admin')

@section('title', 'Edit Category')

@section('admin_title')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-grid"></i> Edit Category: {{ $category->name }}
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Categories
        </a>
    </div>
@endsection

@section('admin_content')
<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
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
                            value="{{ old('name', $category->name) }}" 
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
                            rows="3">{{ old('description', $category->description) }}</textarea>
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
                        
                        @if($category->image)
                            <div class="mt-2">
                                <img src="{{ Storage::url($category->image) }}" 
                                    alt="{{ $category->name }}" 
                                    class="img-thumbnail" 
                                    style="max-height: 200px;">
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" 
                            class="form-control @error('sort_order') is-invalid @enderror" 
                            id="sort_order" 
                            name="sort_order" 
                            value="{{ old('sort_order', $category->sort_order) }}" 
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
                                {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Category
                </button>

                <button type="button" 
                    class="btn btn-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Delete Category
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category?</p>
                @if($category->products->count() > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This category has {{ $category->products->count() }} products associated with it.
                        Deleting this category will remove these associations.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
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