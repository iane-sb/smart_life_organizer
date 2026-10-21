<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-theme="{{ (Auth::user()?->theme_preference ?? 'dark') === 'light' ? 'light' : 'dark' }}"
      data-theme-preference="{{ Auth::user()?->theme_preference ?? 'dark' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Smart Life Organizer - Your intelligent daily planner for tasks, calendar, and notes.">
    <title>{{ config('app.name', 'Smart Life Organizer') }} — @yield('title', 'Dashboard')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Theme Management Scripts -->
    <script>
        (function() {
            window.getResolvedTheme = function(pref) {
                if (pref === 'system') {
                    return (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) ? 'light' : 'dark';
                }
                return pref === 'light' ? 'light' : 'dark';
            };

            const userPref = "{{ Auth::user()->theme_preference ?? 'dark' }}";
            const stored = localStorage.getItem('theme_preference') || userPref || 'dark';
            const effective = window.getResolvedTheme(stored);

            document.documentElement.setAttribute('data-theme', effective);
            document.documentElement.setAttribute('data-theme-preference', stored);

            window.applyThemePreference = function(pref) {
                if (!['dark', 'light', 'system'].includes(pref)) {
                    pref = 'dark';
                }
                localStorage.setItem('theme_preference', pref);
                document.documentElement.setAttribute('data-theme-preference', pref);
                const resolved = window.getResolvedTheme(pref);
                document.documentElement.setAttribute('data-theme', resolved);

                if (typeof window.updateHeaderThemeIcons === 'function') {
                    window.updateHeaderThemeIcons(pref, resolved);
                }

                window.dispatchEvent(new CustomEvent('themechanged', { detail: { preference: pref, resolved: resolved } }));
            };

            window.cycleThemePreference = function() {
                const current = document.documentElement.getAttribute('data-theme-preference') || 'dark';
                const cycle = { 'dark': 'light', 'light': 'system', 'system': 'dark' };
                const next = cycle[current] || 'dark';
                window.applyThemePreference(next);
            };

            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', e => {
                    if (document.documentElement.getAttribute('data-theme-preference') === 'system') {
                        const newTheme = e.matches ? 'light' : 'dark';
                        document.documentElement.setAttribute('data-theme', newTheme);
                        if (typeof window.updateHeaderThemeIcons === 'function') {
                            window.updateHeaderThemeIcons('system', newTheme);
                        }
                        window.dispatchEvent(new CustomEvent('themechanged', { detail: { preference: 'system', resolved: newTheme } }));
                    }
                });
            }
        })();
    </script>

    <style>
        /* ===== CSS VARIABLES & RESET ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ===== DARK THEME (DEFAULT) ===== */
        :root, [data-theme="dark"] {
            --bg-primary:    #0d0f1a;
            --bg-secondary:  #13162b;
            --bg-card:       rgba(255, 255, 255, 0.04);
            --bg-card-hover: rgba(255, 255, 255, 0.08);
            --sidebar-bg:    rgba(13, 15, 26, 0.85);
            --header-bg:     rgba(13, 15, 26, 0.6);
            --modal-bg:      #13162b;
            --dock-bg:       rgba(13, 15, 26, 0.92);
            --border:        rgba(255, 255, 255, 0.08);
            --border-hover:  rgba(255, 255, 255, 0.16);
            --text-primary:  #f1f5f9;
            --text-secondary:#94a3b8;
            --text-muted:    #64748b;
            --accent-purple: #7c3aed;
            --accent-blue:   #2563eb;
            --accent-teal:   #06b6d4;
            --accent-pink:   #ec4899;
            --accent-amber:  #f59e0b;
            --accent-green:  #10b981;
            --accent-red:    #ef4444;
            --gradient-main: linear-gradient(135deg, #0d0f1a 0%, #1a1d2e 50%, #0f1626 100%);
            --gradient-accent: linear-gradient(135deg, #7c3aed, #2563eb);
            --sidebar-width: 260px;
            --header-height: 70px;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --shadow-glow: 0 0 30px rgba(124, 58, 237, 0.15);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.4);
            --input-bg:      rgba(255, 255, 255, 0.05);
            --input-border:  rgba(255, 255, 255, 0.08);
            --input-option-bg: #1a1d2e;
            --tab-group-bg:  rgba(255, 255, 255, 0.04);
            --cal-header-bg: rgba(255, 255, 255, 0.03);
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ===== LIGHT THEME ===== */
        [data-theme="light"] {
            --bg-primary:    #f8fafc;
            --bg-secondary:  #ffffff;
            --bg-card:       rgba(255, 255, 255, 0.85);
            --bg-card-hover: rgba(255, 255, 255, 0.98);
            --sidebar-bg:    rgba(255, 255, 255, 0.9);
            --header-bg:     rgba(255, 255, 255, 0.85);
            --modal-bg:      #ffffff;
            --dock-bg:       rgba(255, 255, 255, 0.94);
            --border:        rgba(203, 213, 225, 0.7);
            --border-hover:  rgba(148, 163, 184, 0.6);
            --text-primary:  #0f172a;
            --text-secondary:#334155;
            --text-muted:    #64748b;
            --accent-purple: #7c3aed;
            --accent-blue:   #2563eb;
            --accent-teal:   #0891b2;
            --accent-pink:   #db2777;
            --accent-amber:  #d97706;
            --accent-green:  #059669;
            --accent-red:    #dc2626;
            --gradient-main: radial-gradient(ellipse at 15% 40%, rgba(124, 58, 237, 0.07) 0%, transparent 60%), radial-gradient(ellipse at 85% 20%, rgba(37, 99, 235, 0.06) 0%, transparent 50%), linear-gradient(135deg, #f8fafc 0%, #eef2f6 50%, #f1f5f9 100%);
            --gradient-accent: linear-gradient(135deg, #7c3aed, #2563eb);
            --shadow-glow: 0 0 25px rgba(124, 58, 237, 0.12);
            --shadow-card: 0 8px 30px rgba(15, 23, 42, 0.07);
            --input-bg:      rgba(255, 255, 255, 0.95);
            --input-border:  rgba(203, 213, 225, 0.85);
            --input-option-bg: #ffffff;
            --tab-group-bg:  rgba(0, 0, 0, 0.05);
            --cal-header-bg: rgba(0, 0, 0, 0.03);
        }

        /* High-contrast and crisp light theme adjustments */
        [data-theme="light"] .badge-purple { color: #7c3aed; background: rgba(124, 58, 237, 0.1); border-color: rgba(124, 58, 237, 0.25); }
        [data-theme="light"] .badge-blue   { color: #1d4ed8; background: rgba(37, 99, 235, 0.1); border-color: rgba(37, 99, 235, 0.25); }
        [data-theme="light"] .badge-green  { color: #047857; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.25); }
        [data-theme="light"] .badge-amber  { color: #b45309; background: rgba(245, 158, 11, 0.12); border-color: rgba(245, 158, 11, 0.25); }
        [data-theme="light"] .badge-red    { color: #dc2626; background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.25); }
        [data-theme="light"] .priority-urgent { color: #dc2626; background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.25); }
        [data-theme="light"] .priority-high   { color: #b45309; background: rgba(245, 158, 11, 0.12); border-color: rgba(245, 158, 11, 0.25); }
        [data-theme="light"] .priority-medium { color: #4338ca; background: rgba(99, 102, 241, 0.1); border-color: rgba(99, 102, 241, 0.25); }
        [data-theme="light"] .priority-low    { color: #047857; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.25); }
        [data-theme="light"] .status-completed { color: #047857; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.25); }
        [data-theme="light"] .status-in_progress { color: #1d4ed8; background: rgba(37, 99, 235, 0.1); border-color: rgba(37, 99, 235, 0.25); }
        [data-theme="light"] .status-pending { color: #475569; background: rgba(100, 116, 139, 0.1); border-color: rgba(100, 116, 139, 0.25); }
        [data-theme="light"] .nav-item.active { background: rgba(124, 58, 237, 0.1); color: #7c3aed; border-color: rgba(124, 58, 237, 0.25); }
        [data-theme="light"] .cal-day.today { background: rgba(124, 58, 237, 0.08); }
        [data-theme="light"] .streak-badge { background: rgba(245, 158, 11, 0.1); color: #b45309; border-color: rgba(245, 158, 11, 0.3); }
        [data-theme="light"] ::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.15); }
        [data-theme="light"] ::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.25); }
        [data-theme="light"] .kanban-card { background: #ffffff; }
        [data-theme="light"] .kanban-card:hover { background: #f8fafc; }
        [data-theme="light"] .kanban-count { background: rgba(0, 0, 0, 0.06); color: var(--text-secondary); }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--gradient-main);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.6;
            overflow: hidden;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* ===== APP SHELL ===== */
        .app-shell {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--sidebar-bg);
            backdrop-filter: blur(30px);
            border-right: 1px solid var(--border);
            flex-shrink: 0;
            position: relative;
            z-index: 50;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            background: var(--gradient-accent);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 15px;
            font-weight: 700;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .logo-sub {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 400;
            -webkit-text-fill-color: var(--text-muted);
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 12px;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 16px 8px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 2px;
            position: relative;
        }

        .nav-item:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: rgba(124, 58, 237, 0.15);
            color: #a78bfa;
            border: 1px solid rgba(124, 58, 237, 0.25);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--accent-purple);
            border-radius: 0 3px 3px 0;
        }

        .nav-item i {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            border: 1px solid var(--border);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--gradient-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-email { font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }

        /* ===== TOP HEADER ===== */
        .top-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            border-bottom: 1px solid var(--border);
            background: var(--header-bg);
            backdrop-filter: blur(20px);
            flex-shrink: 0;
        }

        .header-greeting h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .header-greeting p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-clock {
            font-size: 13px;
            color: var(--text-secondary);
            font-variant-numeric: tabular-nums;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            flex: 1;
            overflow-y: auto;
            padding: 28px;
        }

        /* ===== GLASS CARDS ===== */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            transition: all var(--transition);
        }

        .glass-card:hover {
            border-color: var(--border-hover);
            box-shadow: var(--shadow-card), var(--shadow-glow);
        }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all var(--transition);
            text-decoration: none;
            white-space: nowrap;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: var(--gradient-accent);
            color: white;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.5);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
            border-color: var(--border-hover);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-icon {
            padding: 8px;
            border-radius: var(--radius-sm);
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-icon:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
            border-color: var(--border-hover);
        }

        /* ===== FORM ELEMENTS ===== */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            transition: all var(--transition);
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent-purple);
            background: var(--input-bg);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }

        .form-control option { background: var(--input-option-bg); color: var(--text-primary); }

        select.form-control { cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 90px; }

        /* ===== MODAL ===== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box {
            background: var(--modal-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 28px;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0,0,0,0.6), var(--shadow-glow);
            animation: modalIn 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.92) translateY(10px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .modal-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ===== PRIORITY BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .priority-urgent { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .priority-high   { background: rgba(245,158,11, 0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
        .priority-medium { background: rgba(99,102,241, 0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.3); }
        .priority-low    { background: rgba(16,185,129, 0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .status-pending    { background: rgba(148,163,184,0.1); color: #94a3b8; border: 1px solid rgba(148,163,184,0.2); }
        .status-in_progress{ background: rgba(37,99,235,0.15);  color: #60a5fa; border: 1px solid rgba(37,99,235,0.3); }
        .status-completed  { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }

        /* ===== ALERTS ===== */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .alert-error   { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.3); }

        /* ===== STAT CARDS ===== */
        .stat-card {
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-value { font-size: 30px; font-weight: 800; color: var(--text-primary); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-muted); margin-top: 4px; font-weight: 500; }

        /* ===== PAGE TITLE ===== */
        .page-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .page-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        /* ===== SECTION HEADERS ===== */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ===== TASK ITEM ===== */
        .task-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            transition: background var(--transition);
        }

        .task-item:last-child { border-bottom: none; }
        .task-item:hover { background: var(--bg-card-hover); }

        .task-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--border-hover);
            flex-shrink: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition);
            margin-top: 2px;
        }

        .task-checkbox.completed {
            background: var(--accent-green);
            border-color: var(--accent-green);
        }

        .task-title { font-size: 13.5px; font-weight: 500; color: var(--text-primary); }
        .task-title.completed { text-decoration: line-through; color: var(--text-muted); }
        .task-meta { font-size: 11.5px; color: var(--text-muted); margin-top: 3px; display: flex; gap: 8px; align-items: center; }

        /* ===== GRID LAYOUTS ===== */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }

        /* ===== DIVIDER ===== */
        .divider { height: 1px; background: var(--border); margin: 20px 0; }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            color: var(--text-muted);
            text-align: center;
        }
        .empty-state i { margin-bottom: 12px; opacity: 0.4; }
        .empty-state p { font-size: 14px; }

        /* ===== TOAST NOTIFICATION ===== */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ===== TAB BUTTONS ===== */
        .tab-group {
            display: flex;
            gap: 4px;
            background: var(--tab-group-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 4px;
        }
        .tab-btn {
            padding: 7px 16px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            transition: all var(--transition);
            font-family: 'Inter', sans-serif;
        }
        .tab-btn.active {
            background: var(--gradient-accent);
            color: white;
            box-shadow: 0 2px 8px rgba(124,58,237,0.4);
        }

        /* ===== FILTER BAR ===== */
        .filter-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-bar .form-control { width: auto; }

        /* ===== KANBAN ===== */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            align-items: start;
        }
        .kanban-col {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        .kanban-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 700;
        }
        .kanban-count {
            background: rgba(255,255,255,0.08);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .kanban-items { padding: 12px; display: flex; flex-direction: column; gap: 10px; }
        .kanban-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px;
            transition: all var(--transition);
        }
        .kanban-card:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* ===== OVERDUE ===== */
        .overdue-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #f87171;
            font-weight: 600;
        }

        /* ===== CALENDAR ===== */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
        }
        .cal-day-header {
            background: var(--cal-header-bg);
            padding: 10px 6px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .cal-day {
            background: var(--bg-secondary);
            min-height: 100px;
            padding: 8px;
            cursor: pointer;
            transition: background var(--transition);
            position: relative;
        }
        .cal-day:hover { background: var(--bg-card-hover); }
        .cal-day.other-month { opacity: 0.35; }
        .cal-day.today { background: rgba(124,58,237,0.08); }
        .cal-day.today .cal-day-num { background: var(--accent-purple); color: white; }
        .cal-day-num {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }
        .cal-event-dot {
            display: block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10.5px;
            font-weight: 500;
            color: white;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        /* ===== NOTES MASONRY ===== */
        .notes-grid {
            columns: 3;
            column-gap: 18px;
        }
        .note-card {
            break-inside: avoid;
            margin-bottom: 18px;
            border-radius: var(--radius-md);
            padding: 18px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            transition: all var(--transition);
            position: relative;
        }
        .note-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-hover);
            box-shadow: var(--shadow-card), var(--shadow-glow);
        }
        .note-card.pinned { border-color: rgba(124,58,237,0.4); }
        .note-title { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
        .note-content { font-size: 13px; color: var(--text-secondary); line-height: 1.7; white-space: pre-wrap; }
        .note-meta { margin-top: 12px; display: flex; align-items: center; justify-content: space-between; }
        .note-date { font-size: 11px; color: var(--text-muted); }
        .note-actions { display: flex; gap: 6px; opacity: 0; transition: opacity var(--transition); }
        .note-card:hover .note-actions { opacity: 1; }
        .note-accent-bar {
            height: 3px;
            border-radius: 2px;
            margin-bottom: 14px;
        }

        /* ===== PROGRESS PAGE ===== */
        .progress-ring-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }
        .streak-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 30px;
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.3);
            color: #fbbf24;
            font-size: 14px;
            font-weight: 700;
        }

        /* ===== MODAL & OVERLAY ===== */
        .modal-overlay, .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-box {
            background: var(--modal-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 28px;
            width: min(540px, 95vw);
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0,0,0,0.6), var(--shadow-glow);
            animation: modalIn 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-close {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition);
        }
        .modal-close:hover { color: var(--text-primary); background: rgba(255,255,255,0.15); }

        .modal-body { margin-bottom: 20px; }
        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        /* ===== MOBILE HAMBURGER & DRAWER ===== */
        .mobile-menu-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-primary);
            cursor: pointer;
            transition: all var(--transition);
            flex-shrink: 0;
        }
        .mobile-menu-btn:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
        }

        .sidebar-close-btn {
            display: none;
            margin-left: auto;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            color: var(--text-muted);
            cursor: pointer;
            transition: all var(--transition);
        }
        .sidebar-close-btn:hover {
            color: var(--text-primary);
            background: rgba(255,255,255,0.12);
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 9998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* ===== MOBILE BOTTOM DOCK ===== */
        .mobile-bottom-dock {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: var(--dock-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-top: 1px solid var(--border);
            z-index: 900;
            padding: 4px 8px;
            box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.2);
            justify-content: space-around;
            align-items: center;
        }

        .dock-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 10px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 12px;
            transition: all var(--transition);
            flex: 1;
            max-width: 68px;
        }
        .dock-item i { width: 18px; height: 18px; }
        .dock-item.active {
            color: #c4b5fd;
            background: rgba(124, 58, 237, 0.18);
        }
        .dock-item.active i { stroke: #a78bfa; }

        /* ===== RESPONSIVE MEDIA QUERIES ===== */
        @media (max-width: 1200px) {
            .notes-grid { columns: 3; }
        }

        @media (max-width: 1024px) {
            .mobile-menu-btn { display: inline-flex; }
            .sidebar-close-btn { display: inline-flex; }
            .sidebar-backdrop { display: block; }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 270px;
                max-width: 82vw;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 0 50px rgba(0,0,0,0.85);
                z-index: 9999;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .grid-4 { grid-template-columns: repeat(2, 1fr); gap: 14px; }
            .notes-grid { columns: 2; column-gap: 16px; }

            .kanban-board {
                display: grid;
                grid-auto-flow: column;
                grid-auto-columns: minmax(280px, 1fr);
                overflow-x: auto;
                padding-bottom: 14px;
                scroll-snap-type: x mandatory;
                gap: 16px;
                -webkit-overflow-scrolling: touch;
            }
            .kanban-col {
                scroll-snap-align: start;
                min-width: 280px;
            }
        }

        @media (max-width: 768px) {
            .mobile-bottom-dock { display: flex; }
            .page-content { padding: 18px 16px 86px 16px; }

            .top-header {
                padding: 0 16px;
                height: 60px;
            }

            .header-greeting h1 {
                font-size: 15px;
                max-width: 180px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .header-date { display: none; }
            .header-clock { display: none; }

            .grid-2, .grid-3 { grid-template-columns: 1fr; gap: 16px; }

            .filter-bar {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            .filter-bar .form-control {
                flex: 1 1 130px;
                width: auto !important;
            }

            .modal-box {
                padding: 22px 18px;
                border-radius: var(--radius-lg);
            }

            .note-actions {
                opacity: 1 !important;
            }
        }

        @media (max-width: 540px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-card { padding: 12px 14px; }
            .stat-value { font-size: 20px; }
            .stat-icon { width: 36px; height: 36px; }

            .notes-grid { columns: 1; }

            .kanban-board {
                grid-auto-columns: minmax(84vw, 1fr);
            }

            .header-greeting h1 {
                max-width: 140px;
                font-size: 14px;
            }

            .tab-btn {
                padding: 6px 10px;
                font-size: 11px;
            }
        }

        @media (hover: none) {
            .note-actions {
                opacity: 1 !important;
            }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.4s ease forwards; }
        .animate-in:nth-child(1) { animation-delay: 0.05s; }
        .animate-in:nth-child(2) { animation-delay: 0.1s; }
        .animate-in:nth-child(3) { animation-delay: 0.15s; }
        .animate-in:nth-child(4) { animation-delay: 0.2s; }

        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        .gradient-text {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-shell" x-data="{ mobileNavOpen: false }">
    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" :class="{ 'active': mobileNavOpen }" @click="mobileNavOpen = false"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" :class="{ 'mobile-open': mobileNavOpen }">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i data-lucide="zap" style="color:white; width:20px; height:20px;"></i>
            </div>
            <div>
                <div class="logo-text">Smart Life</div>
                <div class="logo-sub">Organizer</div>
            </div>
            <button type="button" class="sidebar-close-btn" @click="mobileNavOpen = false" aria-label="Close navigation menu">
                <i data-lucide="x" style="width:18px;height:18px;"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                Dashboard
            </a>
            <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <i data-lucide="check-square"></i>
                Tasks
            </a>
            <a href="{{ route('calendar.index') }}" class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                Calendar
            </a>
            <a href="{{ route('notes.index') }}" class="nav-item {{ request()->routeIs('notes.*') ? 'active' : '' }}">
                <i data-lucide="sticky-note"></i>
                Notes
            </a>

            <div class="nav-section-label">Account</div>

            <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i data-lucide="user"></i>
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item" style="width:100%; border:none; cursor:pointer; background:transparent; text-align:left;">
                    <i data-lucide="log-out"></i>
                    Logout
                </button>
            </form>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar" style="overflow:hidden; padding:0;">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-email">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                <button type="button" class="mobile-menu-btn" @click="mobileNavOpen = true" aria-label="Open navigation menu">
                    <i data-lucide="menu" style="width:20px;height:20px;"></i>
                </button>
                <div class="header-greeting" style="min-width:0;">
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <p class="header-date" x-data="{}" x-text="new Date().toLocaleDateString('en-US', {weekday:'short', month:'short', day:'numeric'})"></p>
                </div>
            </div>
            <div class="header-actions">
                <!-- Theme Toggle Button -->
                <button type="button" id="header-theme-toggle-btn" class="btn-icon" onclick="window.cycleThemePreference()"
                        title="Toggle theme (Dark / Light / System)"
                        aria-label="Toggle color theme"
                        style="position: relative; width: 34px; height: 34px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                    <span id="header-theme-icon-moon" style="display: none; line-height: 0;"><i data-lucide="moon" style="width: 17px; height: 17px;"></i></span>
                    <span id="header-theme-icon-sun" style="display: none; line-height: 0;"><i data-lucide="sun" style="width: 17px; height: 17px; color: #f59e0b;"></i></span>
                    <span id="header-theme-icon-system" style="display: none; line-height: 0;"><i data-lucide="monitor" style="width: 17px; height: 17px; color: #10b981;"></i></span>
                </button>

                <span class="header-clock" x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'}), 1000); time = new Date().toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'})" x-text="time"></span>
                @yield('header-actions')
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="page-content" id="main-scroll">
            @if(session('success'))
                <div class="alert alert-success animate-in">
                    <i data-lucide="check-circle" style="width:16px;height:16px;"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error animate-in">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- ===== MOBILE BOTTOM NAVIGATION DOCK ===== -->
    <nav class="mobile-bottom-dock">
        <a href="{{ route('dashboard') }}" class="dock-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('tasks.index') }}" class="dock-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <i data-lucide="check-square"></i>
            <span>Tasks</span>
        </a>
        <a href="{{ route('calendar.index') }}" class="dock-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
            <i data-lucide="calendar"></i>
            <span>Calendar</span>
        </a>
        <a href="{{ route('notes.index') }}" class="dock-item {{ request()->routeIs('notes.*') ? 'active' : '' }}">
            <i data-lucide="sticky-note"></i>
            <span>Notes</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="dock-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i data-lucide="user"></i>
            <span>Profile</span>
        </a>
    </nav>
</div>

<script>
    window.updateHeaderThemeIcons = function(pref, resolved) {
        const iconMoon = document.getElementById('header-theme-icon-moon');
        const iconSun = document.getElementById('header-theme-icon-sun');
        const iconSys = document.getElementById('header-theme-icon-system');
        const btn = document.getElementById('header-theme-toggle-btn');
        if (!iconMoon || !iconSun || !iconSys) return;

        iconMoon.style.display = 'none';
        iconSun.style.display = 'none';
        iconSys.style.display = 'none';

        if (pref === 'system') {
            iconSys.style.display = 'inline-block';
            if (btn) btn.title = 'Current Theme: System (' + resolved + ') — Click to switch to Dark';
        } else if (resolved === 'light') {
            iconSun.style.display = 'inline-block';
            if (btn) btn.title = 'Current Theme: Light — Click to switch to System';
        } else {
            iconMoon.style.display = 'inline-block';
            if (btn) btn.title = 'Current Theme: Dark — Click to switch to Light';
        }
    };

    lucide.createIcons();

    (function() {
        const pref = document.documentElement.getAttribute('data-theme-preference') || 'dark';
        const resolved = document.documentElement.getAttribute('data-theme') || 'dark';
        window.updateHeaderThemeIcons(pref, resolved);
    })();
</script>
@stack('scripts')
</body>
</html>
