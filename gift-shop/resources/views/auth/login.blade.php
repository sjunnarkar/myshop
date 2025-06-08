@extends('layouts.shop')

@section('title', 'Login')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">Welcome Back</h1>
                        <p class="text-muted">Sign in to your account to continue</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
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

                        <div class="form-floating mb-3">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="name@example.com" 
                                   required 
                                   autofocus>
                            <label for="email">Email address</label>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
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

                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="remember" 
                                   id="remember" 
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid mb-4">
                            <button class="btn btn-primary btn-lg" type="submit">Sign in</button>
                        </div>

                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            @endif
                            <p class="mt-3 mb-0">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-decoration-none">Register now</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">
                    By signing in, you agree to our 
                    <a href="{{ route('terms') }}" class="text-decoration-none">Terms of Service</a> and 
                    <a href="{{ route('privacy') }}" class="text-decoration-none">Privacy Policy</a>.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.form-floating > .form-control:focus,
.form-floating > .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
}

.form-floating > .form-control:-webkit-autofill {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label,
.form-floating > .form-select ~ label {
    opacity: 0.65;
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
}

.form-floating > .form-control:-webkit-autofill ~ label {
    opacity: 0.65;
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
}
</style>
@endsection 