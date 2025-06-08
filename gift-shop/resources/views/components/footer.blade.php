@props(['showNewsletter' => true])

<footer class="footer mt-auto py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <!-- Quick Links -->
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-decoration-none text-secondary">Home</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('products.index') }}" class="text-decoration-none text-secondary">Shop</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-secondary">About Us</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-secondary">Contact</a>
                    </li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">Customer Service</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-secondary">FAQ</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-secondary">Shipping Information</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-secondary">Returns Policy</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-secondary">Privacy Policy</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt me-2"></i> 123 Gift Street, Shop City
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone me-2"></i> (555) 123-4567
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2"></i> info@giftshop.com
                    </li>
                    <li class="mb-2">
                        <div class="d-flex gap-2 mt-3">
                            <a href="#" class="text-secondary"><i class="bi bi-facebook fs-5"></i></a>
                            <a href="#" class="text-secondary"><i class="bi bi-instagram fs-5"></i></a>
                            <a href="#" class="text-secondary"><i class="bi bi-twitter fs-5"></i></a>
                            <a href="#" class="text-secondary"><i class="bi bi-pinterest fs-5"></i></a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            @if($showNewsletter)
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">Stay Updated</h5>
                <x-newsletter-subscription class="p-0" />
            </div>
            @endif
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-12 text-center">
                <p class="text-muted mb-0">
                    © {{ date('Y') }} Gift Shop. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.footer h5 {
    font-weight: 600;
    font-size: 1.1rem;
}

.footer .newsletter-subscription .card {
    background: transparent !important;
    box-shadow: none !important;
}

.footer .newsletter-subscription .card-body {
    padding: 0;
}

.footer .newsletter-subscription .card-title {
    display: none;
}

.footer .newsletter-subscription .text-muted {
    font-size: 0.9rem;
}

.footer .newsletter-subscription .form-floating {
    margin-bottom: 1rem;
}

@media (max-width: 767.98px) {
    .footer .newsletter-subscription .row {
        flex-direction: column;
    }
    
    .footer .newsletter-subscription .col-md-2 {
        width: 100%;
    }
}
</style> 