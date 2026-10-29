<div class="glass-card animate-in" style="padding: 28px; border-radius: var(--radius-lg);">
    <div
        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 18px;">
        <div>
            <h2
                style="font-size: 18px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
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
        <div
            style="display: flex; align-items: center; gap: 24px; padding: 20px; background: rgba(255,255,255,0.02); border: 1px dashed var(--border); border-radius: var(--radius-md); margin-bottom: 28px;">
            <div style="position: relative;">
                <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                    style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid #7c3aed; box-shadow: 0 0 24px rgba(124,58,237,0.35);">
                <div
                    style="position: absolute; bottom: 0; right: 0; width: 26px; height: 26px; background: #7c3aed; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid var(--bg-primary);">
                    <i data-lucide="camera" style="width: 13px; height: 13px; color: white;"></i>
                </div>
            </div>

            <div style="flex: 1;">
                <div style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Profile
                    Photo or Avatar</div>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                    Upload a custom JPG, PNG, or WEBP photo (max 2MB), or choose a preset style.
                </p>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <label class="btn btn-ghost btn-sm"
                        style="cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="upload" style="width: 13px; height: 13px;"></i>
                        Upload Photo
                        <input type="file" id="avatar-file-input" accept="image/jpeg,image/png,image/webp,image/jpg"
                            style="display: none;">
                    </label>
                    <input type="hidden" name="avatar" id="avatar-url-input" value="{{ $user->avatar }}">
                    <button type="button" class="btn btn-ghost btn-sm" style="font-size: 11px;"
                        onclick="const seed = Math.random().toString(36).substring(7); const url = 'https://api.dicebear.com/7.x/bottts/svg?seed=' + seed; document.getElementById('avatar-preview').src = url; document.getElementById('avatar-url-input').value = url; if(document.getElementById('profile-hero-avatar')) document.getElementById('profile-hero-avatar').src = url; document.querySelectorAll('.user-avatar img').forEach(img => img.src = url);">
                        <i data-lucide="sparkles" style="width: 12px; height: 12px; color: #a78bfa;"></i>
                        Randomize Avatar
                    </button>
                    @if($user->avatar)
                        <button type="button" class="btn btn-ghost btn-sm" style="font-size: 11px; color: #f87171;"
                            onclick="const defaultUrl = 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=7c3aed&color=ffffff&bold=true&rounded=true&size=128'; document.getElementById('avatar-url-input').value = ''; document.getElementById('avatar-preview').src = defaultUrl; if(document.getElementById('profile-hero-avatar')) document.getElementById('profile-hero-avatar').src = defaultUrl; document.querySelectorAll('.user-avatar img').forEach(img => img.src = defaultUrl);">
                            Reset to Default
                        </button>
                    @endif
                </div>

                <!-- Live Client Error / Success Alerts -->
                <div id="avatar-selection-error" class="alert alert-error"
                    style="display: none; margin-top: 12px; padding: 8px 12px; font-size: 12px;">
                    <i data-lucide="alert-circle" style="width: 14px; height: 14px; flex-shrink: 0;"></i>
                    <span id="avatar-selection-error-text"></span>
                </div>
                <div id="avatar-selection-success" class="alert alert-success"
                    style="display: none; margin-top: 12px; padding: 8px 12px; font-size: 12px;">
                    <i data-lucide="check-circle" style="width: 14px; height: 14px; flex-shrink: 0;"></i>
                    <span id="avatar-selection-success-text"></span>
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
                    <i data-lucide="user"
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
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
                    <span
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-weight: 600; font-size: 14px; pointer-events: none;">@</span>
                    <input id="username" name="username" type="text" class="form-control" style="padding-left: 36px;"
                        value="{{ old('username', $user->username ?? $user->username_display) }}"
                        placeholder="alexjohnson">
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
                    <i data-lucide="mail"
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
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
                    <span>Phone Number <span
                            style="font-size: 10px; color: var(--text-muted); text-transform: none;">(Optional)</span></span>
                </label>
                <div style="position: relative;">
                    <i data-lucide="phone"
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
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
                    <span>Birthday <span
                            style="font-size: 10px; color: var(--text-muted); text-transform: none;">(Optional)</span></span>
                </label>
                <div style="position: relative;">
                    <i data-lucide="cake"
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
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
            <div class="form-group"
                style="display: flex; flex-direction: column; justify-content: center; padding: 12px 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                <div
                    style="font-size: 11.5px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
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
                <span>Short Bio <span
                        style="font-size: 10px; color: var(--text-muted); text-transform: none;">(Optional)</span></span>
                <span style="font-size: 11px; color: var(--text-muted);" x-text="(bioText.length) + ' / 300'"></span>
            </label>
            <textarea id="bio" name="bio" rows="3" class="form-control" x-model="bioText" maxlength="300"
                placeholder="Share a short summary about your focus, work, daily routine, or personal objectives...">{{ old('bio', $user->bio) }}</textarea>
            @error('bio')
                <div style="color: #f87171; font-size: 11.5px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div
            style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border);">
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                Save Personal Info
            </button>
        </div>
    </form>

    <!-- ===== SQUARE IMAGE CROPPING MODAL ===== -->
    <div id="avatar-crop-modal" class="modal-overlay" style="display: none;" role="dialog" aria-modal="true"
        aria-labelledby="avatar-crop-modal-title">
        <div class="modal-box"
            style="max-width: 500px; width: 94vw; max-height: 92vh; display: flex; flex-direction: column; padding: 22px; gap: 14px;">
            <!-- Modal Header -->
            <div class="modal-header"
                style="margin-bottom: 0; padding-bottom: 12px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div
                        style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.3); display: flex; align-items: center; justify-content: center; color: #a78bfa;">
                        <i data-lucide="crop" style="width: 17px; height: 17px;"></i>
                    </div>
                    <div>
                        <h3 id="avatar-crop-modal-title" class="modal-title" style="font-size: 16px; margin: 0;">Adjust Your Profile Photo</h3>
                        <p style="font-size: 11px; color: var(--text-muted); margin: 2px 0 0 0;">Drag to reposition and crop your image to a square.</p>
                    </div>
                </div>
                <button type="button" id="crop-modal-close-btn" class="modal-close" aria-label="Close modal">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </button>
            </div>

            <!-- Modal Error Alert -->
            <div id="crop-modal-error" class="alert alert-error"
                style="display: none; padding: 8px 12px; font-size: 12px; margin-bottom: 0;">
                <i data-lucide="alert-circle" style="width: 14px; height: 14px; flex-shrink: 0;"></i>
                <span id="crop-modal-error-text"></span>
            </div>

            <!-- Crop Stage Canvas -->
            <div id="crop-stage-container"
                style="position: relative; width: 100%; height: 260px; max-height: 38vh; background: #07090f; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; align-items: center; justify-content: center; touch-action: none; user-select: none;">
                <img id="crop-image-element" src="" alt="Selected image" style="max-width: 100%; max-height: 100%; display: block;">
            </div>

            <!-- Zoom & Pan Controls -->
            <div
                style="display: flex; flex-direction: column; gap: 8px; background: rgba(255,255,255,0.02); padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span
                        style="font-size: 11.5px; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="zoom-in" style="width: 13px; height: 13px; color: #a78bfa;"></i>
                        Zoom
                    </span>
                    <button type="button" id="crop-reset-btn" class="btn btn-ghost btn-sm"
                        style="font-size: 11px; padding: 3px 8px; height: 24px;">
                        <i data-lucide="rotate-ccw" style="width: 11px; height: 11px;"></i>
                        Reset
                    </button>
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <button type="button" id="crop-zoom-out-btn" class="btn btn-ghost btn-icon"
                        style="width: 28px; height: 28px; border-radius: var(--radius-sm);" title="Zoom Out"
                        aria-label="Zoom Out">
                        <i data-lucide="minus" style="width: 13px; height: 13px;"></i>
                    </button>
                    <input type="range" id="crop-zoom-slider" min="0.1" max="3" step="0.02" value="1"
                        style="flex: 1; accent-color: #7c3aed; cursor: pointer; height: 6px; border-radius: 3px;">
                    <button type="button" id="crop-zoom-in-btn" class="btn btn-ghost btn-icon"
                        style="width: 28px; height: 28px; border-radius: var(--radius-sm);" title="Zoom In"
                        aria-label="Zoom In">
                        <i data-lucide="plus" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
            </div>

            <!-- Circular Preview Option -->
            <div
                style="display: flex; align-items: center; gap: 14px; padding: 10px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md);">
                <div class="crop-circle-preview"></div>
                <div style="flex: 1;">
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-primary); margin-bottom: 2px;">
                        Circular Preview</div>
                    <p style="font-size: 11px; color: var(--text-muted); line-height: 1.35; margin: 0;">
                        Live preview of how your cropped square image appears in your circular profile avatar.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer"
                style="padding-top: 12px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 0;">
                <button type="button" id="crop-modal-cancel-btn" class="btn btn-ghost btn-sm"
                    style="padding: 8px 16px;">
                    Cancel
                </button>
                <button type="button" id="crop-modal-save-btn" class="btn btn-primary btn-sm"
                    style="padding: 8px 20px; display: inline-flex; align-items: center; gap: 6px;">
                    <span id="crop-save-spinner"
                        style="display: none; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.7s linear infinite;"></span>
                    <i data-lucide="check" id="crop-save-icon" style="width: 14px; height: 14px;"></i>
                    <span id="crop-save-text">Save Photo</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * NativeCanvasCropper: Lightweight fallback cropping engine using HTML5 Canvas.
     * Provides 1:1 square cropping with zero external dependencies, drag-to-position,
     * zoom, boundary clamping (no empty margins), live preview, and high-quality export.
     */
    class NativeCanvasCropper {
        constructor(imageElement, options) {
            this.img = imageElement;
            this.options = options || {};
            this.container = document.getElementById('crop-stage-container') || imageElement.parentElement;
            this.isDestroyed = false;

            this.canvas = document.createElement('canvas');
            this.canvas.id = 'native-cropper-canvas';
            this.canvas.style.display = 'block';
            this.canvas.style.width = '100%';
            this.canvas.style.height = '100%';
            this.canvas.style.cursor = 'grab';
            this.canvas.style.touchAction = 'none';
            this.ctx = this.canvas.getContext('2d');

            this.img.style.display = 'none';
            this.container.appendChild(this.canvas);

            this.scale = 1;
            this.minScale = 1;
            this.maxScale = 4;
            this.panX = 0;
            this.panY = 0;
            this.isDragging = false;
            this.dragStartX = 0;
            this.dragStartY = 0;
            this.lastTouchDistance = null;

            this.init();
        }

        init() {
            const rect = this.container.getBoundingClientRect();
            this.width = Math.floor(rect.width || this.container.clientWidth || 450);
            this.height = Math.floor(rect.height || this.container.clientHeight || 260);
            this.canvas.width = this.width;
            this.canvas.height = this.height;

            this.cropSize = Math.floor(Math.min(this.width, this.height) * 0.84);
            this.cropX = Math.floor((this.width - this.cropSize) / 2);
            this.cropY = Math.floor((this.height - this.cropSize) / 2);

            const naturalWidth = this.img.naturalWidth || 500;
            const naturalHeight = this.img.naturalHeight || 500;

            // Minimum scale ensures image strictly covers the entire square crop frame (no empty margins)
            this.minScale = Math.max(this.cropSize / naturalWidth, this.cropSize / naturalHeight);
            this.maxScale = this.minScale * 4.5;
            this.scale = this.minScale * 1.05;

            this.panX = this.cropX + (this.cropSize - naturalWidth * this.scale) / 2;
            this.panY = this.cropY + (this.cropSize - naturalHeight * this.scale) / 2;
            this.clamp();

            this.bindEvents();
            this.render();

            if (typeof this.options.ready === 'function') {
                this.options.ready.call(this);
            }
        }

        clamp() {
            const naturalWidth = this.img.naturalWidth || 500;
            const naturalHeight = this.img.naturalHeight || 500;
            const currentW = naturalWidth * this.scale;
            const currentH = naturalHeight * this.scale;

            const minPanX = this.cropX + this.cropSize - currentW;
            const maxPanX = this.cropX;
            if (minPanX <= maxPanX) {
                this.panX = Math.min(maxPanX, Math.max(minPanX, this.panX));
            } else {
                this.panX = this.cropX + (this.cropSize - currentW) / 2;
            }

            const minPanY = this.cropY + this.cropSize - currentH;
            const maxPanY = this.cropY;
            if (minPanY <= maxPanY) {
                this.panY = Math.min(maxPanY, Math.max(minPanY, this.panY));
            } else {
                this.panY = this.cropY + (this.cropSize - currentH) / 2;
            }
        }

        render() {
            if (this.isDestroyed) return;
            const ctx = this.ctx;
            ctx.clearRect(0, 0, this.width, this.height);

            // Draw image
            ctx.drawImage(
                this.img,
                this.panX,
                this.panY,
                this.img.naturalWidth * this.scale,
                this.img.naturalHeight * this.scale
            );

            // Draw dark mask outside square crop box
            ctx.fillStyle = 'rgba(10, 12, 22, 0.72)';
            ctx.fillRect(0, 0, this.width, this.cropY);
            ctx.fillRect(0, this.cropY + this.cropSize, this.width, this.height - (this.cropY + this.cropSize));
            ctx.fillRect(0, this.cropY, this.cropX, this.cropSize);
            ctx.fillRect(this.cropX + this.cropSize, this.cropY, this.width - (this.cropX + this.cropSize), this.cropSize);

            // Draw purple crop frame
            ctx.strokeStyle = '#7c3aed';
            ctx.lineWidth = 2.5;
            ctx.strokeRect(this.cropX, this.cropY, this.cropSize, this.cropSize);

            // Draw grid guides (rule of thirds)
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.22)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(this.cropX + this.cropSize / 3, this.cropY);
            ctx.lineTo(this.cropX + this.cropSize / 3, this.cropY + this.cropSize);
            ctx.moveTo(this.cropX + (this.cropSize * 2) / 3, this.cropY);
            ctx.lineTo(this.cropX + (this.cropSize * 2) / 3, this.cropY + this.cropSize);
            ctx.moveTo(this.cropX, this.cropY + this.cropSize / 3);
            ctx.lineTo(this.cropX + this.cropSize, this.cropY + this.cropSize / 3);
            ctx.moveTo(this.cropX, this.cropY + (this.cropSize * 2) / 3);
            ctx.lineTo(this.cropX + this.cropSize, this.cropY + (this.cropSize * 2) / 3);
            ctx.stroke();

            if (typeof this.options.crop === 'function') {
                this.options.crop.call(this);
            }
        }

        getCroppedCanvas(opts) {
            opts = opts || {};
            const targetWidth = opts.width || 512;
            const targetHeight = opts.height || 512;

            const outCanvas = document.createElement('canvas');
            outCanvas.width = targetWidth;
            outCanvas.height = targetHeight;
            const outCtx = outCanvas.getContext('2d');
            outCtx.imageSmoothingEnabled = true;
            outCtx.imageSmoothingQuality = 'high';

            const srcX = Math.max(0, (this.cropX - this.panX) / this.scale);
            const srcY = Math.max(0, (this.cropY - this.panY) / this.scale);
            const srcSize = this.cropSize / this.scale;

            outCtx.drawImage(
                this.img,
                srcX,
                srcY,
                srcSize,
                srcSize,
                0,
                0,
                targetWidth,
                targetHeight
            );

            return outCanvas;
        }

        zoom(delta) {
            this.zoomTo(this.scale + delta * this.minScale);
        }

        zoomTo(newScale) {
            newScale = Math.min(this.maxScale, Math.max(this.minScale, newScale));
            const centerScreenX = this.cropX + this.cropSize / 2;
            const centerScreenY = this.cropY + this.cropSize / 2;

            const naturalCenterX = (centerScreenX - this.panX) / this.scale;
            const naturalCenterY = (centerScreenY - this.panY) / this.scale;

            this.scale = newScale;
            this.panX = centerScreenX - naturalCenterX * this.scale;
            this.panY = centerScreenY - naturalCenterY * this.scale;

            this.clamp();
            this.render();

            if (typeof this.options.zoom === 'function') {
                this.options.zoom({ detail: { ratio: this.scale } });
            }
        }

        reset() {
            this.scale = this.minScale * 1.05;
            this.panX = this.cropX + (this.cropSize - this.img.naturalWidth * this.scale) / 2;
            this.panY = this.cropY + (this.cropSize - this.img.naturalHeight * this.scale) / 2;
            this.clamp();
            this.render();
            if (typeof this.options.zoom === 'function') {
                this.options.zoom({ detail: { ratio: this.scale } });
            }
        }

        getImageData() {
            return {
                naturalWidth: this.img.naturalWidth,
                naturalHeight: this.img.naturalHeight,
                width: this.img.naturalWidth * this.scale,
                height: this.img.naturalHeight * this.scale
            };
        }

        bindEvents() {
            const onStart = (clientX, clientY) => {
                this.isDragging = true;
                this.dragStartX = clientX - this.panX;
                this.dragStartY = clientY - this.panY;
                this.canvas.style.cursor = 'grabbing';
            };

            const onMove = (clientX, clientY) => {
                if (!this.isDragging) return;
                this.panX = clientX - this.dragStartX;
                this.panY = clientY - this.dragStartY;
                this.clamp();
                this.render();
            };

            const onEnd = () => {
                if (!this.isDragging) return;
                this.isDragging = false;
                this.canvas.style.cursor = 'grab';
            };

            // Mouse interaction
            this.canvas.addEventListener('mousedown', e => {
                e.preventDefault();
                onStart(e.clientX, e.clientY);
            });

            this._onMouseMove = e => {
                if (this.isDragging) onMove(e.clientX, e.clientY);
            };
            this._onMouseUp = () => {
                if (this.isDragging) onEnd();
            };

            window.addEventListener('mousemove', this._onMouseMove);
            window.addEventListener('mouseup', this._onMouseUp);

            // Touch interaction (Android & iOS mobile devices)
            this.canvas.addEventListener('touchstart', e => {
                if (e.touches.length === 1) {
                    onStart(e.touches[0].clientX, e.touches[0].clientY);
                } else if (e.touches.length === 2) {
                    this.isDragging = false;
                    const dx = e.touches[0].clientX - e.touches[1].clientX;
                    const dy = e.touches[0].clientY - e.touches[1].clientY;
                    this.lastTouchDistance = Math.hypot(dx, dy);
                }
            }, { passive: false });

            this.canvas.addEventListener('touchmove', e => {
                e.preventDefault(); // prevent parent scrolling while dragging
                if (e.touches.length === 1 && this.isDragging) {
                    onMove(e.touches[0].clientX, e.touches[0].clientY);
                } else if (e.touches.length === 2 && this.lastTouchDistance) {
                    const dx = e.touches[0].clientX - e.touches[1].clientX;
                    const dy = e.touches[0].clientY - e.touches[1].clientY;
                    const distance = Math.hypot(dx, dy);
                    const delta = (distance - this.lastTouchDistance) / 220;
                    this.lastTouchDistance = distance;
                    this.zoom(delta);
                }
            }, { passive: false });

            this.canvas.addEventListener('touchend', () => {
                onEnd();
                this.lastTouchDistance = null;
            });

            // Wheel zoom
            this.canvas.addEventListener('wheel', e => {
                e.preventDefault();
                const delta = e.deltaY < 0 ? 0.1 : -0.1;
                this.zoom(delta);
            }, { passive: false });
        }

        destroy() {
            this.isDestroyed = true;
            window.removeEventListener('mousemove', this._onMouseMove);
            window.removeEventListener('mouseup', this._onMouseUp);
            if (this.canvas && this.canvas.parentElement) {
                this.canvas.parentElement.removeChild(this.canvas);
            }
            this.img.style.display = 'block';
        }
    }

    (function () {
        const fileInput = document.getElementById('avatar-file-input');
        const modal = document.getElementById('avatar-crop-modal');
        const cropImg = document.getElementById('crop-image-element');
        const zoomSlider = document.getElementById('crop-zoom-slider');
        const zoomInBtn = document.getElementById('crop-zoom-in-btn');
        const zoomOutBtn = document.getElementById('crop-zoom-out-btn');
        const resetBtn = document.getElementById('crop-reset-btn');
        const cancelBtn = document.getElementById('crop-modal-cancel-btn');
        const closeBtn = document.getElementById('crop-modal-close-btn');
        const saveBtn = document.getElementById('crop-modal-save-btn');
        const saveSpinner = document.getElementById('crop-save-spinner');
        const saveIcon = document.getElementById('crop-save-icon');
        const saveText = document.getElementById('crop-save-text');
        const modalError = document.getElementById('crop-modal-error');
        const modalErrorText = document.getElementById('crop-modal-error-text');
        const selectionError = document.getElementById('avatar-selection-error');
        const selectionErrorText = document.getElementById('avatar-selection-error-text');
        const selectionSuccess = document.getElementById('avatar-selection-success');
        const selectionSuccessText = document.getElementById('avatar-selection-success-text');

        let cropper = null;
        let currentObjectUrl = null;
        let isUpdatingSlider = false;
        let isSaving = false;

        if (!fileInput || !modal) return;

        fileInput.addEventListener('change', function () {
            hideSelectionMessages();
            const file = this.files && this.files[0];
            if (!file) return;

            // 1. Validate file format (JPG, JPEG, PNG, WEBP)
            const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            const fileType = (file.type || '').toLowerCase();
            const fileName = (file.name || '').toLowerCase();
            const hasValidExt = /\.(jpe?g|png|webp)$/i.test(fileName);

            if (!validTypes.includes(fileType) && !hasValidExt) {
                showSelectionError('Unsupported file type. Please select a JPG, JPEG, PNG, or WEBP image.');
                fileInput.value = '';
                return;
            }

            // 2. Validate max file size (2 MB)
            const maxBytes = 2 * 1024 * 1024;
            if (file.size > maxBytes) {
                const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
                showSelectionError('File is too large (' + sizeMb + ' MB). The maximum allowed size is 2 MB.');
                fileInput.value = '';
                return;
            }

            openCropModal(file);
        });

        function updateCirclePreview() {
            if (!cropper) return;
            const previewContainer = document.querySelector('.crop-circle-preview');
            if (!previewContainer) return;

            try {
                const previewCanvas = cropper.getCroppedCanvas({ width: 136, height: 136 });
                if (!previewCanvas) return;

                let pCanvas = previewContainer.querySelector('canvas.live-circle-preview');
                if (!pCanvas) {
                    previewContainer.innerHTML = '';
                    pCanvas = document.createElement('canvas');
                    pCanvas.className = 'live-circle-preview';
                    pCanvas.width = 136;
                    pCanvas.height = 136;
                    pCanvas.style.width = '100%';
                    pCanvas.style.height = '100%';
                    pCanvas.style.borderRadius = '50%';
                    pCanvas.style.display = 'block';
                    previewContainer.appendChild(pCanvas);
                }
                const pCtx = pCanvas.getContext('2d');
                pCtx.clearRect(0, 0, 136, 136);
                pCtx.drawImage(previewCanvas, 0, 0, 136, 136);
            } catch (e) {
                // Ignore preview errors during transition
            }
        }

        function openCropModal(file) {
            if (currentObjectUrl) {
                URL.revokeObjectURL(currentObjectUrl);
            }
            currentObjectUrl = URL.createObjectURL(file);
            cropImg.src = currentObjectUrl;

            modalError.style.display = 'none';
            modalErrorText.textContent = '';
            setSaveLoading(false);
            saveBtn.disabled = true; // Enabled when cropper is initialized

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            let initialized = false;
            const initCropper = function () {
                if (initialized) return;
                initialized = true;

                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                // If Cropper.js is available, use it; otherwise seamlessly use NativeCanvasCropper
                if (typeof Cropper !== 'undefined') {
                    cropper = new Cropper(cropImg, {
                        aspectRatio: 1,
                        viewMode: 1, // Restrict crop frame strictly within image bounds (prevents empty margins)
                        dragMode: 'move',
                        autoCropArea: 0.9,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        ready: function () {
                            saveBtn.disabled = false;
                            updateCirclePreview();
                            const data = cropper.getImageData();
                            const initialRatio = (data.width / data.naturalWidth) || 1;
                            zoomSlider.min = (initialRatio * 0.4).toFixed(3);
                            zoomSlider.max = (initialRatio * 3.5).toFixed(3);
                            zoomSlider.value = initialRatio.toFixed(3);
                            if (window.lucide) window.lucide.createIcons();
                        },
                        crop: function () {
                            updateCirclePreview();
                        },
                        zoom: function (e) {
                            if (!isUpdatingSlider && zoomSlider) {
                                zoomSlider.value = (e.detail.ratio || 1).toFixed(3);
                            }
                        }
                    });
                } else {
                    // Fallback to NativeCanvasCropper (100% reliable, zero CDN dependency)
                    cropper = new NativeCanvasCropper(cropImg, {
                        ready: function () {
                            saveBtn.disabled = false;
                            updateCirclePreview();
                            zoomSlider.min = (this.minScale * 0.9).toFixed(3);
                            zoomSlider.max = (this.maxScale).toFixed(3);
                            zoomSlider.value = (this.scale).toFixed(3);
                            if (window.lucide) window.lucide.createIcons();
                        },
                        crop: function () {
                            updateCirclePreview();
                        },
                        zoom: function (e) {
                            if (!isUpdatingSlider && zoomSlider) {
                                zoomSlider.value = (e.detail.ratio || 1).toFixed(3);
                            }
                        }
                    });
                }
            };

            cropImg.onload = initCropper;
            if (cropImg.complete && cropImg.naturalWidth) {
                initCropper();
            }
        }

        function closeCropModal() {
            if (isSaving) return;
            modal.style.display = 'none';
            document.body.style.overflow = '';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (currentObjectUrl) {
                URL.revokeObjectURL(currentObjectUrl);
                currentObjectUrl = null;
            }
            cropImg.src = '';
            const previewContainer = document.querySelector('.crop-circle-preview');
            if (previewContainer) previewContainer.innerHTML = '';
            fileInput.value = '';
        }

        cancelBtn.addEventListener('click', closeCropModal);
        closeBtn.addEventListener('click', closeCropModal);

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeCropModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display !== 'none') {
                closeCropModal();
            }
        });

        zoomSlider.addEventListener('input', function () {
            if (cropper) {
                isUpdatingSlider = true;
                cropper.zoomTo(parseFloat(this.value));
                isUpdatingSlider = false;
            }
        });

        zoomInBtn.addEventListener('click', function () {
            if (cropper) cropper.zoom(0.1);
        });

        zoomOutBtn.addEventListener('click', function () {
            if (cropper) cropper.zoom(-0.1);
        });

        resetBtn.addEventListener('click', function () {
            if (cropper) {
                cropper.reset();
                const data = cropper.getImageData();
                const initialRatio = (data.width / data.naturalWidth) || 1;
                zoomSlider.value = initialRatio.toFixed(3);
            }
        });

        saveBtn.addEventListener('click', function () {
            if (!cropper || isSaving) return;

            setSaveLoading(true);
            modalError.style.display = 'none';

            try {
                const imgData = typeof cropper.getImageData === 'function' ? cropper.getImageData() : { naturalWidth: cropImg.naturalWidth, naturalHeight: cropImg.naturalHeight };
                const naturalMin = Math.min(imgData.naturalWidth || 512, imgData.naturalHeight || 512);
                const outputDimension = Math.min(512, Math.max(128, naturalMin));

                const canvas = cropper.getCroppedCanvas({
                    width: outputDimension,
                    height: outputDimension,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                if (!canvas) {
                    throw new Error('Could not create cropped image canvas.');
                }

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        setSaveLoading(false);
                        showModalError('Failed to process image blob. Please try again.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('avatar_file', blob, 'avatar.jpg');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('profile.avatar.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                        .then(async function (response) {
                            const data = await response.json();
                            if (!response.ok) {
                                const errorMsg = (data.errors && data.errors.avatar_file && data.errors.avatar_file[0])
                                    || data.message
                                    || 'Upload failed. Please try again.';
                                throw new Error(errorMsg);
                            }
                            return data;
                        })
                        .then(function (data) {
                            setSaveLoading(false);
                            closeCropModal();

                            const cacheBustUrl = data.avatar_url + (data.avatar_url.includes('?') ? '&' : '?') + 't=' + Date.now();

                            // 1. Personal information preview
                            const previewEl = document.getElementById('avatar-preview');
                            if (previewEl) previewEl.src = cacheBustUrl;

                            // 2. Profile hero card avatar
                            const heroEl = document.getElementById('profile-hero-avatar');
                            if (heroEl) heroEl.src = cacheBustUrl;

                            // 3. Sidebar avatar
                            document.querySelectorAll('.user-avatar img').forEach(function (img) {
                                img.src = cacheBustUrl;
                            });

                            // 4. Hidden form input value
                            const avatarInput = document.getElementById('avatar-url-input');
                            if (avatarInput && data.avatar) {
                                avatarInput.value = data.avatar;
                            }

                            showSelectionSuccess(data.message || 'Profile photo updated successfully!');
                        })
                        .catch(function (err) {
                            setSaveLoading(false);
                            showModalError(err.message || 'Failed to save cropped photo.');
                        });

                }, 'image/jpeg', 0.92);

            } catch (err) {
                setSaveLoading(false);
                showModalError(err.message || 'Error generating crop.');
            }
        });

        function setSaveLoading(loading) {
            isSaving = loading;
            saveBtn.disabled = loading;
            cancelBtn.disabled = loading;
            if (loading) {
                saveSpinner.style.display = 'inline-block';
                saveIcon.style.display = 'none';
                saveText.textContent = 'Saving...';
            } else {
                saveSpinner.style.display = 'none';
                saveIcon.style.display = 'inline-block';
                saveText.textContent = 'Save Photo';
            }
        }

        function showModalError(msg) {
            modalErrorText.textContent = msg;
            modalError.style.display = 'flex';
        }

        function showSelectionError(msg) {
            selectionErrorText.textContent = msg;
            selectionError.style.display = 'flex';
            selectionSuccess.style.display = 'none';
        }

        function showSelectionSuccess(msg) {
            selectionSuccessText.textContent = msg;
            selectionSuccess.style.display = 'flex';
            selectionError.style.display = 'none';
        }

        function hideSelectionMessages() {
            selectionError.style.display = 'none';
            selectionSuccess.style.display = 'none';
        }
    })();
</script>