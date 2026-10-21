<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — {{ $title ?? 'Welcome' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Theme Initialization Script -->
    <script>
        (function() {
            window.getResolvedTheme = function(pref) {
                if (pref === 'system') {
                    return (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) ? 'light' : 'dark';
                }
                return pref === 'light' ? 'light' : 'dark';
            };

            const stored = localStorage.getItem('theme_preference') || 'dark';
            const effective = window.getResolvedTheme(stored);

            document.documentElement.setAttribute('data-theme', effective);
            document.documentElement.setAttribute('data-theme-preference', stored);

            window.applyGuestTheme = function(pref) {
                if (!['dark', 'light', 'system'].includes(pref)) {
                    pref = 'dark';
                }
                localStorage.setItem('theme_preference', pref);
                document.documentElement.setAttribute('data-theme-preference', pref);
                const resolved = window.getResolvedTheme(pref);
                document.documentElement.setAttribute('data-theme', resolved);
                if (typeof window.updateGuestThemeIcons === 'function') {
                    window.updateGuestThemeIcons(pref, resolved);
                }
            };

            window.cycleGuestTheme = function() {
                const current = document.documentElement.getAttribute('data-theme-preference') || 'dark';
                const cycle = { 'dark': 'light', 'light': 'system', 'system': 'dark' };
                const next = cycle[current] || 'dark';
                window.applyGuestTheme(next);
            };

            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', e => {
                    if (document.documentElement.getAttribute('data-theme-preference') === 'system') {
                        const newTheme = e.matches ? 'light' : 'dark';
                        document.documentElement.setAttribute('data-theme', newTheme);
                        if (typeof window.updateGuestThemeIcons === 'function') {
                            window.updateGuestThemeIcons('system', newTheme);
                        }
                    }
                });
            }
        })();
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root, [data-theme="dark"] {
            --bg-primary: #0d0f1a;
            --bg-secondary: #13162b;
            --bg-card: rgba(255, 255, 255, 0.04);
            --border: rgba(255,255,255,0.08);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-purple: #7c3aed;
            --accent-blue: #2563eb;
            --gradient-accent: linear-gradient(135deg, #7c3aed, #2563eb);
            --gradient-bg: radial-gradient(ellipse at 20% 50%, rgba(124,58,237,0.15) 0%, transparent 50%),
                           radial-gradient(ellipse at 80% 20%, rgba(37,99,235,0.1) 0%, transparent 50%),
                           linear-gradient(135deg, #0d0f1a 0%, #13162b 50%, #0a0d1c 100%);
            --input-bg: rgba(255,255,255,0.05);
            --input-border: rgba(255,255,255,0.08);
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-xl: 28px;
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: rgba(255, 255, 255, 0.88);
            --border: rgba(203, 213, 225, 0.7);
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --accent-purple: #7c3aed;
            --accent-blue: #2563eb;
            --gradient-accent: linear-gradient(135deg, #7c3aed, #2563eb);
            --gradient-bg: radial-gradient(ellipse at 20% 50%, rgba(124,58,237,0.08) 0%, transparent 50%),
                           radial-gradient(ellipse at 80% 20%, rgba(37,99,235,0.06) 0%, transparent 50%),
                           linear-gradient(135deg, #f8fafc 0%, #eef2f6 50%, #f1f5f9 100%);
            --input-bg: rgba(255, 255, 255, 0.95);
            --input-border: rgba(203, 213, 225, 0.85);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            padding: 20px;
            position: relative;
        }
        .theme-toggle-floating {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            border-radius: var(--radius-sm);
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(12px);
            transition: all var(--transition);
        }
        .theme-toggle-floating:hover {
            color: var(--text-primary);
            border-color: rgba(124,58,237,0.4);
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
            background: var(--bg-card);
            backdrop-filter: blur(30px);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 36px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.15), 0 0 40px rgba(124,58,237,0.08);
        }
        .auth-title { font-size: 22px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px; }
        .auth-sub { font-size: 13px; color: var(--text-muted); margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
        .form-control {
            width: 100%; padding: 11px 14px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary); font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all var(--transition); outline: none;
        }
        .form-control:focus { border-color: var(--accent-purple); background: var(--input-bg); box-shadow: 0 0 0 3px rgba(124,58,237,0.12); }
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
        [data-theme="light"] .auth-link { color: #7c3aed; }
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

        @media (max-width: 480px) {
            body { padding: 14px; }
            .auth-card { padding: 24px 18px; border-radius: var(--radius-lg); }
            .auth-title { font-size: 19px; }
            .auth-sub { font-size: 12px; margin-bottom: 20px; }
            .auth-logo { margin-bottom: 22px; }
            .logo-icon { width: 38px; height: 38px; }
            .logo-name { font-size: 18px; }
        }
    </style>
</head>
<body>
    <button type="button" class="theme-toggle-floating" id="guest-theme-btn" onclick="window.cycleGuestTheme()" title="Toggle Color Theme" aria-label="Toggle Color Theme">
        <span id="guest-icon-moon" style="display:none; line-height:0;"><i data-lucide="moon" style="width:18px;height:18px;"></i></span>
        <span id="guest-icon-sun" style="display:none; line-height:0;"><i data-lucide="sun" style="width:18px;height:18px;color:#f59e0b;"></i></span>
        <span id="guest-icon-system" style="display:none; line-height:0;"><i data-lucide="monitor" style="width:18px;height:18px;color:#10b981;"></i></span>
    </button>

    <div class="auth-container">
        <div class="auth-logo">
            <div class="logo-icon">
                <i data-lucide="zap" style="color:white;width:22px;height:22px;"></i>
            </div>
            <div class="logo-name">Smart Life Organizer</div>
        </div>
        {{ $slot }}
    </div>

    <script>
        window.updateGuestThemeIcons = function(pref, resolved) {
            const iconMoon = document.getElementById('guest-icon-moon');
            const iconSun = document.getElementById('guest-icon-sun');
            const iconSys = document.getElementById('guest-icon-system');
            const btn = document.getElementById('guest-theme-btn');
            if (!iconMoon || !iconSun || !iconSys) return;

            iconMoon.style.display = 'none';
            iconSun.style.display = 'none';
            iconSys.style.display = 'none';

            if (pref === 'system') {
                iconSys.style.display = 'inline-block';
                if (btn) btn.title = 'Theme: System (' + resolved + ')';
            } else if (resolved === 'light') {
                iconSun.style.display = 'inline-block';
                if (btn) btn.title = 'Theme: Light';
            } else {
                iconMoon.style.display = 'inline-block';
                if (btn) btn.title = 'Theme: Dark';
            }
        };

        lucide.createIcons();

        (function() {
            const pref = document.documentElement.getAttribute('data-theme-preference') || 'dark';
            const resolved = document.documentElement.getAttribute('data-theme') || 'dark';
            window.updateGuestThemeIcons(pref, resolved);
        })();
    </script>
</body>
</html>
