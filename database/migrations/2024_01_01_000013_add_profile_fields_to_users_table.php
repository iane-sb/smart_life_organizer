<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name');
            $table->string('username')->nullable()->unique()->after('avatar');
            $table->string('phone')->nullable()->after('email');
            $table->date('birthday')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('birthday');
            $table->string('theme_preference')->default('dark')->after('bio');
            $table->string('language')->default('en')->after('theme_preference');
            $table->string('date_format')->default('Y-m-d')->after('language');
            $table->string('time_format')->default('12h')->after('date_format');
            $table->boolean('notify_email')->default(true)->after('time_format');
            $table->boolean('notify_tasks')->default(true)->after('notify_email');
            $table->boolean('notify_events')->default(true)->after('notify_tasks');
            $table->string('default_dashboard_view')->default('overview')->after('notify_events');
            $table->boolean('two_factor_enabled')->default(false)->after('default_dashboard_view');
            $table->string('privacy_visibility')->default('private')->after('two_factor_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 'username', 'phone', 'birthday', 'bio',
                'theme_preference', 'language', 'date_format', 'time_format',
                'notify_email', 'notify_tasks', 'notify_events',
                'default_dashboard_view', 'two_factor_enabled', 'privacy_visibility'
            ]);
        });
    }
};
