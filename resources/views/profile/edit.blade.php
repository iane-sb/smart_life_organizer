@extends('layouts.app')

@section('title', 'Profile & Settings')
@section('page-title', 'Profile & Settings')

@section('header-actions')
    <a href="{{ route('profile.export') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
        Export Data
    </a>
@endsection

@section('content')
<div x-data="{ currentTab: '{{ request('tab', 'personal') }}' }" style="display: flex; flex-direction: column; gap: 24px;">

    <!-- ===== PROFILE HERO CARD ===== -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-xl); background: radial-gradient(ellipse at 15% 50%, rgba(124,58,237,0.18), transparent 60%), rgba(255,255,255,0.03); border: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
                <div style="position: relative; flex-shrink: 0;">
                    <img src="{{ $user->avatar_url }}"
                         alt="{{ $user->name }}"
                         style="width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 3px solid #7c3aed; box-shadow: 0 0 25px rgba(124,58,237,0.4);">
                    <span style="position: absolute; bottom: 2px; right: 2px; width: 15px; height: 15px; border-radius: 50%; background: #10b981; border: 2px solid var(--bg-primary);"></span>
                </div>

                <div style="min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <h1 style="font-size: 20px; font-weight: 800; color: var(--text-primary);">{{ $user->name }}</h1>
                        <span style="font-size: 13px; color: #a78bfa; font-weight: 600;">@ {{ $user->username_display }}</span>
                        <span class="badge badge-purple" style="font-size: 10px; text-transform: uppercase;">Pro Member</span>
                    </div>

                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span style="display: inline-flex; align-items: center; gap: 5px;">
                            <i data-lucide="mail" style="width: 13px; height: 13px; color: var(--text-secondary);"></i>
                            {{ $user->email }}
                        </span>
                        <span>•</span>
                        <span style="display: inline-flex; align-items: center; gap: 5px;">
                            <i data-lucide="calendar" style="width: 13px; height: 13px; color: var(--text-secondary);"></i>
                            Member since {{ $user->member_since }}
                        </span>
                        @if($user->phone)
                            <span>•</span>
                            <span style="display: inline-flex; align-items: center; gap: 5px;">
                                <i data-lucide="phone" style="width: 13px; height: 13px; color: var(--text-secondary);"></i>
                                {{ $user->phone }}
                            </span>
                        @endif
                    </div>

                    @if($user->bio)
                        <p style="font-size: 12px; color: var(--text-secondary); margin-top: 6px; max-width: 620px; line-height: 1.4;">
                            "{{ $user->bio }}"
                        </p>
                    @endif
                </div>
            </div>

            <!-- Stats Chips -->
            <div style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%; max-width: 420px;">
                <div style="flex: 1 1 110px; padding: 10px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: var(--radius-md); text-align: center;">
                    <div style="font-size: 17px; font-weight: 800; color: #a78bfa;">{{ $stats['total_tasks'] ?? $user->tasks()->count() }}</div>
                    <div style="font-size: 10.5px; color: var(--text-muted); text-transform: uppercase;">Tasks ({{ $stats['completed_tasks'] ?? $user->tasks()->where('status', 'completed')->count() }} done)</div>
                </div>
                <div style="flex: 1 1 80px; padding: 10px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: var(--radius-md); text-align: center;">
                    <div style="font-size: 17px; font-weight: 800; color: #60a5fa;">{{ $stats['total_events'] ?? $user->events()->count() }}</div>
                    <div style="font-size: 10.5px; color: var(--text-muted); text-transform: uppercase;">Events</div>
                </div>
                <div style="flex: 1 1 80px; padding: 10px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: var(--radius-md); text-align: center;">
                    <div style="font-size: 17px; font-weight: 800; color: #34d399;">{{ $stats['total_notes'] ?? $user->notes()->count() }}</div>
                    <div style="font-size: 10.5px; color: var(--text-muted); text-transform: uppercase;">Notes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TABBED NAVIGATION BAR (Horizontal Swipe on Mobile) ===== -->
    <div style="display: flex; gap: 8px; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; background: rgba(255,255,255,0.02); padding: 6px; border: 1px solid var(--border); border-radius: var(--radius-md);">
        <button type="button" @click="currentTab = 'personal'"
                class="tab-btn" :class="{ 'active': currentTab === 'personal' }"
                style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap;">
            <i data-lucide="user" style="width: 15px; height: 15px;"></i>
            1. Personal Information
        </button>

        <button type="button" @click="currentTab = 'account'"
                class="tab-btn" :class="{ 'active': currentTab === 'account' }"
                style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap;">
            <i data-lucide="settings" style="width: 15px; height: 15px;"></i>
            2. Account Settings
        </button>

        <button type="button" @click="currentTab = 'preferences'"
                class="tab-btn" :class="{ 'active': currentTab === 'preferences' }"
                style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap;">
            <i data-lucide="sliders" style="width: 15px; height: 15px;"></i>
            3. Preferences
        </button>

        <button type="button" @click="currentTab = 'security'"
                class="tab-btn" :class="{ 'active': currentTab === 'security' }"
                style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap;">
            <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i>
            4. Privacy & Security
        </button>

        <button type="button" @click="currentTab = 'about'"
                class="tab-btn" :class="{ 'active': currentTab === 'about' }"
                style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap;">
            <i data-lucide="info" style="width: 15px; height: 15px;"></i>
            5. About & Support
        </button>
    </div>

    <!-- ===== TAB PANELS ===== -->
    <!-- Tab 1: Personal Information -->
    <div x-show="currentTab === 'personal'" x-cloak>
        @include('profile.partials.personal-information')
    </div>

    <!-- Tab 2: Account Settings -->
    <div x-show="currentTab === 'account'" x-cloak>
        @include('profile.partials.account-settings')
    </div>

    <!-- Tab 3: Preferences -->
    <div x-show="currentTab === 'preferences'" x-cloak>
        @include('profile.partials.preferences')
    </div>

    <!-- Tab 4: Privacy & Security -->
    <div x-show="currentTab === 'security'" x-cloak>
        @include('profile.partials.privacy-security')
    </div>

    <!-- Tab 5: About & Support -->
    <div x-show="currentTab === 'about'" x-cloak>
        @include('profile.partials.about-support')
    </div>

</div>
@endsection
