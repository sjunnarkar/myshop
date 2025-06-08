@extends('layouts.shop')

@section('title', 'Newsletter Preferences')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">Newsletter Preferences</h1>
                        <p class="text-muted">Manage your email subscription preferences</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('newsletter.update') }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="newsletter" 
                                   id="newsletter" 
                                   {{ auth()->user()->newsletter ? 'checked' : '' }}>
                            <label class="form-check-label" for="newsletter">
                                Receive promotional emails about new products, sales, and updates
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="order_updates" 
                                   id="order_updates" 
                                   {{ auth()->user()->order_updates ? 'checked' : '' }}>
                            <label class="form-check-label" for="order_updates">
                                Receive order status updates and shipping notifications
                            </label>
                        </div>

                        <div class="d-grid mb-4">
                            <button class="btn btn-primary btn-lg" type="submit">Save Preferences</button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                You can change these preferences at any time
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 