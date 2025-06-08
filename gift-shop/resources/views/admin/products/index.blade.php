@extends('layouts.admin')

@section('title', 'Products')

@section('admin_title')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-box"></i> Products
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Product
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
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th width="150">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->thumbnail)
                            <img src="{{ Storage::url($product->thumbnail) }}" 
                                alt="{{ $product->name }}" 
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
                        <strong>{{ $product->name }}</strong>
                        <br>
                        <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                    </td>
                    <td>
                        <span class="badge bg-info">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td>₹{{ number_format($product->base_price, 2) }}</td>
                    <td>
                        @if($product->stock > 10)
                            <span class="badge bg-success">{{ $product->stock }}</span>
                        @elseif($product->stock > 0)
                            <span class="badge bg-warning">{{ $product->stock }}</span>
                        @else
                            <span class="badge bg-danger">Out of stock</span>
                        @endif
                    </td>
                    <td>
                        @if($product->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                                class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" 
                                method="POST" 
                                class="d-inline" 
                                onsubmit="return confirm('Are you sure you want to delete this product?');">
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
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-box fs-2"></i>
                            <p class="mt-2">No products found.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $products->links() }}
</div>
@endsection 