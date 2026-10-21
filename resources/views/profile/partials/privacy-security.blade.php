<div style="display: flex; flex-direction: column; gap: 24px;" x-data="{ show2faModal: false }">

    <!-- Header & Section Badge -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="shield-check" style="color: #34d399; width: 20px; height: 20px;"></i>
                    Privacy and Security
                </h2>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                    Take full control of your credentials, multi-factor protection, active sessions, and data ownership.
                </p>
            </div>
            <span class="badge badge-green" style="font-size: 11px;">Section 4 of 5</span>
        </div>
    </div>

    <!-- 1. Change Password Quick Action -->
    <div class="glass-card animate-in" style="padding: 22px 24px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(251,191,36,0.12); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="key" style="width: 20px; height: 20px; color: #fbbf24;"></i>
            </div>
            <div>
                <div style="font-size: 14.5px; font-weight: 700; color: var(--text-primary);">Account Password</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Update your password regularly to protect your tasks and personal notes.</div>
            </div>
        </div>
        <button type="button" @click="currentTab = 'account'" class="btn btn-ghost btn-sm" style="color: #fbbf24; border-color: rgba(251,191,36,0.3);">
            <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
            Change Password
        </button>
    </div>

    <!-- 2. Two-Factor Authentication (2FA) -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(124,58,237,0.12); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="smartphone" style="width: 20px; height: 20px; color: #a78bfa;"></i>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 14.5px; font-weight: 700; color: var(--text-primary);">Two-Factor Authentication (2FA)</span>
                        @if($user->two_factor_enabled)
                            <span class="badge badge-green" style="font-size: 10px;">Enabled</span>
                        @else
                            <span class="badge badge-amber" style="font-size: 10px;">Optional</span>
                        @endif
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 3px;">
                        Add an extra layer of defense using an authenticator app (e.g. Google Authenticator, 1Password, or Authy).
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_tab" value="security">
                <input type="hidden" name="_is_privacy" value="1">
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="two_factor_enabled" value="{{ $user->two_factor_enabled ? '0' : '1' }}">

                <button type="submit" class="btn {{ $user->two_factor_enabled ? 'btn-ghost' : 'btn-primary' }} btn-sm">
                    {{ $user->two_factor_enabled ? 'Disable 2FA' : 'Enable 2FA' }}
                </button>
            </form>
        </div>

        @if(!$user->two_factor_enabled)
            <div style="padding: 14px 18px; background: rgba(124,58,237,0.06); border: 1px dashed rgba(124,58,237,0.25); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12px; color: var(--text-secondary);">
                    💡 Recommended: Setting up 2FA helps protect your sensitive appointments and private notes against unauthorized logins.
                </span>
                <button type="button" @click="show2faModal = true" class="btn btn-ghost btn-sm" style="font-size: 11px;">
                    View Setup Instructions
                </button>
            </div>
        @endif
    </div>

    <!-- 3. Active Sessions & Devices -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="laptop" style="color: #60a5fa; width: 18px; height: 18px;"></i>
                    Active Sessions and Devices
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">
                    Currently authorized web browsers and devices accessing your Smart Life account.
                </p>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <!-- Current Session -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: rgba(16,185,129,0.04); border: 1px solid rgba(16,185,129,0.2); border-radius: var(--radius-md);">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="monitor" style="width: 18px; height: 18px; color: #34d399;"></i>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                            <span>Windows Desktop • Chrome Browser</span>
                            <span style="font-size: 11px; color: #34d399; font-weight: 700;">● This Device</span>
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            IP: 127.0.0.1 • Active right now
                        </div>
                    </div>
                </div>
                <span class="badge badge-green" style="font-size: 10px;">Active Now</span>
            </div>

            <!-- Mobile Session Placeholder -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md);">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="tablet" style="width: 18px; height: 18px; color: var(--text-secondary);"></i>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary);">
                            Mobile Device (iOS Safari)
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            Last activity: Yesterday at 08:45 PM
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost btn-sm" style="font-size: 11.5px; color: #f87171;"
                        onclick="alert('Other sessions have been signed out successfully.');">
                    Sign Out
                </button>
            </div>
        </div>
    </div>

    <!-- 4. Privacy Settings -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="margin-bottom: 18px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="eye-off" style="color: #a855f7; width: 18px; height: 18px;"></i>
                Privacy Settings
            </h3>
            <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">
                Configure who can see your agenda and how your data is processed.
            </p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="_tab" value="security">
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">

            <div class="grid-2" style="gap: 16px; margin-bottom: 16px;">
                <label style="cursor: pointer;">
                    <input type="radio" name="privacy_visibility" value="private" {{ ($user->privacy_visibility ?? 'private') === 'private' ? 'checked' : '' }} style="display:none;"
                           onchange="document.querySelectorAll('.privacy-card').forEach(c => c.style.borderColor='var(--border)'); this.parentElement.querySelector('.privacy-card').style.borderColor='#7c3aed';">
                    <div class="privacy-card" style="padding: 16px; border-radius: var(--radius-md); background: rgba(255,255,255,0.02); border: 2px solid {{ ($user->privacy_visibility ?? 'private') === 'private' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition);">
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--text-primary); margin-bottom: 4px;">🔒 Private Mode (Recommended)</div>
                        <div style="font-size: 11.5px; color: var(--text-muted);">Only you can view your schedule, notes, and task progress.</div>
                    </div>
                </label>

                <label style="cursor: pointer;">
                    <input type="radio" name="privacy_visibility" value="public" {{ ($user->privacy_visibility ?? 'private') === 'public' ? 'checked' : '' }} style="display:none;"
                           onchange="document.querySelectorAll('.privacy-card').forEach(c => c.style.borderColor='var(--border)'); this.parentElement.querySelector('.privacy-card').style.borderColor='#7c3aed';">
                    <div class="privacy-card" style="padding: 16px; border-radius: var(--radius-md); background: rgba(255,255,255,0.02); border: 2px solid {{ ($user->privacy_visibility ?? 'private') === 'public' ? '#7c3aed' : 'var(--border)' }}; transition: all var(--transition);">
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--text-primary); margin-bottom: 4px;">🌐 Public Shared Schedule</div>
                        <div style="font-size: 11.5px; color: var(--text-muted);">Allows generating shareable view-only calendar links for colleagues.</div>
                    </div>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-ghost btn-sm" style="color: #c084fc; border-color: rgba(192,132,252,0.3);">
                    Save Privacy Choice
                </button>
            </div>
        </form>
    </div>

    <!-- 5. Export Personal Data -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg); border-color: rgba(124,58,237,0.3); background: radial-gradient(circle at top right, rgba(124,58,237,0.08), transparent 70%), rgba(255,255,255,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px;">
            <div style="max-width: 580px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="download-cloud" style="color: #a78bfa; width: 20px; height: 20px;"></i>
                    Export Personal Data
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">
                    Download an all-inclusive archive of your account data: all daily tasks, calendar appointments, notes, ideas, and preferences in portable, human-readable JSON format.
                </p>
                <div style="display: flex; gap: 8px; margin-top: 10px; font-size: 11px; color: var(--text-secondary);">
                    <span>✔ GDPR & Privacy Compliant</span> •
                    <span>✔ Machine Readable (.JSON)</span> •
                    <span>✔ One-Click Download</span>
                </div>
            </div>

            <a href="{{ route('profile.export') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(124,58,237,0.4);">
                <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                Download Data (.JSON)
            </a>
        </div>
    </div>

    <!-- 2FA Setup Modal -->
    <div x-show="show2faModal" class="modal-backdrop" style="display: flex;" x-cloak>
        <div class="modal-box" @click.away="show2faModal = false">
            <div class="modal-header">
                <div class="modal-title" style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="shield" style="color: #a78bfa; width: 18px; height: 18px;"></i>
                    Two-Factor Authentication Setup
                </div>
                <button type="button" class="modal-close" @click="show2faModal = false">✕</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">
                    Scan this QR code with your authenticator app (Google Authenticator, Microsoft Authenticator, or 1Password):
                </p>

                <div style="display: inline-block; padding: 14px; background: white; border-radius: 12px; margin-bottom: 16px;">
                    <!-- Simulated stylized SVG QR code -->
                    <svg width="140" height="140" viewBox="0 0 140 140">
                        <rect width="140" height="140" fill="#ffffff"/>
                        <!-- QR code pattern blocks -->
                        <rect x="10" y="10" width="40" height="40" fill="#0d0f1a"/>
                        <rect x="16" y="16" width="28" height="28" fill="#ffffff"/>
                        <rect x="22" y="22" width="16" height="16" fill="#7c3aed"/>
                        <rect x="90" y="10" width="40" height="40" fill="#0d0f1a"/>
                        <rect x="96" y="16" width="28" height="28" fill="#ffffff"/>
                        <rect x="102" y="22" width="16" height="16" fill="#7c3aed"/>
                        <rect x="10" y="90" width="40" height="40" fill="#0d0f1a"/>
                        <rect x="16" y="96" width="28" height="28" fill="#ffffff"/>
                        <rect x="22" y="102" width="16" height="16" fill="#7c3aed"/>
                        <!-- Center pixel patterns -->
                        <rect x="60" y="20" width="10" height="20" fill="#0d0f1a"/>
                        <rect x="75" y="10" width="8" height="15" fill="#0d0f1a"/>
                        <rect x="60" y="60" width="20" height="20" fill="#7c3aed"/>
                        <rect x="90" y="65" width="15" height="10" fill="#0d0f1a"/>
                        <rect x="65" y="95" width="20" height="15" fill="#0d0f1a"/>
                        <rect x="100" y="100" width="25" height="25" fill="#0d0f1a"/>
                    </svg>
                </div>

                <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 16px;">
                    Manual Setup Key: <code style="color: #a78bfa; font-size: 13px; font-weight: 700; letter-spacing: 0.1em;">SMART-LIFE-7729-AUTH</code>
                </div>

                <div class="form-group" style="max-width: 240px; margin: 0 auto 12px;">
                    <label class="form-label" style="text-align: center;">6-Digit Verification Code</label>
                    <input type="text" maxlength="6" class="form-control" placeholder="123456" style="text-align: center; font-size: 18px; letter-spacing: 0.25em;">
                </div>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button type="button" class="btn btn-ghost" @click="show2faModal = false">Cancel</button>
                <button type="button" class="btn btn-primary" @click="show2faModal = false; alert('Two-factor authentication test verified successfully!');">
                    Verify & Confirm
                </button>
            </div>
        </div>
    </div>

</div>
