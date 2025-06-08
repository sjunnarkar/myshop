@extends('layouts.admin')

@section('title', 'Edit Discount')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Edit Discount</h1>
    </div>

    <form action="{{ route('admin.discounts.update', $discount) }}" method="POST">
        @csrf
        @method('PATCH')
        @include('admin.discounts.form')
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('select[multiple]').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush 