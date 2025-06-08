@extends('layouts.admin')

@section('title', 'View Customization Template')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">View Customization Template</h1>
        <div>
            <a href="{{ route('admin.customization-templates.edit', $customizationTemplate) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil"></i> Edit Template
            </a>
            <a href="{{ route('admin.customization-templates.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Templates
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Template Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $customizationTemplate->name }}</p>
                            <p><strong>Created:</strong> {{ $customizationTemplate->created_at->format('M d, Y H:i A') }}</p>
                            <p><strong>Last Updated:</strong> {{ $customizationTemplate->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Description:</strong></p>
                            <p>{{ $customizationTemplate->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Template Fields</h5>
                </div>
                <div class="card-body">
                    @if(empty($customizationTemplate->fields))
                        <div class="alert alert-info">
                            No fields have been defined for this template.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Field Name</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customizationTemplate->fields as $index => $field)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $field['name'] }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($field['type']) }}</span>
                                            </td>
                                            <td>
                                                @if($field['required'])
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-warning">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($field['type'] === 'select' && !empty($field['options']))
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($field['options'] as $option)
                                                            <li>{{ $option }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Products Using This Template</h5>
                </div>
                <div class="card-body">
                    @if($customizationTemplate->products->isEmpty())
                        <div class="alert alert-info">
                            No products are using this template.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customizationTemplate->products as $product)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.products.edit', $product) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>₹{{ number_format($product->base_price, 2) }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 