<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- 1. Account Metadata & Quick Summary -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="settings" style="color: #60a5fa; width: 20px; height: 20px;"></i>
                    Account Settings
                </h2>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                    Review your account status, update security credentials, and manage membership lifecycle.
                </p>
            </div>
            <span class="badge badge-blue" style="font-size: 11px;">Section 2 of 5</span>
        </div>

        <div class="grid-3" style="gap: 16px;">
            <!-- Account Creation Date -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="calendar" style="width: 14px; height: 14px; color: #60a5fa;"></i>
                    Account Creation Date
                </div>
                <div style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-top: 8px;">
                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                    {{ $user->created_at ? $user->created_at->diffForHumans() : 'Recently created' }}
                </div>
            </div>

            <!-- Account Status -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px;">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="check-circle" style="width: 14px; height: 14px; color: #34d399;"></i>
                    Account Status
                </div>
                <div style="font-size: 15px; font-weight: 700; color: #34d399; margin-top: 8px; display: flex; align-items: center; gap: 6px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #34d399; display: inline-block;"></span>
                    Active & Verified
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                    UID #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <!-- Quick Edit Profile Shortcut -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="user-check" style="width: 14px; height: 14px; color: #a78bfa;"></i>
                        Edit Profile
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                        Update your photo, username, or contact information.
                    </div>
                </div>
                <button type="button" @click="currentTab = 'personal'" class="btn btn-ghost btn-sm" style="margin-top: 10px; width: 100%; justify-content: center;">
                    Edit Profile Info →
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Change Email Address -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="mail" style="color: #38bdf8; width: 18px; height: 18px;"></i>
                    Change Email
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">
                    Current email: <code style="color: #93c5fd; background: rgba(59,130,246,0.1); padding: 2px 8px; border-radius: 4px;">{{ $user->email }}</code>
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="_tab" value="account">
            <input type="hidden" name="name" value="{{ $user->name }}">

            <div style="display: flex; gap: 14px; align-items: flex-start; max-width: 580px;">
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control" placeholder="new.email@example.com">
                    @error('email')
                        <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-ghost" style="white-space: nowrap; border-color: rgba(56,189,248,0.3); color: #38bdf8;">
                    Update Email
                </button>
            </div>
            <div class="form-hint" style="font-size: 11.5px; color: var(--text-muted); margin-top: 8px;">
                Changing your email address will require re-verification upon next sign-in.
            </div>
        </form>
    </div>

    <!-- 3. Change Password -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="margin-bottom: 18px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="key-round" style="color: #fbbf24; width: 18px; height: 18px;"></i>
                Change Password
            </h3>
            <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </div>

        @if (session('status') === 'password-updated')
            <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                Your password has been changed successfully.
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" style="max-width: 580px;">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="update_password_current_password" class="form-label">Current Password</label>
                <div style="position: relative;">
                    <i data-lucide="lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                    <input id="update_password_current_password" name="current_password" type="password" class="form-control" style="padding-left: 42px;" placeholder="••••••••" autocomplete="current-password">
                </div>
                @if($errors->updatePassword->get('current_password'))
                    <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div class="grid-2" style="gap: 16px;">
                <div class="form-group">
                    <label for="update_password_password" class="form-label">New Password</label>
                    <div style="position: relative;">
                        <i data-lucide="key" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                        <input id="update_password_password" name="password" type="password" class="form-control" style="padding-left: 42px;" placeholder="Min. 8 characters" autocomplete="new-password">
                    </div>
                    @if($errors->updatePassword->get('password'))
                        <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $errors->updatePassword->first('password') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="update_password_password_confirmation" class="form-label">Confirm New Password</label>
                    <div style="position: relative;">
                        <i data-lucide="shield-check" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" style="padding-left: 42px;" placeholder="Repeat password" autocomplete="new-password">
                    </div>
                    @if($errors->updatePassword->get('password_confirmation'))
                        <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 8px;">
                Update Password
            </button>
        </form>
    </div>

    <!-- 4. Delete Account (Danger Zone) -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg); border-color: rgba(239,68,68,0.25); background: rgba(239,68,68,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #f87171; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                    Delete Account
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; max-width: 580px;">
                    Permanently delete your account, personal data, daily tasks, calendar appointments, and saved notes. This action cannot be reversed.
                </p>
            </div>
            <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-account-modal').style.display='flex'">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                Delete Account
            </button>
        </div>

        @if($errors->userDeletion->isNotEmpty())
            <div style="color: #f87171; font-size: 12px; margin-top: 12px; padding: 8px 12px; background: rgba(239,68,68,0.1); border-radius: 6px;">
                {{ $errors->userDeletion->first() }}
            </div>
        @endif
    </div>

</div>

<!-- Delete Account Confirmation Modal -->
<div id="delete-account-modal" class="modal-backdrop" style="display: none;">
    <div class="modal-box" style="border-color: rgba(239,68,68,0.3);">
        <div class="modal-header">
            <div class="modal-title" style="color: #f87171; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="alert-octagon" style="width: 20px; height: 20px;"></i>
                Confirm Account Deletion
            </div>
            <button type="button" class="modal-close" onclick="document.getElementById('delete-account-modal').style.display='none'">✕</button>
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <div class="modal-body">
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 18px;">
                    Are you sure you want to delete your account? All of your tasks, events, notes, and productivity history will be permanently wiped.
                </p>

                <div class="form-group">
                    <label for="delete_password" class="form-label">Enter your password to confirm:</label>
                    <input id="delete_password" name="password" type="password" required class="form-control" placeholder="••••••••">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('delete-account-modal').style.display='none'">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger">
                    Permanently Delete Everything
                </button>
            </div>
        </form>
    </div>
</div>
