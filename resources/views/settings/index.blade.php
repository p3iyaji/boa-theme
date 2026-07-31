<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Theme Settings — {{ config('app.name', 'BOA') }}</title>
    <x-boa-theme::styles />
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem;
            background:
                radial-gradient(900px 500px at 100% 0%, color-mix(in srgb, var(--boa-accent-200) 45%, transparent), transparent 55%),
                var(--boa-canvas-50);
            font-family: var(--boa-font-sans);
            color: var(--boa-canvas-950);
        }
        .boa-standalone-note {
            max-width: 28rem;
            text-align: center;
        }
        .boa-standalone-note a {
            color: var(--boa-link);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="boa-standalone-note">
        <h1 style="font-family: var(--boa-font-display);">Theme Settings</h1>
        <p>Use the drawer on the right. Prefer opening it from your app with <code>&lt;x-boa-theme::settings-link /&gt;</code> so changes apply on the live page.</p>
        <p><a href="{{ url('/') }}">Back to app</a></p>
    </div>

    @include('boa-theme::settings.drawer', ['standalone' => true])
</body>
</html>
