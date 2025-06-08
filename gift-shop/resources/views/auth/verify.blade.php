@extends('layouts.shop')

@section('title', 'Verify Email')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">Verify Your Email</h1>
                        <p class="text-muted">Please check your email for a verification link</p>
                    </div>

                    @if (session('resent'))
                        <div class="alert alert-success mb-4" role="alert">
                            A fresh verification link has been sent to your email address.
                        </div>
                    @endif

                    <p class="mb-4">
                        Before proceeding, please check your email for a verification link.
                        If you did not receive the email,
                    </p>

                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Click here to request another
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 