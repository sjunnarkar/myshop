@extends('layouts.shop')

@section('title', 'Terms of Service')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    <h1 class="h2 mb-4">Terms of Service</h1>
                    
                    <div class="terms-content">
                        <h2 class="h5 mb-3">1. Acceptance of Terms</h2>
                        <p class="mb-4">
                            By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.
                        </p>

                        <h2 class="h5 mb-3">2. Use License</h2>
                        <p class="mb-4">
                            Permission is granted to temporarily download one copy of the materials (information or software) on {{ config('app.name') }}'s website for personal, non-commercial transitory viewing only.
                        </p>

                        <h2 class="h5 mb-3">3. Disclaimer</h2>
                        <p class="mb-4">
                            The materials on {{ config('app.name') }}'s website are provided on an 'as is' basis. {{ config('app.name') }} makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
                        </p>

                        <h2 class="h5 mb-3">4. Limitations</h2>
                        <p class="mb-4">
                            In no event shall {{ config('app.name') }} or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on {{ config('app.name') }}'s website.
                        </p>

                        <h2 class="h5 mb-3">5. Accuracy of Materials</h2>
                        <p class="mb-4">
                            The materials appearing on {{ config('app.name') }}'s website could include technical, typographical, or photographic errors. {{ config('app.name') }} does not warrant that any of the materials on its website are accurate, complete or current.
                        </p>

                        <h2 class="h5 mb-3">6. Links</h2>
                        <p class="mb-4">
                            {{ config('app.name') }} has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by {{ config('app.name') }} of the site.
                        </p>

                        <h2 class="h5 mb-3">7. Modifications</h2>
                        <p class="mb-4">
                            {{ config('app.name') }} may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.
                        </p>

                        <h2 class="h5 mb-3">8. Governing Law</h2>
                        <p class="mb-4">
                            These terms and conditions are governed by and construed in accordance with the laws and you irrevocably submit to the exclusive jurisdiction of the courts in that location.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.terms-content h2 {
    color: #333;
    margin-top: 2rem;
}
.terms-content p {
    color: #666;
    line-height: 1.6;
}
</style>
@endsection 