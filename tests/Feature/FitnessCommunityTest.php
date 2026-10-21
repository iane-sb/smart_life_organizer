<?php

namespace Tests\Feature;

use App\Models\FitnessPost;
use App\Models\FitnessPostComment;
use App\Models\FitnessPostLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FitnessCommunityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Helper to create a valid fake image without requiring the PHP GD extension.
     */
    protected function createFakeImage(string $filename = 'photo.jpg', int $sizeKilobytes = 100): UploadedFile
    {
        // 1x1 valid JPEG binary string
        $header = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $padding = str_repeat('0', max(0, ($sizeKilobytes * 1024) - strlen($header)));
        return UploadedFile::fake()->createWithContent($filename, $header . $padding);
    }

    public function test_unauthenticated_user_cannot_access_fitness_community(): void
    {
        $response = $this->get(route('fitness.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_fitness_feed(): void
    {
        $post = FitnessPost::create([
            'user_id'          => $this->user->id,
            'caption'          => 'Crushed a 5k morning run!',
            'workout_type'     => 'running',
            'duration_minutes' => 25,
            'calories_burned'  => 280,
        ]);

        $response = $this->actingAs($this->user)->get(route('fitness.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Fitness Community', false);
        $response->assertSeeText('Crushed a 5k morning run!', false);
        $response->assertSeeText('Running', false);
    }

    public function test_user_can_create_fitness_post_with_caption_and_metrics(): void
    {
        $response = $this->actingAs($this->user)->post(route('fitness.store'), [
            'caption'          => 'Heavy leg day completed.',
            'workout_type'     => 'weightlifting',
            'duration_minutes' => 60,
            'calories_burned'  => 450,
        ]);

        $response->assertRedirect(route('fitness.index'));
        $this->assertDatabaseHas('fitness_posts', [
            'user_id'          => $this->user->id,
            'caption'          => 'Heavy leg day completed.',
            'workout_type'     => 'weightlifting',
            'duration_minutes' => 60,
            'calories_burned'  => 450,
        ]);
    }

    public function test_user_can_create_post_with_valid_image_upload(): void
    {
        Storage::fake('public');

        $image = $this->createFakeImage('post_photo.jpg', 200);

        $response = $this->actingAs($this->user)->post(route('fitness.store'), [
            'caption'      => 'Trail run with a scenic sunrise.',
            'workout_type' => 'running',
            'image'        => $image,
        ]);

        $response->assertRedirect(route('fitness.index'));

        $post = FitnessPost::first();
        $this->assertNotNull($post);
        $this->assertNotNull($post->image_path);
        Storage::disk('public')->assertExists($post->image_path);
    }

    public function test_image_validation_rejects_oversized_or_invalid_file(): void
    {
        Storage::fake('public');

        // Test non-image file
        $pdfFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('fitness.store'), [
            'caption'      => 'Trying to upload pdf',
            'workout_type' => 'general',
            'image'        => $pdfFile,
        ]);

        $response->assertSessionHasErrors('image');

        // Test oversized image (> 5120KB)
        $oversizedImage = $this->createFakeImage('huge.jpg', 6000);

        $responseOver = $this->actingAs($this->user)->post(route('fitness.store'), [
            'caption'      => 'Trying to upload oversized image',
            'workout_type' => 'general',
            'image'        => $oversizedImage,
        ]);

        $responseOver->assertSessionHasErrors('image');
    }

    public function test_user_can_toggle_like_on_fitness_post(): void
    {
        $post = FitnessPost::create([
            'user_id'      => $this->user->id,
            'caption'      => 'Evening yoga session.',
            'workout_type' => 'yoga',
        ]);

        // First like: adds like
        $response1 = $this->actingAs($this->user)->post(route('fitness.like', $post));
        $response1->assertRedirect();
        $this->assertDatabaseHas('fitness_post_likes', [
            'fitness_post_id' => $post->id,
            'user_id'         => $this->user->id,
        ]);

        // Second like: unlikes
        $response2 = $this->actingAs($this->user)->post(route('fitness.like', $post));
        $response2->assertRedirect();
        $this->assertDatabaseMissing('fitness_post_likes', [
            'fitness_post_id' => $post->id,
            'user_id'         => $this->user->id,
        ]);
    }

    public function test_user_can_comment_on_fitness_post(): void
    {
        $otherUser = User::factory()->create();
        $post = FitnessPost::create([
            'user_id'      => $otherUser->id,
            'caption'      => 'Ran 10k today!',
            'workout_type' => 'running',
        ]);

        $response = $this->actingAs($this->user)->post(route('fitness.comment', $post), [
            'comment' => 'Incredible pace, keep it up!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('fitness_post_comments', [
            'fitness_post_id' => $post->id,
            'user_id'         => $this->user->id,
            'comment'         => 'Incredible pace, keep it up!',
        ]);
    }

    public function test_user_can_delete_their_own_post_and_image_is_removed_from_storage(): void
    {
        Storage::fake('public');

        $image = $this->createFakeImage('photo_to_delete.jpg', 150);
        $path  = $image->store('fitness_posts', 'public');

        $post = FitnessPost::create([
            'user_id'    => $this->user->id,
            'caption'    => 'Post to be deleted',
            'image_path' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)->delete(route('fitness.destroy', $post));
        $response->assertRedirect(route('fitness.index'));

        $this->assertDatabaseMissing('fitness_posts', ['id' => $post->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_user_cannot_delete_another_users_post(): void
    {
        $otherUser = User::factory()->create();
        $post = FitnessPost::create([
            'user_id' => $otherUser->id,
            'caption' => 'Unauthorized deletion target',
        ]);

        $response = $this->actingAs($this->user)->delete(route('fitness.destroy', $post));
        $response->assertStatus(403);

        $this->assertDatabaseHas('fitness_posts', ['id' => $post->id]);
    }

    public function test_user_can_delete_own_comment_but_not_unrelated_users_comment(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $post = FitnessPost::create([
            'user_id' => $this->user->id,
            'caption' => 'Post with comments',
        ]);

        $commentA = FitnessPostComment::create([
            'fitness_post_id' => $post->id,
            'user_id'         => $userA->id,
            'comment'         => 'Comment by User A',
        ]);

        // User B tries to delete User A's comment on this post -> forbidden
        $responseB = $this->actingAs($userB)->delete(route('fitness.comment.destroy', $commentA));
        $responseB->assertStatus(403);
        $this->assertDatabaseHas('fitness_post_comments', ['id' => $commentA->id]);

        // User A deletes their own comment -> allowed
        $responseA = $this->actingAs($userA)->delete(route('fitness.comment.destroy', $commentA));
        $responseA->assertRedirect();
        $this->assertDatabaseMissing('fitness_post_comments', ['id' => $commentA->id]);
    }

    public function test_feed_can_be_filtered_by_workout_type(): void
    {
        FitnessPost::create([
            'user_id'      => $this->user->id,
            'caption'      => 'Morning sprint interval',
            'workout_type' => 'running',
        ]);

        FitnessPost::create([
            'user_id'      => $this->user->id,
            'caption'      => 'Restorative vinyasa flow',
            'workout_type' => 'yoga',
        ]);

        $response = $this->actingAs($this->user)->get(route('fitness.index', ['type' => 'running']));

        $response->assertStatus(200);
        $response->assertSeeText('Morning sprint interval', false);
        $response->assertDontSeeText('Restorative vinyasa flow', false);
    }
}
