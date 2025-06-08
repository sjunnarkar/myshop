<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Construction - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .maintenance-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
        }
        .maintenance-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        .maintenance-title {
            color: #343a40;
            margin-bottom: 1rem;
        }
        .maintenance-text {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .progress {
            height: 0.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">🚧</div>
        <h1 class="maintenance-title">We're Under Construction</h1>
        <p class="maintenance-text">
            Our website is currently undergoing maintenance and improvements. 
            We'll be back soon with a better shopping experience for you!
        </p>
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                 role="progressbar" 
                 style="width: 75%" 
                 aria-valuenow="75" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
            </div>
        </div>
        <p class="text-muted small">
            Expected completion: {{ config('app.maintenance_eta', 'Coming Soon') }}
        </p>
    </div>
</body>
</html> 