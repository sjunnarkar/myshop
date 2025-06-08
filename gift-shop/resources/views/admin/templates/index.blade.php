@extends('layouts.admin')

@section('title', 'Customization Templates')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Customization Templates</h1>
        <a href="{{ route('admin.customization-templates.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Template
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($templates->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-paint-bucket display-1 text-muted"></i>
                    <h3 class="mt-3">No Templates Found</h3>
                    <p class="text-muted">Create your first customization template to get started.</p>
                    <a href="{{ route('admin.customization-templates.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Template
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Fields</th>
                                <th>Products</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ Str::limit($template->description, 50) }}</td>
                                    <td>{{ count($template->fields) }} fields</td>
                                    <td>{{ $template->products->count() }} products</td>
                                    <td>{{ $template->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.customization-templates.edit', $template) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.customization-templates.destroy', $template) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this template?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 