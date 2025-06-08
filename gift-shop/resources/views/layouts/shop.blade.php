<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .nav-link {
            font-weight: 500;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 3rem 0;
            margin-top: 3rem;
        }
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }
        .password-toggle:hover {
            color: #495057;
        }
        .password-wrapper {
            position: relative;
        }
        .product-card .card-footer {
            padding: 0.75rem;
            background-color: transparent;
        }
        .product-card .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-card .btn i {
            font-size: 1rem;
        }
        @media (max-width: 576px) {
            .product-card .card-footer {
                padding: 0.5rem;
            }
            
            .product-card .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.8125rem;
                height: 34px;
            }
        }
        .quantity-form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quantity-form .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .quantity-form input {
            width: 60px;
            text-align: center;
            -moz-appearance: textfield;
        }
        .quantity-form input::-webkit-outer-spin-button,
        .quantity-form input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                {{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Shop</a>
                    </li>
                    @foreach(\App\Models\Category::active()->sorted()->get() as $category)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.show', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <span class="badge bg-primary">{{ $cartCount }}</span>
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}#orders">Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">{{ config('app.name') }}</h5>
                    <p class="text-muted">
                        Your one-stop shop for personalized gifts and custom merchandise. 
                        Create unique, memorable items that celebrate special moments.
                    </p>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h6 class="mb-3">Shop</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('products.index') }}" class="text-muted text-decoration-none">All Products</a></li>
                        @foreach(\App\Models\Category::active()->sorted()->get() as $category)
                            <li class="mb-2">
                                <a href="{{ route('categories.show', $category->slug) }}" class="text-muted text-decoration-none">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h6 class="mb-3">Account</h6>
                    <ul class="list-unstyled">
                        @auth
                            <li class="mb-2"><a href="{{ route('profile.show') }}" class="text-muted text-decoration-none">Profile</a></li>
                            <li class="mb-2"><a href="{{ route('profile.show') }}#orders" class="text-muted text-decoration-none">Orders</a></li>
                        @else
                            <li class="mb-2"><a href="{{ route('login') }}" class="text-muted text-decoration-none">Login</a></li>
                            <li class="mb-2"><a href="{{ route('register') }}" class="text-muted text-decoration-none">Register</a></li>
                        @endauth
                        <li class="mb-2"><a href="{{ route('cart.index') }}" class="text-muted text-decoration-none">Cart</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-3">Contact</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> support@example.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> (555) 123-4567</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Main St, City, Country</li>
                    </ul>
                    <div class="mt-4">
                        <a href="#" class="text-muted text-decoration-none me-3"><i class="fab fa-facebook fs-5"></i></a>
                        <a href="#" class="text-muted text-decoration-none me-3"><i class="fab fa-instagram fs-5"></i></a>
                        <a href="#" class="text-muted text-decoration-none me-3"><i class="fab fa-twitter fs-5"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <i class="bi bi-credit-card fs-4"></i> Payment Methods
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.querySelector(`[onclick="togglePassword('${inputId}')"]`);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }
    </script>
    @stack('scripts')
</body>
</html> 