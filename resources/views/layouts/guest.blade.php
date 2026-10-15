<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — {{ $title ?? 'Welcome' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg-primary: #0d0f1a;
            --border: rgba(255,255,255,0.08);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-purple: #7c3aed;
            --accent-blue: #2563eb;
            --gradient-accent: linear-gradient(135deg, #7c3aed, #2563eb);
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-xl: 28px;
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(ellipse at 20% 50%, rgba(124,58,237,0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(37,99,235,0.1) 0%, transparent 50%),
                        linear-gradient(135deg, #0d0f1a 0%, #13162b 50%, #0a0d1c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            padding: 20px;
        }
        .auth-container {
            width: 100%;
            max-width: 440px;
        }
        .auth-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 44px; height: 44px;
            background: var(--gradient-accent);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 30px rgba(124,58,237,0.5);
        }
        .logo-name {
            font-size: 20px; font-weight: 800;
            background: var(--gradient-accent);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .auth-card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(30px);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 36px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5), 0 0 40px rgba(124,58,237,0.08);
        }
        .auth-title { font-size: 22px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px; }
        .auth-sub { font-size: 13px; color: var(--text-muted); margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
        .form-control {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary); font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all var(--transition); outline: none;
        }
        .form-control:focus { border-color: var(--accent-purple); background: rgba(124,58,237,0.06); box-shadow: 0 0 0 3px rgba(124,58,237,0.12); }
        .btn-submit {
            width: 100%; padding: 13px;
            background: var(--gradient-accent);
            color: white; border: none;
            border-radius: var(--radius-sm);
            font-size: 14px; font-weight: 700;
            cursor: pointer; font-family: 'Inter', sans-serif;
            transition: all var(--transition);
            box-shadow: 0 4px 16px rgba(124,58,237,0.4);
            margin-top: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 22px rgba(124,58,237,0.5); }
        .auth-footer { text-align: center; margin-top: 22px; font-size: 13px; color: var(--text-muted); }
        .auth-link { color: #a78bfa; text-decoration: none; font-weight: 600; }
        .auth-link:hover { text-decoration: underline; }
        .form-error { font-size: 11.5px; color: #f87171; margin-top: 5px; }
        .alert-error { background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: #f87171; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; }
        .input-icon-wrap { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
        .input-icon + .form-control { padding-left: 38px; }
        .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; color: var(--text-muted); font-size: 12px; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .remember-row { display: flex; align-items: center; justify-content: space-between; }
        input[type=checkbox] { accent-color: var(--accent-purple); width: 15px; height: 15px; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <div class="logo-icon">
                <i data-lucide="zap" style="color:white;width:22px;height:22px;"></i>
            </div>
            <div class="logo-name">Smart Life Organizer</div>
        </div>
        {{ $slot }}
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
