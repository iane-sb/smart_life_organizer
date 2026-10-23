<div class="glass-card animate-in" style="padding: 28px; border-radius: var(--radius-lg);">
    <div
        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 18px;">
        <div>
            <h2
                style="font-size: 18px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="sliders" style="color: #c084fc; width: 20px; height: 20px;"></i>
                Application Preferences
            </h2>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                Tailor theme appearance, locale, date conventions, notifications, and default starting views.
            </p>
        </div>
        <span class="badge badge-purple" style="font-size: 11px;">Section 3 of 5</span>
    </div>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="_tab" value="preferences">
        <input type="hidden" name="_is_preferences" value="1">
        <input type="hidden" name="name" value="{{ $user->name }}">
        <input type="hidden" name="email" value="{{ $user->email }}">

        <!-- 1. Color Theme (Light / Dark Mode) -->
        <div style="margin-bottom: 28px;">
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                <label class="form-label" style="margin-bottom: 0; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="sun-moon" style="width: 15px; height: 15px; color: #c084fc;"></i>
                    Color Theme (Light / Dark Mode)
                </label>
                <span id="theme-status-indicator"
                    style="font-size: 11.5px; color: #a78bfa; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <span
                        style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                    <span id="theme-status-text">Active: Dark Glass</span>
                </span>
            </div>

            <div class="grid-3" style="gap: 16px;">
                <!-- Dark Mode -->
                <label style="cursor: pointer; position: relative;" onclick="window.selectThemeOption('dark')">
                    <input type="radio" name="theme_preference" id="theme-radio-dark" value="dark"
                        onclick="event.stopPropagation()"
                        {{ ($user->theme_preference ?? 'dark') === 'dark' ? 'checked' : '' }} style="display:none;">
                    <div id="theme-card-dark" class="theme-card" data-theme-val="dark"
                        style="padding: 18px; border-radius: var(--radius-md); background: var(--bg-card); border: 2px solid {{ ($user->theme_preference ?? 'dark') === 'dark' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition); height: 100%; display: flex; flex-direction: column;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 22px;">🌙</span>
                                <span style="font-weight: 700; font-size: 14.5px; color: var(--text-primary);">Dark
                                    Glass</span>
                            </div>
                            <span class="theme-badge badge badge-purple" data-default="Recommended"
                                style="font-size: 10px;">
                                {{ ($user->theme_preference ?? 'dark') === 'dark' ? '✓ Active' : 'Recommended' }}
                            </span>
                        </div>

                        <!-- Miniature Preview Widget -->
                        <div
                            style="background: #0d0f1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 10px; margin-bottom: 12px; pointer-events: none;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 6px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 8px;">
                                <div style="width: 30px; height: 6px; border-radius: 3px; background: #7c3aed;"></div>
                                <div
                                    style="width: 14px; height: 6px; border-radius: 3px; background: rgba(255,255,255,0.2);">
                                </div>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <div
                                    style="flex: 1; height: 26px; border-radius: 4px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                                </div>
                                <div
                                    style="flex: 1; height: 26px; border-radius: 4px; background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.3);">
                                </div>
                            </div>
                        </div>

                        <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5; flex: 1;">
                            Deep obsidian canvas with neon purple gradients and frosted glass reflections.
                        </div>
                    </div>
                </label>

                <!-- Light Mode -->
                <label style="cursor: pointer; position: relative;" onclick="window.selectThemeOption('light')">
                    <input type="radio" name="theme_preference" id="theme-radio-light" value="light"
                        onclick="event.stopPropagation()"
                        {{ ($user->theme_preference ?? 'dark') === 'light' ? 'checked' : '' }} style="display:none;">
                    <div id="theme-card-light" class="theme-card" data-theme-val="light"
                        style="padding: 18px; border-radius: var(--radius-md); background: var(--bg-card); border: 2px solid {{ ($user->theme_preference ?? 'dark') === 'light' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition); height: 100%; display: flex; flex-direction: column;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 22px;">☀️</span>
                                <span style="font-weight: 700; font-size: 14.5px; color: var(--text-primary);">Light
                                    Glass</span>
                            </div>
                            <span class="theme-badge badge badge-blue" data-default="Daylight" style="font-size: 10px;">
                                {{ ($user->theme_preference ?? 'dark') === 'light' ? '✓ Active' : 'Daylight' }}
                            </span>
                        </div>

                        <!-- Miniature Preview Widget -->
                        <div
                            style="background: #f8fafc; border: 1px solid rgba(203,213,225,0.8); border-radius: 8px; padding: 10px; margin-bottom: 12px; pointer-events: none;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 6px; border-bottom: 1px solid rgba(203,213,225,0.6); margin-bottom: 8px;">
                                <div style="width: 30px; height: 6px; border-radius: 3px; background: #2563eb;"></div>
                                <div style="width: 14px; height: 6px; border-radius: 3px; background: #cbd5e1;"></div>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <div
                                    style="flex: 1; height: 26px; border-radius: 4px; background: #ffffff; border: 1px solid rgba(203,213,225,0.8);">
                                </div>
                                <div
                                    style="flex: 1; height: 26px; border-radius: 4px; background: rgba(37,99,235,0.1); border: 1px solid rgba(37,99,235,0.25);">
                                </div>
                            </div>
                        </div>

                        <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5; flex: 1;">
                            Bright frosted acrylic surfaces designed for crisp daylight focus and high contrast.
                        </div>
                    </div>
                </label>

                <!-- System Mode -->
                <label style="cursor: pointer; position: relative;" onclick="window.selectThemeOption('system')">
                    <input type="radio" name="theme_preference" id="theme-radio-system" value="system"
                        onclick="event.stopPropagation()"
                        {{ ($user->theme_preference ?? 'dark') === 'system' ? 'checked' : '' }} style="display:none;">
                    <div id="theme-card-system" class="theme-card" data-theme-val="system"
                        style="padding: 18px; border-radius: var(--radius-md); background: var(--bg-card); border: 2px solid {{ ($user->theme_preference ?? 'dark') === 'system' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition); height: 100%; display: flex; flex-direction: column;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 22px;">💻</span>
                                <span style="font-weight: 700; font-size: 14.5px; color: var(--text-primary);">System
                                    Match</span>
                            </div>
                            <span class="theme-badge badge badge-green" data-default="Sync OS"
                                style="font-size: 10px;">
                                {{ ($user->theme_preference ?? 'dark') === 'system' ? '✓ Active' : 'Sync OS' }}
                            </span>
                        </div>

                        <!-- Miniature Preview Widget (Split) -->
                        <div
                            style="background: linear-gradient(90deg, #0d0f1a 50%, #f8fafc 50%); border: 1px solid var(--border); border-radius: 8px; padding: 10px; margin-bottom: 12px; pointer-events: none;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 6px; border-bottom: 1px solid rgba(124,58,237,0.25); margin-bottom: 8px;">
                                <div style="width: 30px; height: 6px; border-radius: 3px; background: #7c3aed;"></div>
                                <div
                                    style="width: 14px; height: 6px; border-radius: 3px; background: rgba(37,99,235,0.7);">
                                </div>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <div
                                    style="flex: 1; height: 26px; border-radius: 4px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                                </div>
                                <div
                                    style="flex: 1; height: 26px; border-radius: 4px; background: #ffffff; border: 1px solid rgba(203,213,225,0.8);">
                                </div>
                            </div>
                        </div>

                        <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5; flex: 1;">
                            Automatically syncs with your desktop or mobile operating system appearance.
                        </div>
                    </div>
                </label>
            </div>

            <div id="theme-toast-msg"
                style="display: none; margin-top: 14px; padding: 10px 16px; border-radius: var(--radius-sm); background: rgba(124,58,237,0.12); border: 1px solid rgba(124,58,237,0.25); font-size: 12.5px; color: var(--text-primary); align-items: center; gap: 8px;">
                <i data-lucide="sparkles" style="width: 15px; height: 15px; color: #a78bfa; flex-shrink: 0;"></i>
                <span id="theme-toast-text">Theme switched! Click "Save Preferences" below to permanently save to your
                    account.</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- 2. Language & 3. Date & Time Format -->
        <div class="grid-3" style="gap: 20px; margin-bottom: 28px;">
            <!-- Language -->
            <div class="form-group">
                <label for="language" class="form-label" style="display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="globe" style="width: 14px; height: 14px; color: #38bdf8;"></i>
                    Language
                </label>
                <select id="language" name="language" class="form-control">
                    <option value="en" {{ ($user->language ?? 'en') === 'en' ? 'selected' : '' }}>🇺🇸 English
                        (US)</option>
                    <option value="es" {{ ($user->language ?? 'en') === 'es' ? 'selected' : '' }}>🇪🇸 Español
                        (Spanish)</option>
                    <option value="fr" {{ ($user->language ?? 'en') === 'fr' ? 'selected' : '' }}>🇫🇷 Français
                        (French)</option>
                    <option value="de" {{ ($user->language ?? 'en') === 'de' ? 'selected' : '' }}>🇩🇪 Deutsch
                        (German)</option>
                    <option value="ja" {{ ($user->language ?? 'en') === 'ja' ? 'selected' : '' }}>🇯🇵 日本語
                        (Japanese)</option>
                </select>
                <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                    Language applied across navigation, dates, and buttons.
                </div>
            </div>

            <!-- Date Format -->
            <div class="form-group">
                <label for="date_format" class="form-label" style="display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="calendar" style="width: 14px; height: 14px; color: #fbbf24;"></i>
                    Date Format
                </label>
                <select id="date_format" name="date_format" class="form-control">
                    <option value="Y-m-d" {{ ($user->date_format ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>
                        YYYY-MM-DD (e.g. {{ date('Y-m-d') }})</option>
                    <option value="m/d/Y" {{ ($user->date_format ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : '' }}>
                        MM/DD/YYYY (e.g. {{ date('m/d/Y') }})</option>
                    <option value="d/m/Y" {{ ($user->date_format ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : '' }}>
                        DD/MM/YYYY (e.g. {{ date('d/m/Y') }})</option>
                </select>
                <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                    Applied to calendar grid and task due dates.
                </div>
            </div>

            <!-- Time Format -->
            <div class="form-group">
                <label for="time_format" class="form-label" style="display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="clock" style="width: 14px; height: 14px; color: #34d399;"></i>
                    Time Format
                </label>
                <select id="time_format" name="time_format" class="form-control">
                    <option value="12h" {{ ($user->time_format ?? '12h') === '12h' ? 'selected' : '' }}>12-Hour
                        (e.g. 02:30 PM)</option>
                    <option value="24h" {{ ($user->time_format ?? '12h') === '24h' ? 'selected' : '' }}>24-Hour
                        (e.g. 14:30)</option>
                </select>
                <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                    Format displayed in top header clock and event hours.
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- 4. Notification Preferences -->
        <div style="margin-bottom: 28px;">
            <label class="form-label" style="margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="bell" style="width: 15px; height: 15px; color: #f59e0b;"></i>
                Notification Preferences
            </label>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <!-- Email Notifications -->
                <label
                    style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-md); cursor: pointer;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div
                            style="width: 36px; height: 36px; border-radius: 8px; background: rgba(59,130,246,0.12); display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="mail" style="width: 18px; height: 18px; color: #60a5fa;"></i>
                        </div>
                        <div>
                            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">Email
                                Summaries & Digests</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Receive weekly productivity
                                insights and agenda highlights.</div>
                        </div>
                    </div>
                    <input type="checkbox" name="notify_email" value="1"
                        {{ $user->notify_email ?? true ? 'checked' : '' }}
                        style="width: 18px; height: 18px; accent-color: #7c3aed; cursor: pointer;">
                </label>

                <!-- Task Reminders -->
                <label
                    style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-md); cursor: pointer;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div
                            style="width: 36px; height: 36px; border-radius: 8px; background: rgba(239,68,68,0.12); display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="alert-circle" style="width: 18px; height: 18px; color: #f87171;"></i>
                        </div>
                        <div>
                            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">Task
                                Reminders & Deadline Alerts</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Get notified when urgent tasks are
                                approaching their due date.</div>
                        </div>
                    </div>
                    <input type="checkbox" name="notify_tasks" value="1"
                        {{ $user->notify_tasks ?? true ? 'checked' : '' }}
                        style="width: 18px; height: 18px; accent-color: #7c3aed; cursor: pointer;">
                </label>

                <!-- Calendar Alerts -->
                <label
                    style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-md); cursor: pointer;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div
                            style="width: 36px; height: 36px; border-radius: 8px; background: rgba(16,185,129,0.12); display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="calendar" style="width: 18px; height: 18px; color: #34d399;"></i>
                        </div>
                        <div>
                            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">Calendar
                                Appointments & Study Shifts</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Alerts 15 minutes before scheduled
                                appointments and work shifts.</div>
                        </div>
                    </div>
                    <input type="checkbox" name="notify_events" value="1"
                        {{ $user->notify_events ?? true ? 'checked' : '' }}
                        style="width: 18px; height: 18px; accent-color: #7c3aed; cursor: pointer;">
                </label>
            </div>
        </div>

        <div class="divider"></div>

        <!-- 5. Default Dashboard View -->
        <div style="margin-bottom: 24px;">
            <label class="form-label" style="margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="layout" style="width: 15px; height: 15px; color: #38bdf8;"></i>
                Default Dashboard View
            </label>

            <div class="grid-3" style="gap: 16px;">
                <label style="cursor: pointer;">
                    <input type="radio" name="default_dashboard_view" value="overview"
                        {{ ($user->default_dashboard_view ?? 'overview') === 'overview' ? 'checked' : '' }}
                        style="display:none;"
                        onchange="document.querySelectorAll('.view-card').forEach(c => c.style.borderColor='var(--border)'); this.parentElement.querySelector('.view-card').style.borderColor='#7c3aed';">
                    <div class="view-card"
                        style="padding: 16px; border-radius: var(--radius-md); background: var(--bg-card); border: 2px solid {{ ($user->default_dashboard_view ?? 'overview') === 'overview' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition);">
                        <div
                            style="font-weight: 700; font-size: 13.5px; color: var(--text-primary); margin-bottom: 4px;">
                            📊 All-in-One Overview</div>
                        <div style="font-size: 11.5px; color: var(--text-muted);">Charts, today's tasks, weekly streak,
                            and upcoming appointments.</div>
                    </div>
                </label>

                <label style="cursor: pointer;">
                    <input type="radio" name="default_dashboard_view" value="tasks"
                        {{ ($user->default_dashboard_view ?? 'overview') === 'tasks' ? 'checked' : '' }}
                        style="display:none;"
                        onchange="document.querySelectorAll('.view-card').forEach(c => c.style.borderColor='var(--border)'); this.parentElement.querySelector('.view-card').style.borderColor='#7c3aed';">
                    <div class="view-card"
                        style="padding: 16px; border-radius: var(--radius-md); background: var(--bg-card); border: 2px solid {{ ($user->default_dashboard_view ?? 'overview') === 'tasks' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition);">
                        <div
                            style="font-weight: 700; font-size: 13.5px; color: var(--text-primary); margin-bottom: 4px;">
                            📋 Task Focused</div>
                        <div style="font-size: 11.5px; color: var(--text-muted);">Quickly access your Kanban board and
                            priority task checklist.</div>
                    </div>
                </label>

                <label style="cursor: pointer;">
                    <input type="radio" name="default_dashboard_view" value="calendar"
                        {{ ($user->default_dashboard_view ?? 'overview') === 'calendar' ? 'checked' : '' }}
                        style="display:none;"
                        onchange="document.querySelectorAll('.view-card').forEach(c => c.style.borderColor='var(--border)'); this.parentElement.querySelector('.view-card').style.borderColor='#7c3aed';">
                    <div class="view-card"
                        style="padding: 16px; border-radius: var(--radius-md); background: var(--bg-card); border: 2px solid {{ ($user->default_dashboard_view ?? 'overview') === 'calendar' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition);">
                        <div
                            style="font-weight: 700; font-size: 13.5px; color: var(--text-primary); margin-bottom: 4px;">
                            📅 Calendar Focused</div>
                        <div style="font-size: 11.5px; color: var(--text-muted);">Direct entry to weekly schedule and
                            upcoming timetable.</div>
                    </div>
                </label>
            </div>
        </div>

        <div
            style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 18px; border-top: 1px solid var(--border);">
            <button type="submit" class="btn btn-primary"
                style="display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                Save Preferences
            </button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        // ===== Theme Selection Logic =====
        window.selectThemeOption = function(pref) {
            // 1. Check corresponding radio
            var radio = document.getElementById('theme-radio-' + pref);
            if (radio) {
                radio.checked = true;
            }

            // 2. Apply theme dynamically across the entire app (instant preview)
            if (typeof window.applyThemePreference === 'function') {
                window.applyThemePreference(pref);
            }

            // 3. Update the visual state of theme cards
            window.updateThemeCardsUI(pref);

            // 4. Show helpful feedback toast
            var toast = document.getElementById('theme-toast-msg');
            var text = document.getElementById('theme-toast-text');
            if (toast && text) {
                var labelMap = {
                    'dark': 'Dark Glassmorphism',
                    'light': 'Light Glass',
                    'system': 'System Match'
                };
                text.textContent = 'Switched to ' + (labelMap[pref] || pref) +
                    '! Click "Save Preferences" below to permanently save to your account.';
                toast.style.display = 'flex';
                // Re-render any new lucide icons in the toast
                if (window.lucide) lucide.createIcons();
            }
        };

        window.updateThemeCardsUI = function(pref) {
            var cards = document.querySelectorAll('.theme-card[data-theme-val]');
            cards.forEach(function(card) {
                var val = card.getAttribute('data-theme-val');
                var badge = card.querySelector('.theme-badge');
                if (val === pref) {
                    card.style.borderColor = '#7c3aed';
                    card.style.boxShadow = '0 0 20px rgba(124, 58, 237, 0.28)';
                    if (badge) {
                        badge.textContent = '✓ Active';
                        badge.className = 'theme-badge badge badge-purple';
                    }
                } else {
                    card.style.borderColor = 'var(--border)';
                    card.style.boxShadow = 'none';
                    if (badge) {
                        badge.textContent = badge.getAttribute('data-default') || '';
                        if (val === 'light') badge.className = 'theme-badge badge badge-blue';
                        else if (val === 'system') badge.className = 'theme-badge badge badge-green';
                        else badge.className = 'theme-badge badge badge-purple';
                    }
                }
            });

            // Update the status indicator text
            var statusText = document.getElementById('theme-status-text');
            if (statusText) {
                var labelMap = {
                    'dark': 'Active: Dark Glass',
                    'light': 'Active: Light Glass',
                    'system': 'Active: System Match'
                };
                statusText.textContent = labelMap[pref] || ('Active: ' + pref);
            }
        };

        // Initialize card UI state from current preference on page load
        (function initThemeCards() {
            var current = document.documentElement.getAttribute('data-theme-preference') ||
                '{{ $user->theme_preference ?? 'dark' }}';
            var radio = document.getElementById('theme-radio-' + current);
            if (radio) radio.checked = true;
            window.updateThemeCardsUI(current);
        })();

        // Listen to theme changes from the header toggle button so cards stay in sync
        window.addEventListener('themechanged', function(e) {
            if (e.detail && e.detail.preference) {
                var radio = document.getElementById('theme-radio-' + e.detail.preference);
                if (radio) radio.checked = true;
                window.updateThemeCardsUI(e.detail.preference);

                // Also show the toast when toggled from header
                var toast = document.getElementById('theme-toast-msg');
                var text = document.getElementById('theme-toast-text');
                if (toast && text) {
                    var labelMap = {
                        'dark': 'Dark Glassmorphism',
                        'light': 'Light Glass',
                        'system': 'System Match'
                    };
                    text.textContent = 'Switched to ' + (labelMap[e.detail.preference] || e.detail.preference) +
                        '! Click "Save Preferences" to persist.';
                    toast.style.display = 'flex';
                    if (window.lucide) lucide.createIcons();
                }
            }
        });
    </script>
@endpush
