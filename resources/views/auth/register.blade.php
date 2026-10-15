<x-guest-layout>
    <div class="auth-card">
        <h1 class="auth-title">Create Account ✨</h1>
        <p class="auth-sub">Start organizing your life with intelligent planning.</p>

        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <div class="input-icon-wrap">
                    <i data-lucide="user" class="input-icon" style="width:16px;height:16px;"></i>
                    <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}"
                        required autofocus autocomplete="name" placeholder="Juan Dela Cruz">
                </div>
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-icon-wrap">
                    <i data-lucide="mail" class="input-icon" style="width:16px;height:16px;"></i>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}"
                        required autocomplete="username" placeholder="you@example.com">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-icon-wrap">
                    <i data-lucide="lock" class="input-icon" style="width:16px;height:16px;"></i>
                    <input id="password" class="form-control" type="password" name="password" required
                        autocomplete="new-password" placeholder="Min. 8 characters">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-icon-wrap">
                    <i data-lucide="shield-check" class="input-icon" style="width:16px;height:16px;"></i>
                    <input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
                        required autocomplete="new-password" placeholder="Repeat your password">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Create My Account
            </button>
        </form>

        <div class="divider">or</div>

        <div class="auth-footer" style="margin-top:0;">
            Already have an account? <a href="{{ route('login') }}" class="auth-link">Sign In</a>
        </div>
    </div>
</x-guest-layout>
