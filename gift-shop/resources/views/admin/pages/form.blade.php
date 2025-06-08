@extends('layouts.admin')

@section('admin_title', isset($page) ? 'Edit Page: ' . $page->title : 'Create New Page')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            {{ isset($page) ? 'Edit Page' : 'Create New Page' }}
        </h1>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Pages
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($page) ? route('admin.pages.update', $page) : route('admin.pages.store') }}" 
                  method="POST">
                @csrf
                @if(isset($page))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <!-- Main Content -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Page Title</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $page->title ?? '') }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">URL Slug</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $page->slug ?? '') }}">
                            <div class="form-text">Leave empty to auto-generate from title</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="15" 
                                      required>{{ old('content', $page->content ?? '') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Sidebar -->
                        <div class="card">
                            <div class="card-header">
                                Page Settings
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="layout" class="form-label">Layout</label>
                                    <select class="form-select @error('layout') is-invalid @enderror" 
                                            id="layout" 
                                            name="layout" 
                                            required>
                                        <option value="default" {{ (old('layout', $page->layout ?? '') == 'default') ? 'selected' : '' }}>
                                            Default
                                        </option>
                                        <option value="full-width" {{ (old('layout', $page->layout ?? '') == 'full-width') ? 'selected' : '' }}>
                                            Full Width
                                        </option>
                                        <option value="sidebar" {{ (old('layout', $page->layout ?? '') == 'sidebar') ? 'selected' : '' }}>
                                            With Sidebar
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Page is active
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="show_in_header" 
                                               name="show_in_header" 
                                               value="1" 
                                               {{ old('show_in_header', $page->show_in_header ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_in_header">
                                            Show in Header Menu
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="show_in_footer" 
                                               name="show_in_footer" 
                                               value="1" 
                                               {{ old('show_in_footer', $page->show_in_footer ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_in_footer">
                                            Show in Footer Menu
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="order" class="form-label">Menu Order</label>
                                    <input type="number" 
                                           class="form-control @error('order') is-invalid @enderror" 
                                           id="order" 
                                           name="order" 
                                           value="{{ old('order', $page->order ?? 0) }}">
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="card mt-4">
                            <div class="card-header">
                                SEO Settings
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" 
                                           class="form-control @error('meta_title') is-invalid @enderror" 
                                           id="meta_title" 
                                           name="meta_title" 
                                           value="{{ old('meta_title', $page->meta_title ?? '') }}">
                                    <div class="form-text">Leave empty to use page title</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" 
                                              name="meta_description" 
                                              rows="3">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($page) ? 'Update Page' : 'Create Page' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#content',
    height: 500,
    menubar: true,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
    ],
    toolbar: 'undo redo | formatselect | bold italic backcolor | \
        alignleft aligncenter alignright alignjustify | \
        bullist numlist outdent indent | removeformat | help'
});

// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    if (!document.getElementById('slug').value) {
        document.getElementById('slug').value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9-]/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }
});
</script>
@endpush
@endsection 