<?php

namespace Tests\Feature;

use App\Models\FitnessPost;
use App\Models\FitnessPostHide;
use App\Models\FitnessPostReport;
use App\Models\FitnessPostSave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FitnessCommunityActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;
    protected User $otherUser;
    protected FitnessPost $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->post = FitnessPost::create([
            'user_id'      => $this->author->id,
            'caption'      => 'Morning run completed!',
            'workout_type' => 'running',
            'duration_minutes' => 30,
            'calories_burned'  => 300,
        ]);
    }

    // ──────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────

    public function test_author_can_view_edit_form(): void
    {
        $response = $this->actingAs($this->author)->get(route('fitness.edit', $this->post));
        $response->assertStatus(200);
        $response->assertSeeText('Edit Your Post', false);
    }

    public function test_non_author_cannot_view_edit_form(): void
    {
        $response = $this->actingAs($this->otherUser)->get(route('fitness.edit', $this->post));
        $response->assertStatus(403);
    }

    public function test_author_can_update_own_post(): void
    {
        $response = $this->actingAs($this->author)->put(route('fitness.update', $this->post), [
            'caption'      => 'Updated morning run!',
            'workout_type' => 'cycling',
            'duration_minutes' => 45,
            'calories_burned'  => 400,
        ]);

        $response->assertRedirect(route('fitness.index'));

        $this->post->refresh();
        $this->assertEquals('Updated morning run!', $this->post->caption);
        $this->assertEquals('cycling', $this->post->workout_type);
        $this->assertEquals(45, $this->post->duration_minutes);
        $this->assertEquals(400, $this->post->calories_burned);
    }

    public function test_non_author_cannot_update_post(): void
    {
        $response = $this->actingAs($this->otherUser)->put(route('fitness.update', $this->post), [
            'caption'      => 'Hacked caption!',
            'workout_type' => 'yoga',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('fitness_posts', [
            'id'      => $this->post->id,
            'caption' => 'Morning run completed!',
        ]);
    }

    public function test_edit_validates_required_fields(): void
    {
        $response = $this->actingAs($this->author)->put(route('fitness.update', $this->post), [
            'caption'      => '',
            'workout_type' => '',
        ]);

        $response->assertSessionHasErrors(['caption', 'workout_type']);
    }

    // ──────────────────────────────────────────
    // SAVE / UNSAVE
    // ──────────────────────────────────────────

    public function test_user_can_save_a_post(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->postJson(route('fitness.save', $this->post));

        $response->assertOk();
        $response->assertJson(['saved' => true]);

        $this->assertDatabaseHas('fitness_post_saves', [
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);
    }

    public function test_user_can_unsave_a_post(): void
    {
        FitnessPostSave::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);

        $response = $this->actingAs($this->otherUser)
            ->postJson(route('fitness.save', $this->post));

        $response->assertOk();
        $response->assertJson(['saved' => false]);

        $this->assertDatabaseMissing('fitness_post_saves', [
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);
    }

    public function test_save_prevents_duplicate_entries(): void
    {
        // Save twice via toggle (save then unsave then save)
        $this->actingAs($this->otherUser)->postJson(route('fitness.save', $this->post));
        $this->actingAs($this->otherUser)->postJson(route('fitness.save', $this->post)); // unsave
        $this->actingAs($this->otherUser)->postJson(route('fitness.save', $this->post)); // re-save

        $count = FitnessPostSave::where('fitness_post_id', $this->post->id)
            ->where('user_id', $this->otherUser->id)
            ->count();

        $this->assertEquals(1, $count);
    }

    // ──────────────────────────────────────────
    // HIDE / UNHIDE
    // ──────────────────────────────────────────

    public function test_user_can_hide_a_post(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->postJson(route('fitness.hide', $this->post));

        $response->assertOk();
        $response->assertJson(['hidden' => true]);

        $this->assertDatabaseHas('fitness_post_hides', [
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);
    }

    public function test_hidden_post_excluded_from_feed_for_hiding_user(): void
    {
        FitnessPostHide::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);

        $response = $this->actingAs($this->otherUser)->get(route('fitness.index'));
        $response->assertStatus(200);
        $response->assertDontSeeText('Morning run completed!', false);
    }

    public function test_hidden_post_still_visible_to_other_users(): void
    {
        FitnessPostHide::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);

        // Author can still see their own post
        $response = $this->actingAs($this->author)->get(route('fitness.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Morning run completed!', false);

        // A third user also sees it
        $thirdUser = User::factory()->create();
        $response = $this->actingAs($thirdUser)->get(route('fitness.index'));
        $response->assertSeeText('Morning run completed!', false);
    }

    public function test_user_can_view_hidden_posts_via_filter(): void
    {
        FitnessPostHide::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);

        $response = $this->actingAs($this->otherUser)
            ->get(route('fitness.index', ['show_hidden' => 1]));

        $response->assertStatus(200);
        $response->assertSeeText('Morning run completed!', false);
        $response->assertSeeText('Viewing Hidden Posts', false);
    }

    public function test_user_can_unhide_a_post(): void
    {
        FitnessPostHide::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);

        $response = $this->actingAs($this->otherUser)
            ->deleteJson(route('fitness.unhide', $this->post));

        $response->assertOk();
        $response->assertJson(['hidden' => false]);

        $this->assertDatabaseMissing('fitness_post_hides', [
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);

        // Post should now appear in feed again
        $response = $this->actingAs($this->otherUser)->get(route('fitness.index'));
        $response->assertSeeText('Morning run completed!', false);
    }

    public function test_hide_prevents_duplicate_entries(): void
    {
        // Hide twice
        $this->actingAs($this->otherUser)->postJson(route('fitness.hide', $this->post));
        $this->actingAs($this->otherUser)->postJson(route('fitness.hide', $this->post));

        $count = FitnessPostHide::where('fitness_post_id', $this->post->id)
            ->where('user_id', $this->otherUser->id)
            ->count();

        $this->assertEquals(1, $count);
    }

    // ──────────────────────────────────────────
    // DELETE (author ownership)
    // ──────────────────────────────────────────

    public function test_author_can_delete_own_post(): void
    {
        $response = $this->actingAs($this->author)->delete(route('fitness.destroy', $this->post));
        $response->assertRedirect(route('fitness.index'));
        $this->assertDatabaseMissing('fitness_posts', ['id' => $this->post->id]);
    }

    public function test_non_author_cannot_delete_post(): void
    {
        $response = $this->actingAs($this->otherUser)->delete(route('fitness.destroy', $this->post));
        $response->assertStatus(403);
        $this->assertDatabaseHas('fitness_posts', ['id' => $this->post->id]);
    }

    // ──────────────────────────────────────────
    // REPORT
    // ──────────────────────────────────────────

    public function test_user_can_report_a_post(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->postJson(route('fitness.report', $this->post), [
                'reason'  => 'spam',
                'details' => 'This looks like spam content.',
            ]);

        $response->assertOk();
        $response->assertJson(['reported' => true, 'duplicate' => false]);

        $this->assertDatabaseHas('fitness_post_reports', [
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
            'reason'          => 'spam',
            'status'          => 'pending',
        ]);
    }

    public function test_user_cannot_report_own_post(): void
    {
        $response = $this->actingAs($this->author)
            ->postJson(route('fitness.report', $this->post), [
                'reason' => 'spam',
            ]);

        $response->assertStatus(403);
    }

    public function test_duplicate_report_is_prevented(): void
    {
        FitnessPostReport::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
            'reason'          => 'spam',
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($this->otherUser)
            ->postJson(route('fitness.report', $this->post), [
                'reason' => 'harassment',
            ]);

        $response->assertOk();
        $response->assertJson(['duplicate' => true]);

        // Still only one report
        $count = FitnessPostReport::where('fitness_post_id', $this->post->id)
            ->where('user_id', $this->otherUser->id)
            ->count();
        $this->assertEquals(1, $count);
    }

    public function test_report_requires_valid_reason(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->postJson(route('fitness.report', $this->post), [
                'reason' => 'invalid_reason',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('reason');
    }

    // ──────────────────────────────────────────
    // SHARE
    // ──────────────────────────────────────────

    public function test_share_endpoint_returns_share_data(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->getJson(route('fitness.share', $this->post));

        $response->assertOk();
        $response->assertJsonStructure(['title', 'text', 'url']);
        $response->assertJsonFragment([
            'title' => 'Fitness Journey by ' . $this->author->name,
        ]);
    }

    // ──────────────────────────────────────────
    // UNAUTHENTICATED REQUESTS
    // ──────────────────────────────────────────

    public function test_unauthenticated_user_cannot_save_post(): void
    {
        $response = $this->postJson(route('fitness.save', $this->post));
        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_hide_post(): void
    {
        $response = $this->postJson(route('fitness.hide', $this->post));
        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_edit_post(): void
    {
        $response = $this->get(route('fitness.edit', $this->post));
        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_report_post(): void
    {
        $response = $this->postJson(route('fitness.report', $this->post), [
            'reason' => 'spam',
        ]);
        $response->assertStatus(401);
    }

    // ──────────────────────────────────────────
    // REGRESSION: Existing features still work
    // ──────────────────────────────────────────

    public function test_feed_loads_with_new_features(): void
    {
        $response = $this->actingAs($this->author)->get(route('fitness.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Fitness Community', false);
        $response->assertSeeText('Morning run completed!', false);
    }

    public function test_existing_like_and_comment_still_work(): void
    {
        // Like
        $response = $this->actingAs($this->otherUser)->post(route('fitness.like', $this->post));
        $response->assertRedirect();
        $this->assertDatabaseHas('fitness_post_likes', [
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->otherUser->id,
        ]);

        // Comment
        $response = $this->actingAs($this->otherUser)->post(route('fitness.comment', $this->post), [
            'comment' => 'Great workout!',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('fitness_post_comments', [
            'fitness_post_id' => $this->post->id,
            'comment'         => 'Great workout!',
        ]);
    }

    public function test_post_creation_still_works(): void
    {
        $response = $this->actingAs($this->author)->post(route('fitness.store'), [
            'caption'      => 'New workout post.',
            'workout_type' => 'yoga',
        ]);

        $response->assertRedirect(route('fitness.index'));
        $this->assertDatabaseHas('fitness_posts', [
            'caption' => 'New workout post.',
        ]);
    }
}
