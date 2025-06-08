@extends('layouts.shop')

@section('title', 'Reset Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">Reset Password</h1>
                        <p class="text-muted">Enter your email to receive a password reset link</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
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

                        <div class="d-grid mb-4">
                            <button class="btn btn-primary btn-lg" type="submit">Send Reset Link</button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">
                                Remember your password? 
                                <a href="{{ route('login') }}" class="text-decoration-none">Sign in</a>
                            </p>
                        </div>
                    </form>
                </div>
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