<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarCropTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a valid fake JPEG image binary without requiring GD extension.
     */
    protected function createFakeImage(string $filename = 'avatar.jpg', int $sizeKilobytes = 100): UploadedFile
    {
        $header = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $padding = str_repeat('0', max(0, ($sizeKilobytes * 1024) - strlen($header)));
        return UploadedFile::fake()->createWithContent($filename, $header . $padding);
    }

    public function test_crop_modal_renders_on_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Adjust Your Profile Photo', false);
        $response->assertSee('avatar-crop-modal', false);
        $response->assertSee('crop-zoom-slider', false);
        $response->assertSee('crop-reset-btn', false);
        $response->assertSee('Circular Preview', false);
        $response->assertSee('crop-circle-preview', false);
        $response->assertSee('Save Photo', false);
    }

    public function test_user_can_upload_cropped_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['avatar' => null]);
        $image = $this->createFakeImage('cropped_avatar.jpg', 150);

        $response = $this->actingAs($user)
            ->postJson(route('profile.avatar.upload'), [
                'avatar_file' => $image,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Profile photo updated successfully!',
        ]);

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_avatar_upload_replaces_and_deletes_old_stored_avatar(): void
    {
        Storage::fake('public');

        // Pre-create an old avatar in public storage
        $oldImage = $this->createFakeImage('old_avatar.jpg', 100);
        $oldPath = $oldImage->store('avatars', 'public');

        $user = User::factory()->create(['avatar' => $oldPath]);
        Storage::disk('public')->assertExists($oldPath);

        // Upload new cropped avatar
        $newImage = $this->createFakeImage('new_cropped.jpg', 120);

        $response = $this->actingAs($user)
            ->postJson(route('profile.avatar.upload'), [
                'avatar_file' => $newImage,
            ]);

        $response->assertOk();

        $user->refresh();
        $this->assertNotEquals($oldPath, $user->avatar);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_avatar_upload_rejects_non_image_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $doc = UploadedFile::fake()->create('document.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)
            ->postJson(route('profile.avatar.upload'), [
                'avatar_file' => $doc,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('avatar_file');
    }

    public function test_avatar_upload_rejects_oversized_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $oversized = $this->createFakeImage('huge.jpg', 3000); // 3 MB > 2048 KB

        $response = $this->actingAs($user)
            ->postJson(route('profile.avatar.upload'), [
                'avatar_file' => $oversized,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('avatar_file');
    }

    public function test_unauthenticated_user_cannot_upload_avatar(): void
    {
        $image = $this->createFakeImage('avatar.jpg', 100);

        $response = $this->postJson(route('profile.avatar.upload'), [
            'avatar_file' => $image,
        ]);

        $response->assertStatus(401);
    }

    public function test_initials_fallback_when_no_avatar_set(): void
    {
        $user = User::factory()->create([
            'name' => 'Alex Taylor',
            'avatar' => null,
        ]);

        $this->assertStringContainsString('ui-avatars.com', $user->avatar_url);
        $this->assertStringContainsString('Alex+Taylor', $user->avatar_url);
    }

    public function test_full_profile_update_preserves_avatar(): void
    {
        Storage::fake('public');

        $image = $this->createFakeImage('existing.jpg', 100);
        $path = $image->store('avatars', 'public');

        $user = User::factory()->create([
            'name' => 'Original Name',
            'avatar' => $path,
        ]);

        // Submit personal info form without changing avatar
        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'avatar' => $path,
            '_tab' => 'personal',
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals($path, $user->avatar);
        Storage::disk('public')->assertExists($path);
    }

    public function test_upload_accepts_png_and_webp_cropped_images(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // Valid 1x1 PNG
        $pngHeader = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $pngFile = UploadedFile::fake()->createWithContent('crop_landscape.png', $pngHeader);

        $response = $this->actingAs($user)->postJson(route('profile.avatar.upload'), [
            'avatar_file' => $pngFile,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }
}
