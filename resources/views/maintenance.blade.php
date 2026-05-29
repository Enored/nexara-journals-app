<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — {{ $platformName }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 1.5rem;
        }
        main {
            max-width: 28rem;
            text-align: center;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 0 0.75rem;
        }
        p {
            margin: 0;
            line-height: 1.6;
            color: #475569;
        }
    </style>
</head>
<body>
    <main>
        <h1>{{ $platformName }} is under maintenance</h1>
        <p>We are performing scheduled work and will be back shortly. Thank you for your patience.</p>
    </main>
</body>
</html>
