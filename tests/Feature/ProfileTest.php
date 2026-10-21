<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_all_five_profile_sections_render_on_page(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertSeeText('Personal Information');
        $response->assertSeeText('Account Settings');
        $response->assertSeeText('Preferences');
        $response->assertSeeText('Privacy and Security');
        $response->assertSeeText('About and Support');
    }

    public function test_profile_extended_fields_and_preferences_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name'                   => 'Alex Johnson',
                'email'                  => 'alex@example.com',
                'username'               => 'alexjohnson',
                'phone'                  => '+15551234567',
                'birthday'               => '1995-06-15',
                'bio'                    => 'Organizing my productive life with Smart Life.',
                'theme_preference'       => 'dark',
                'language'               => 'en',
                'date_format'            => 'Y-m-d',
                'time_format'            => '12h',
                'default_dashboard_view' => 'overview',
                '_is_preferences'        => '1',
                'notify_email'           => '1',
                'notify_tasks'           => '1',
                'notify_events'          => '1',
            ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Alex Johnson', $user->name);
        $this->assertSame('alexjohnson', $user->username);
        $this->assertSame('+15551234567', $user->phone);
        $this->assertSame('1995-06-15', $user->birthday->format('Y-m-d'));
        $this->assertSame('Organizing my productive life with Smart Life.', $user->bio);
        $this->assertSame('dark', $user->theme_preference);
        $this->assertTrue($user->notify_email);
    }

    public function test_user_can_export_personal_data_as_json(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile/export');

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'attachment; filename='));
    }

    public function test_user_can_switch_theme_preference_to_light_and_system(): void
    {
        $user = User::factory()->create(['theme_preference' => 'dark']);

        // Switch to light mode
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name'             => $user->name,
                'email'            => $user->email,
                '_tab'             => 'preferences',
                '_is_preferences'  => '1',
                'theme_preference' => 'light',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile?tab=preferences');
        $this->assertSame('light', $user->fresh()->theme_preference);

        // Switch to system mode
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name'             => $user->name,
                'email'            => $user->email,
                '_tab'             => 'preferences',
                '_is_preferences'  => '1',
                'theme_preference' => 'system',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('system', $user->fresh()->theme_preference);
    }

    public function test_invalid_theme_preference_is_rejected(): void
    {
        $user = User::factory()->create(['theme_preference' => 'dark']);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name'             => $user->name,
                'email'            => $user->email,
                '_tab'             => 'preferences',
                '_is_preferences'  => '1',
                'theme_preference' => 'invalid-theme',
            ]);

        $response->assertSessionHasErrors('theme_preference');
        $this->assertSame('dark', $user->fresh()->theme_preference);
    }

    public function test_profile_page_renders_selected_theme_in_preferences_tab(): void
    {
        $user = User::factory()->create(['theme_preference' => 'light']);

        $response = $this
            ->actingAs($user)
            ->get('/profile?tab=preferences');

        $response->assertOk();
        $response->assertSee('data-theme="light"', false);
        $response->assertSee('data-theme-preference="light"', false);
        $response->assertSee('id="theme-radio-light"', false);
    }
}
