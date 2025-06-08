@extends('layouts.admin')

@section('admin_title', 'Manage Pages')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Content Pages</h1>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Page
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pagesTable">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Last Updated</th>
                            <th style="width: 150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pagesList">
                        @foreach($pages as $page)
                            <tr data-id="{{ $page->id }}">
                                <td>
                                    <i class="fas fa-grip-vertical handle cursor-move"></i>
                                    {{ $page->order }}
                                </td>
                                <td>{{ $page->title }}</td>
                                <td>
                                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank">
                                        {{ $page->slug }}
                                    </a>
                                </td>
                                <td>
                                    @if($page->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($page->show_in_header)
                                        <span class="badge bg-info">Header</span>
                                    @endif
                                    @if($page->show_in_footer)
                                        <span class="badge bg-info">Footer</span>
                                    @endif
                                </td>
                                <td>{{ $page->updated_at->diffForHumans() }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.pages.edit', $page) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete({{ $page->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $page->id }}" 
                                          action="{{ route('admin.pages.destroy', $page) }}" 
                                          method="POST" 
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $pages->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Sortable(document.getElementById('pagesList'), {
        handle: '.handle',
        animation: 150,
        onEnd: function() {
            const rows = document.querySelectorAll('#pagesList tr');
            const pages = Array.from(rows).map((row, index) => ({
                id: row.dataset.id,
                order: index + 1
            }));

            fetch('{{ route('admin.pages.update-order') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ pages })
            });
        }
    });
});

function confirmDelete(pageId) {
    if (confirm('Are you sure you want to delete this page?')) {
        document.getElementById('delete-form-' + pageId).submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.cursor-move {
    cursor: move;
}
.handle:hover {
    color: #4a5568;
}
</style>
@endpush
@endsection 