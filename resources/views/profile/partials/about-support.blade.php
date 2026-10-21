<div style="display: flex; flex-direction: column; gap: 24px;" x-data="{ showPrivacyModal: false, showTermsModal: false, showContactModal: false }">

    <!-- Header & Section Badge -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="info" style="color: #38bdf8; width: 20px; height: 20px;"></i>
                    About and Support
                </h2>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                    System versioning, support documentation, legal notices, and developer credits.
                </p>
            </div>
            <span class="badge badge-blue" style="font-size: 11px;">Section 5 of 5</span>
        </div>
    </div>

    <!-- 1. App Version & Tech Information -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px rgba(124,58,237,0.4);">
                    <i data-lucide="zap" style="width: 24px; height: 24px; color: white;"></i>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 800; color: var(--text-primary);">Smart Life Organizer</div>
                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">Your intelligent companion for daily tasks, schedule coordination, and ideas.</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge badge-purple" style="font-size: 12px; padding: 6px 14px;">Version 1.2.0 (Stable)</span>
                <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #34d399; font-weight: 600;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #34d399; display: inline-block;"></span>
                    Latest Release
                </span>
            </div>
        </div>

        <div class="grid-4" style="gap: 12px;">
            <div style="padding: 12px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                <div style="font-size: 10.5px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Architecture</div>
                <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-top: 4px;">Laravel 12.x</div>
            </div>
            <div style="padding: 12px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                <div style="font-size: 10.5px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">PHP Engine</div>
                <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-top: 4px;">PHP 8.2+</div>
            </div>
            <div style="padding: 12px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                <div style="font-size: 10.5px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Database</div>
                <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-top: 4px;">MySQL / MariaDB</div>
            </div>
            <div style="padding: 12px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                <div style="font-size: 10.5px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">UI System</div>
                <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-top: 4px;">Glassmorphism v2</div>
            </div>
        </div>
    </div>

    <!-- 2. Help Center or Contact Support -->
    <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="life-buoy" style="color: #60a5fa; width: 18px; height: 18px;"></i>
                    Help Center and Contact Support
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">
                    Have questions or feedback? Our team and documentation are here to help.
                </p>
            </div>
            <button type="button" @click="showContactModal = true" class="btn btn-ghost btn-sm" style="color: #60a5fa; border-color: rgba(96,165,250,0.3);">
                <i data-lucide="message-square" style="width: 14px; height: 14px;"></i>
                Send Support Message
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <div style="padding: 14px 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md);">
                <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <span>📌 How do task priority colors work?</span>
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.5;">
                    Tasks are sorted automatically: Urgent (Crimson), High (Amber), Medium (Violet), and Low (Slate). Urgent tasks are automatically highlighted on your dashboard.
                </p>
            </div>

            <div style="padding: 14px 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md);">
                <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <span>📅 How does the multi-view Smart Calendar function?</span>
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.5;">
                    You can toggle effortlessly between Monthly, Weekly, and Daily views. Color-coded categories keep appointments, study schedules, and work shifts organized.
                </p>
            </div>

            <div style="padding: 14px 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: var(--radius-md);">
                <div style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <span>🤖 What features will the Future AI Assistant offer?</span>
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.5;">
                    The upcoming AI Assistant will analyze your workload streaks, suggest task prioritization, auto-reschedule conflicting appointments, and generate daily planning digests.
                </p>
            </div>
        </div>
    </div>

    <!-- 3. Privacy Policy & 4. Terms and Conditions -->
    <div class="grid-2" style="gap: 20px;">
        <!-- Privacy Policy -->
        <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="file-text" style="color: #34d399; width: 18px; height: 18px;"></i>
                    Privacy Policy
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 6px; line-height: 1.5;">
                    Your productivity data is strictly yours. We never sell personal data or track your behavior across other platforms.
                </p>
            </div>
            <button type="button" @click="showPrivacyModal = true" class="btn btn-ghost btn-sm" style="margin-top: 16px; width: 100%; justify-content: center;">
                Read Privacy Policy →
            </button>
        </div>

        <!-- Terms and Conditions -->
        <div class="glass-card animate-in" style="padding: 24px; border-radius: var(--radius-lg); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="scale" style="color: #fbbf24; width: 18px; height: 18px;"></i>
                    Terms and Conditions
                </h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 6px; line-height: 1.5;">
                    Guidelines regarding user accounts, intellectual property rights, data reliability, and fair usage.
                </p>
            </div>
            <button type="button" @click="showTermsModal = true" class="btn btn-ghost btn-sm" style="margin-top: 16px; width: 100%; justify-content: center;">
                View Terms & Conditions →
            </button>
        </div>
    </div>

    <!-- 5. Copyright Notice -->
    <div class="glass-card animate-in" style="padding: 20px; border-radius: var(--radius-lg); text-align: center; border-color: rgba(255,255,255,0.04);">
        <p style="font-size: 12.5px; color: var(--text-secondary); margin-bottom: 4px;">
            © 2024 – {{ date('Y') }} <strong>Smart Life Organizer</strong>. All rights reserved.
        </p>
        <p style="font-size: 11px; color: var(--text-muted);">
            Designed for mindful productivity, seamless scheduling, and intuitive daily focus.
        </p>
    </div>

    <!-- Contact Support Modal -->
    <div x-show="showContactModal" class="modal-backdrop" style="display: flex;" x-cloak>
        <div class="modal-box" @click.away="showContactModal = false">
            <div class="modal-header">
                <div class="modal-title" style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="help-circle" style="color: #60a5fa; width: 18px; height: 18px;"></i>
                    Contact Support & Feedback
                </div>
                <button type="button" class="modal-close" @click="showContactModal = false">✕</button>
            </div>
            <div class="modal-body">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                    Send a direct note to our team. We typically respond within 24 business hours.
                </p>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input type="text" class="form-control" placeholder="e.g. Question about calendar sync">
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea rows="4" class="form-control" placeholder="Describe what you need assistance with..."></textarea>
                </div>
                <div style="font-size: 11.5px; color: var(--text-muted);">
                    Direct Email: <a href="mailto:support@smartlifeorganizer.app" style="color: #a78bfa; text-decoration: none;">support@smartlifeorganizer.app</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" @click="showContactModal = false">Close</button>
                <button type="button" class="btn btn-primary" @click="showContactModal = false; alert('Thank you! Your support message has been sent.');">
                    Send Message
                </button>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div x-show="showPrivacyModal" class="modal-backdrop" style="display: flex;" x-cloak>
        <div class="modal-box" style="max-width: 600px;" @click.away="showPrivacyModal = false">
            <div class="modal-header">
                <div class="modal-title" style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="shield" style="color: #34d399; width: 18px; height: 18px;"></i>
                    Privacy Policy
                </div>
                <button type="button" class="modal-close" @click="showPrivacyModal = false">✕</button>
            </div>
            <div class="modal-body" style="max-height: 380px; overflow-y: auto; font-size: 13px; color: var(--text-secondary); line-height: 1.6;">
                <h4 style="color: var(--text-primary); font-size: 14px; margin-bottom: 6px;">1. Information We Collect</h4>
                <p style="margin-bottom: 14px;">We only store information you explicitly enter into your account: profile name, email, tasks, events, and notes. No background third-party tracking or behavioral profiling is ever performed.</p>

                <h4 style="color: var(--text-primary); font-size: 14px; margin-bottom: 6px;">2. Data Storage & Security</h4>
                <p style="margin-bottom: 14px;">Your data is encrypted in transit and stored safely in your designated database instance. Passwords are permanently hashed using industry-standard bcrypt.</p>

                <h4 style="color: var(--text-primary); font-size: 14px; margin-bottom: 6px;">3. Data Export & Deletion Rights</h4>
                <p>Under GDPR and privacy regulations, you have the right to export your complete personal data at any time in open JSON format, or permanently delete your account and all associated records with immediate effect.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" @click="showPrivacyModal = false">I Understand</button>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div x-show="showTermsModal" class="modal-backdrop" style="display: flex;" x-cloak>
        <div class="modal-box" style="max-width: 600px;" @click.away="showTermsModal = false">
            <div class="modal-header">
                <div class="modal-title" style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="scale" style="color: #fbbf24; width: 18px; height: 18px;"></i>
                    Terms and Conditions
                </div>
                <button type="button" class="modal-close" @click="showTermsModal = false">✕</button>
            </div>
            <div class="modal-body" style="max-height: 380px; overflow-y: auto; font-size: 13px; color: var(--text-secondary); line-height: 1.6;">
                <h4 style="color: var(--text-primary); font-size: 14px; margin-bottom: 6px;">1. User Accounts</h4>
                <p style="margin-bottom: 14px;">You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>

                <h4 style="color: var(--text-primary); font-size: 14px; margin-bottom: 6px;">2. User Ownership of Content</h4>
                <p style="margin-bottom: 14px;">You retain 100% intellectual ownership of all personal to-do tasks, notes, journal entries, and schedules created within Smart Life Organizer.</p>

                <h4 style="color: var(--text-primary); font-size: 14px; margin-bottom: 6px;">3. Acceptable Use</h4>
                <p>Smart Life Organizer is intended for lawful personal and professional productivity organization. Abusive automated scraping or unauthorized reverse engineering is prohibited.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" @click="showTermsModal = false">Accept</button>
            </div>
        </div>
    </div>

</div>
