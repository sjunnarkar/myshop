@extends('layouts.' . ($layout ?? 'default'))

@section('meta_title', $meta_title)
@section('meta_description', $meta_description)

@section('content')
<div class="container py-5">
    @if($page->layout === 'sidebar')
        <div class="row">
            <div class="col-lg-8">
                <article class="cms-content">
                    <h1 class="mb-4">{{ $page->title }}</h1>
                    <div class="content">
                        {!! $page->content !!}
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <div class="sidebar">
                    <!-- Sidebar content can be added here -->
                    @include('partials.sidebar')
                </div>
            </div>
        </div>
    @else
        <div class="{{ $page->layout === 'full-width' ? 'container-fluid px-0' : 'container' }}">
            <article class="cms-content">
                <h1 class="mb-4">{{ $page->title }}</h1>
                <div class="content">
                    {!! $page->content !!}
                </div>
            </article>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.cms-content {
    line-height: 1.6;
}

.cms-content h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
}

.cms-content .content {
    font-size: 1.1rem;
    color: #4a5568;
}

.cms-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1.5rem 0;
}

.cms-content table {
    width: 100%;
    margin: 1rem 0;
    border-collapse: collapse;
}

.cms-content table th,
.cms-content table td {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
}

.cms-content table th {
    background-color: #f7fafc;
    font-weight: 600;
}

.cms-content blockquote {
    margin: 1.5rem 0;
    padding: 1rem 1.5rem;
    border-left: 4px solid #4299e1;
    background-color: #ebf8ff;
    font-style: italic;
}

.cms-content ul,
.cms-content ol {
    margin: 1rem 0;
    padding-left: 2rem;
}

.cms-content li {
    margin: 0.5rem 0;
}

.cms-content a {
    color: #4299e1;
    text-decoration: none;
    transition: color 0.2s;
}

.cms-content a:hover {
    color: #2b6cb0;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .cms-content h1 {
        font-size: 2rem;
    }

    .cms-content .content {
        font-size: 1rem;
    }
}
</style>
@endpush 