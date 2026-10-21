<x-guest-layout>
    <div class="auth-card">
        <h1 class="auth-title">Welcome back 👋</h1>
        <p class="auth-sub">Log in to organize your tasks, schedule, and notes.</p>

        <!-- Session Status -->
        @if (session('status'))
            <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-icon-wrap">
                    <i data-lucide="mail" class="input-icon" style="width:16px;height:16px;"></i>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email', 'demo@smartlife.com') }}" required autofocus autocomplete="username" placeholder="you@example.com">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <label for="password" class="form-label" style="margin-bottom:0;">Password</label>
                    @if (Route::has('password.request'))
                        <a class="auth-link" href="{{ route('password.request') }}" style="font-size:12px;">Forgot?</a>
                    @endif
                </div>
                <div class="input-icon-wrap">
                    <i data-lucide="lock" class="input-icon" style="width:16px;height:16px;"></i>
                    <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" value="password">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="form-group remember-row">
                <label for="remember_me" style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary); cursor:pointer;">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Keep me signed in</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">
                Sign In to Dashboard
            </button>
        </form>

        <div class="divider">or</div>

        <div class="auth-footer" style="margin-top:0;">
            Don't have an account? <a href="{{ route('register') }}" class="auth-link">Create an account</a>
        </div>
        
        <div style="margin-top:20px; padding:12px; background:rgba(124,58,237,0.08); border:1px dashed rgba(124,58,237,0.3); border-radius:8px; font-size:12px; color:var(--text-secondary); text-align:center;">
            <span style="color:#c4b5fd;">
                &copy; 2026 Smart Life Organizer. All Rights Reserved.
            </span>
        </div>
        
    </div>
</x-guest-layout>
