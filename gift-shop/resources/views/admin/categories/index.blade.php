@extends('layouts.admin')

@section('title', 'Categories')

@section('admin_title')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-grid"></i> Categories
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Category
        </a>
    </div>
@endsection

@section('admin_content')
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th width="80">Image</th>
                <th>Name</th>
                <th>Products</th>
                <th>Status</th>
                <th>Sort Order</th>
                <th width="150">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" 
                                alt="{{ $category->name }}" 
                                class="img-thumbnail" 
                                width="50">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                style="width: 50px; height: 50px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $category->name }}</strong>
                        @if($category->description)
                            <br>
                            <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info">
                            {{ $category->products->count() }} products
                        </span>
                    </td>
                    <td>
                        @if($category->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $category->sort_order }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" 
                                method="POST" 
                                class="d-inline" 
                                onsubmit="return confirm('Are you sure you want to delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-grid fs-2"></i>
                            <p class="mt-2">No categories found.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $categories->links() }}
</div>
@endsection 