<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('admin_title', 'Admin Panel') - {{ config('app.name', 'Gift Shop') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .navbar-brand {
            padding-top: 1rem;
            padding-bottom: 1rem;
            font-size: 1.25rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
        .navbar .form-control {
            padding: .75rem 1rem;
            border-width: 0;
            border-radius: 0;
        }
        .form-control-dark {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .1);
        }
        .form-control-dark:focus {
            border-color: transparent;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
        }

        /* Pagination Styles */
        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.25rem !important;
            margin: 0 2px;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .page-item.disabled .page-link {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .page-link:focus {
            box-shadow: none;
        }

        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.765625rem;
        }

        /* Control arrow size */
        .page-link[aria-label="@lang('pagination.previous')"],
        .page-link[aria-label="@lang('pagination.next')"] {
            font-size: 16px;
            line-height: 1;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">Admin Panel</h4>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="fas fa-th-large"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <i class="fas fa-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <i class="fas fa-shopping-cart"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.customization-templates.index') }}" class="nav-link {{ request()->routeIs('admin.customization-templates.*') ? 'active' : '' }}">
                                <i class="fas fa-paint-brush"></i> Templates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" 
                               href="#" 
                               data-bs-toggle="collapse" 
                               data-bs-target="#collapseInventory" 
                               aria-expanded="{{ request()->routeIs('admin.inventory.*') ? 'true' : 'false' }}" 
                               aria-controls="collapseInventory">
                                <i class="fas fa-warehouse"></i> Inventory
                            </a>
                            <div id="collapseInventory" 
                                 class="collapse {{ request()->routeIs('admin.inventory.*') ? 'show' : '' }}">
                                <div class="bg-dark py-2 collapse-inner rounded">
                                    <a class="nav-link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}" 
                                       href="{{ route('admin.inventory.index') }}">
                                        Overview
                                    </a>
                                    <a class="nav-link {{ request()->routeIs('admin.inventory.alerts') ? 'active' : '' }}" 
                                       href="{{ route('admin.inventory.alerts') }}">
                                        Low Stock Alerts
                                        @if($lowStockCount ?? 0 > 0)
                                            <span class="badge bg-danger">{{ $lowStockCount }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('admin_title')</h1>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                @yield('admin_content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    @stack('scripts')
</body>
</html> 