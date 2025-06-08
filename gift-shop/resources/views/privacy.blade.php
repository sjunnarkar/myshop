@extends('layouts.shop')

@section('title', 'Privacy Policy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <h1 class="h2 mb-4">Privacy Policy</h1>
                    
                    <div class="privacy-content">
                        <h2 class="h5 mb-3">1. Information We Collect</h2>
                        <p class="mb-4">
                            We collect information that you provide directly to us, including when you create an account, make a purchase, sign up for our newsletter, or contact us for support.
                        </p>

                        <h2 class="h5 mb-3">2. How We Use Your Information</h2>
                        <p class="mb-4">
                            We use the information we collect to process your orders, send you marketing communications (if you opt in), improve our services, and communicate with you about your account or orders.
                        </p>

                        <h2 class="h5 mb-3">3. Information Sharing</h2>
                        <p class="mb-4">
                            We do not sell, trade, or otherwise transfer your personally identifiable information to third parties. This does not include trusted third parties who assist us in operating our website, conducting our business, or servicing you.
                        </p>

                        <h2 class="h5 mb-3">4. Security</h2>
                        <p class="mb-4">
                            We implement a variety of security measures to maintain the safety of your personal information when you place an order or enter, submit, or access your personal information.
                        </p>

                        <h2 class="h5 mb-3">5. Cookies</h2>
                        <p class="mb-4">
                            We use cookies to help us remember and process the items in your shopping cart, understand and save your preferences for future visits, and compile aggregate data about site traffic and site interaction.
                        </p>

                        <h2 class="h5 mb-3">6. Third-Party Links</h2>
                        <p class="mb-4">
                            Occasionally, at our discretion, we may include or offer third-party products or services on our website. These third-party sites have separate and independent privacy policies.
                        </p>

                        <h2 class="h5 mb-3">7. Your Rights</h2>
                        <p class="mb-4">
                            You have the right to access, correct, or delete your personal information. You can also opt out of marketing communications at any time.
                        </p>

                        <h2 class="h5 mb-3">8. Changes to Privacy Policy</h2>
                        <p class="mb-4">
                            We may update this privacy policy from time to time. We will notify you of any changes by posting the new privacy policy on this page.
                        </p>

                        <h2 class="h5 mb-3">9. Contact Us</h2>
                        <p class="mb-4">
                            If you have any questions about our privacy policy, please contact us at {{ config('mail.from.address') }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.privacy-content h2 {
    color: #333;
    margin-top: 2rem;
}
.privacy-content p {
    color: #666;
    line-height: 1.6;
}
</style>
@endsection 