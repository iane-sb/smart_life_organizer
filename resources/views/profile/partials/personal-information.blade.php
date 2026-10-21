<div class="glass-card animate-in" style="padding: 28px; border-radius: var(--radius-lg);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 18px;">
        <div>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user" style="color: #a78bfa; width: 20px; height: 20px;"></i>
                Personal Information
            </h2>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                Manage your public persona, display identity, and personal contact details.
            </p>
        </div>
        <span class="badge badge-purple" style="font-size: 11px;">Section 1 of 5</span>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <input type="hidden" name="_tab" value="personal">

        <!-- 1. Profile Photo / Avatar -->
        <div style="display: flex; align-items: center; gap: 24px; padding: 20px; background: rgba(255,255,255,0.02); border: 1px dashed var(--border); border-radius: var(--radius-md); margin-bottom: 28px;">
            <div style="position: relative;">
                <img id="avatar-preview"
                     src="{{ $user->avatar_url }}"
                     alt="{{ $user->name }}"
                     style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid #7c3aed; box-shadow: 0 0 24px rgba(124,58,237,0.35);">
                <div style="position: absolute; bottom: 0; right: 0; width: 26px; height: 26px; background: #7c3aed; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid var(--bg-primary);">
                    <i data-lucide="camera" style="width: 13px; height: 13px; color: white;"></i>
                </div>
            </div>

            <div style="flex: 1;">
                <div style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Profile Photo or Avatar</div>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                    Upload a custom JPG, PNG, or WEBP photo (max 2MB), or choose a preset style.
                </p>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <label class="btn btn-ghost btn-sm" style="cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="upload" style="width: 13px; height: 13px;"></i>
                        Upload Photo
                        <input type="file" name="avatar_file" accept="image/*" style="display: none;"
                               onchange="if(this.files[0]) { document.getElementById('avatar-preview').src = URL.createObjectURL(this.files[0]); }">
                    </label>
                    <input type="hidden" name="avatar" id="avatar-url-input" value="{{ $user->avatar }}">
                    <button type="button" class="btn btn-ghost btn-sm" style="font-size: 11px;"
                            onclick="const seed = Math.random().toString(36).substring(7); const url = 'https://api.dicebear.com/7.x/bottts/svg?seed=' + seed; document.getElementById('avatar-preview').src = url; document.getElementById('avatar-url-input').value = url;">
                        <i data-lucide="sparkles" style="width: 12px; height: 12px; color: #a78bfa;"></i>
                        Randomize Avatar
                    </button>
                    @if($user->avatar)
                        <button type="button" class="btn btn-ghost btn-sm" style="font-size: 11px; color: #f87171;"
                                onclick="document.getElementById('avatar-url-input').value = ''; document.getElementById('avatar-preview').src = 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=7c3aed&color=ffffff&bold=true&rounded=true&size=128';">
                            Reset to Default
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid-2" style="gap: 20px;">
            <!-- 2. Full Name -->
            <div class="form-group">
                <label for="name" class="form-label" style="display: flex; justify-content: space-between;">
                    <span>Full Name <span style="color: #f87171;">*</span></span>
                </label>
                <div style="position: relative;">
                    <i data-lucide="user" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                    <input id="name" name="name" type="text" class="form-control" style="padding-left: 42px;"
                           value="{{ old('name', $user->name) }}" required placeholder="e.g. Alex Johnson">
                </div>
                @error('name')
                    <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- 3. Username -->
            <div class="form-group">
                <label for="username" class="form-label">
                    <span>Username</span>
                </label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-weight: 600; font-size: 14px; pointer-events: none;">@</span>
                    <input id="username" name="username" type="text" class="form-control" style="padding-left: 36px;"
                           value="{{ old('username', $user->username ?? $user->username_display) }}" placeholder="alexjohnson">
                </div>
                <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                    Unique handle used for notifications and shared agenda views.
                </div>
                @error('username')
                    <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid-2" style="gap: 20px; margin-top: 4px;">
            <!-- 4. Email Address -->
            <div class="form-group">
                <label for="email" class="form-label" style="display: flex; justify-content: space-between;">
                    <span>Email Address <span style="color: #f87171;">*</span></span>
                    @if($user->hasVerifiedEmail())
                        <span style="color: #34d399; font-size: 11px; display: inline-flex; align-items: center; gap: 3px;">
                            <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Verified
                        </span>
                    @endif
                </label>
                <div style="position: relative;">
                    <i data-lucide="mail" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                    <input id="email" name="email" type="email" class="form-control" style="padding-left: 42px;"
                           value="{{ old('email', $user->email) }}" required placeholder="you@example.com">
                </div>
                @error('email')
                    <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- 5. Phone Number (Optional) -->
            <div class="form-group">
                <label for="phone" class="form-label">
                    <span>Phone Number <span style="font-size: 10px; color: var(--text-muted); text-transform: none;">(Optional)</span></span>
                </label>
                <div style="position: relative;">
                    <i data-lucide="phone" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                    <input id="phone" name="phone" type="tel" class="form-control" style="padding-left: 42px;"
                           value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 234-5678">
                </div>
                @error('phone')
                    <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid-2" style="gap: 20px; margin-top: 4px;">
            <!-- 6. Birthday (Optional) -->
            <div class="form-group">
                <label for="birthday" class="form-label">
                    <span>Birthday <span style="font-size: 10px; color: var(--text-muted); text-transform: none;">(Optional)</span></span>
                </label>
                <div style="position: relative;">
                    <i data-lucide="cake" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                    <input id="birthday" name="birthday" type="date" class="form-control" style="padding-left: 42px;"
                           value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}">
                </div>
                <div class="form-hint" style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                    Smart Life Organizer can remind you to plan ahead for milestones.
                </div>
                @error('birthday')
                    <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Empty slot for balanced layout -->
            <div class="form-group" style="display: flex; flex-direction: column; justify-content: center; padding: 12px 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                <div style="font-size: 11.5px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="shield" style="width: 14px; height: 14px; color: #34d399;"></i>
                    <span>Privacy First</span>
                </div>
                <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                    Your personal phone and birthday are strictly private and never shared without permission.
                </p>
            </div>
        </div>

        <!-- 7. Short Bio (Optional) -->
        <div class="form-group" style="margin-top: 14px;" x-data="{ bioText: '{{ addslashes($user->bio ?? '') }}' }">
            <label for="bio" class="form-label" style="display: flex; justify-content: space-between;">
                <span>Short Bio <span style="font-size: 10px; color: var(--text-muted); text-transform: none;">(Optional)</span></span>
                <span style="font-size: 11px; color: var(--text-muted);" x-text="(bioText.length) + ' / 300'"></span>
            </label>
            <textarea id="bio" name="bio" rows="3" class="form-control"
                      x-model="bioText" maxlength="300"
                      placeholder="Share a short summary about your focus, work, daily routine, or personal objectives...">{{ old('bio', $user->bio) }}</textarea>
            @error('bio')
                <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border);">
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                Save Personal Info
            </button>
        </div>
    </form>
</div>
