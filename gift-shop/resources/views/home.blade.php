@extends('layouts.shop')

@section('title', 'Home')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">Welcome to {{ config('app.name') }}</h1>
                        <p class="text-muted">Your one-stop shop for unique and personalized gifts</p>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-gift text-primary mb-3" style="font-size: 2rem;"></i>
                                <h5>Unique Gifts</h5>
                                <p class="text-muted small">Find the perfect gift for every occasion</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-heart text-primary mb-3" style="font-size: 2rem;"></i>
                                <h5>Personalized</h5>
                                <p class="text-muted small">Add a personal touch to your gifts</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-truck text-primary mb-3" style="font-size: 2rem;"></i>
                                <h5>Fast Delivery</h5>
                                <p class="text-muted small">Quick and secure shipping worldwide</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                            Start Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
