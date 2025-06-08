@extends('layouts.shop')

@section('title', 'Confirm Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">Confirm Password</h1>
                        <p class="text-muted">Please confirm your password before continuing</p>
                    </div>

                    <form method="POST" action="{{ route('password.confirm') }}" class="needs-validation" novalidate>
                        @csrf

                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-wrapper">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password">
                                <span class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid mb-4">
                            <button class="btn btn-primary btn-lg" type="submit">Confirm Password</button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <p class="mb-0">
                                    <a href="{{ route('password.request') }}" class="text-decoration-none">
                                        Forgot Your Password?
                                    </a>
                                </p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
