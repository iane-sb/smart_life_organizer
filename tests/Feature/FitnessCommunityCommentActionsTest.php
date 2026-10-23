<?php

namespace Tests\Feature;

use App\Models\FitnessCommentHide;
use App\Models\FitnessCommentReport;
use App\Models\FitnessPost;
use App\Models\FitnessPostComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FitnessCommunityCommentActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $postAuthor;
    protected User $commentAuthor;
    protected User $unrelatedUser;
    protected FitnessPost $post;
    protected FitnessPostComment $comment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postAuthor = User::factory()->create(['name' => 'Post Owner']);
        $this->commentAuthor = User::factory()->create(['name' => 'Comment Author']);
        $this->unrelatedUser = User::factory()->create(['name' => 'Unrelated User']);

        $this->post = FitnessPost::create([
            'user_id'          => $this->postAuthor->id,
            'caption'          => 'First marathon finished!',
            'workout_type'     => 'running',
            'duration_minutes' => 240,
            'calories_burned'  => 2600,
        ]);

        $this->comment = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->commentAuthor->id,
            'comment'         => 'Incredible achievement, well done!',
        ]);
    }

    // ──────────────────────────────────────────
    // 1. Author can edit own comment
    // ──────────────────────────────────────────

    public function test_author_can_edit_own_comment(): void
    {
        $this->travel(2)->seconds();

        $response = $this->actingAs($this->commentAuthor)->putJson(route('fitness.comment.update', $this->comment), [
            'comment' => 'Updated comment: Absolutely incredible pace!',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'   => true,
            'comment'   => 'Updated comment: Absolutely incredible pace!',
            'is_edited' => true,
        ]);

        $this->comment->refresh();
        $this->assertEquals('Updated comment: Absolutely incredible pace!', $this->comment->comment);
        $this->assertTrue($this->comment->isEdited());
    }

    // ──────────────────────────────────────────
    // 2. Unrelated user cannot edit another user's comment
    // ──────────────────────────────────────────

    public function test_unrelated_user_cannot_edit_another_users_comment(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->putJson(route('fitness.comment.update', $this->comment), [
            'comment' => 'Malicious edit attempt',
        ]);

        $response->assertStatus(403);

        $this->comment->refresh();
        $this->assertEquals('Incredible achievement, well done!', $this->comment->comment);
    }

    public function test_parent_post_owner_cannot_edit_another_users_comment(): void
    {
        $response = $this->actingAs($this->postAuthor)->putJson(route('fitness.comment.update', $this->comment), [
            'comment' => 'Post owner trying to edit someone elses comment',
        ]);

        $response->assertStatus(403);
    }

    // ──────────────────────────────────────────
    // 3. Author can delete their comment
    // ──────────────────────────────────────────

    public function test_author_can_delete_their_comment(): void
    {
        $response = $this->actingAs($this->commentAuthor)->deleteJson(route('fitness.comment.destroy', $this->comment));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('fitness_post_comments', [
            'id' => $this->comment->id,
        ]);
    }

    // ──────────────────────────────────────────
    // 4. Parent-post owner can delete a comment on their post (moderation)
    // ──────────────────────────────────────────

    public function test_parent_post_owner_can_delete_comment_on_their_post(): void
    {
        $response = $this->actingAs($this->postAuthor)->deleteJson(route('fitness.comment.destroy', $this->comment));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('fitness_post_comments', [
            'id' => $this->comment->id,
        ]);
    }

    // ──────────────────────────────────────────
    // 5. Unrelated user cannot delete another user's comment
    // ──────────────────────────────────────────

    public function test_unrelated_user_cannot_delete_another_users_comment(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->deleteJson(route('fitness.comment.destroy', $this->comment));

        $response->assertStatus(403);

        $this->assertDatabaseHas('fitness_post_comments', [
            'id' => $this->comment->id,
        ]);
    }

    // ──────────────────────────────────────────
    // 6. Deleting a comment does not delete its parent post or other comments
    // ──────────────────────────────────────────

    public function test_deleting_comment_does_not_delete_parent_post_or_other_comments(): void
    {
        $secondComment = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->unrelatedUser->id,
            'comment'         => 'Another inspiring workout!',
        ]);

        $this->actingAs($this->commentAuthor)->deleteJson(route('fitness.comment.destroy', $this->comment));

        // Parent post is preserved
        $this->assertDatabaseHas('fitness_posts', [
            'id' => $this->post->id,
        ]);

        // Second comment is preserved
        $this->assertDatabaseHas('fitness_post_comments', [
            'id' => $secondComment->id,
        ]);

        // Deleted comment is gone
        $this->assertDatabaseMissing('fitness_post_comments', [
            'id' => $this->comment->id,
        ]);
    }

    // ──────────────────────────────────────────
    // 7. Hiding a comment affects only the current user
    // ──────────────────────────────────────────

    public function test_hiding_comment_affects_only_current_user(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.hide', $this->comment));

        $response->assertOk();
        $response->assertJson([
            'hidden' => true,
        ]);

        $this->assertDatabaseHas('fitness_comment_hides', [
            'fitness_post_comment_id' => $this->comment->id,
            'user_id'                 => $this->unrelatedUser->id,
        ]);

        // The hiding user does NOT see the comment in the normal feed
        $feedUnrelated = $this->actingAs($this->unrelatedUser)->get(route('fitness.index'));
        $feedUnrelated->assertStatus(200);
        $feedUnrelated->assertDontSeeText('Incredible achievement, well done!', false);

        // Another user (e.g. post owner) STILL sees the comment
        $feedPostAuthor = $this->actingAs($this->postAuthor)->get(route('fitness.index'));
        $feedPostAuthor->assertStatus(200);
        $feedPostAuthor->assertSeeText('Incredible achievement, well done!', false);

        // The comment author STILL sees their comment
        $feedCommentAuthor = $this->actingAs($this->commentAuthor)->get(route('fitness.index'));
        $feedCommentAuthor->assertStatus(200);
        $feedCommentAuthor->assertSeeText('Incredible achievement, well done!', false);
    }

    // ──────────────────────────────────────────
    // 8. Hidden comments remain hidden after refreshing or signing in again
    // ──────────────────────────────────────────

    public function test_hidden_comments_remain_hidden_after_signing_in_again(): void
    {
        FitnessCommentHide::create([
            'fitness_post_comment_id' => $this->comment->id,
            'user_id'                 => $this->unrelatedUser->id,
        ]);

        // Simulate session termination & sign in again
        auth()->logout();

        $response = $this->actingAs($this->unrelatedUser)->get(route('fitness.index'));
        $response->assertStatus(200);
        $response->assertDontSeeText('Incredible achievement, well done!', false);
    }

    // ──────────────────────────────────────────
    // 9. User can unhide a comment
    // ──────────────────────────────────────────

    public function test_user_can_unhide_a_comment(): void
    {
        FitnessCommentHide::create([
            'fitness_post_comment_id' => $this->comment->id,
            'user_id'                 => $this->unrelatedUser->id,
        ]);

        // Confirm it is hidden
        $this->actingAs($this->unrelatedUser)->get(route('fitness.index'))
            ->assertDontSeeText('Incredible achievement, well done!', false);

        // Unhide
        $response = $this->actingAs($this->unrelatedUser)->deleteJson(route('fitness.comment.unhide', $this->comment));
        $response->assertOk();
        $response->assertJson([
            'hidden' => false,
        ]);

        $this->assertDatabaseMissing('fitness_comment_hides', [
            'fitness_post_comment_id' => $this->comment->id,
            'user_id'                 => $this->unrelatedUser->id,
        ]);

        // Comment is visible again
        $this->actingAs($this->unrelatedUser)->get(route('fitness.index'))
            ->assertSeeText('Incredible achievement, well done!', false);
    }

    public function test_user_can_view_hidden_comments_via_filter(): void
    {
        FitnessCommentHide::create([
            'fitness_post_comment_id' => $this->comment->id,
            'user_id'                 => $this->unrelatedUser->id,
        ]);

        $response = $this->actingAs($this->unrelatedUser)->get(route('fitness.index', ['show_hidden_comments' => 1]));
        $response->assertStatus(200);
        $response->assertSeeText('Showing Hidden Comments', false);
        $response->assertSeeText('Incredible achievement, well done!', false);
    }

    // ──────────────────────────────────────────
    // 10. Duplicate hidden records are prevented
    // ──────────────────────────────────────────

    public function test_duplicate_hidden_records_are_prevented(): void
    {
        $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.hide', $this->comment));
        $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.hide', $this->comment));

        $count = FitnessCommentHide::where('fitness_post_comment_id', $this->comment->id)
            ->where('user_id', $this->unrelatedUser->id)
            ->count();

        $this->assertEquals(1, $count);
    }

    // ──────────────────────────────────────────
    // 11. User can submit a valid report
    // ──────────────────────────────────────────

    public function test_user_can_submit_valid_report_on_comment(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.report', $this->comment), [
            'reason'  => 'harassment',
            'details' => 'Offensive language targeted at athletes.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'reported'  => true,
            'duplicate' => false,
        ]);

        $this->assertDatabaseHas('fitness_comment_reports', [
            'fitness_post_comment_id' => $this->comment->id,
            'user_id'                 => $this->unrelatedUser->id,
            'reason'                  => 'harassment',
            'details'                 => 'Offensive language targeted at athletes.',
            'status'                  => 'pending',
        ]);
    }

    // ──────────────────────────────────────────
    // 12. Duplicate reports are prevented
    // ──────────────────────────────────────────

    public function test_duplicate_reports_are_prevented(): void
    {
        FitnessCommentReport::create([
            'fitness_post_comment_id' => $this->comment->id,
            'user_id'                 => $this->unrelatedUser->id,
            'reason'                  => 'spam',
            'status'                  => 'pending',
        ]);

        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.report', $this->comment), [
            'reason' => 'inappropriate',
        ]);

        $response->assertOk();
        $response->assertJson([
            'reported'  => true,
            'duplicate' => true,
        ]);

        $count = FitnessCommentReport::where('fitness_post_comment_id', $this->comment->id)
            ->where('user_id', $this->unrelatedUser->id)
            ->count();

        $this->assertEquals(1, $count);
    }

    // ──────────────────────────────────────────
    // 13. Invalid report reasons and invalid comment edits are rejected
    // ──────────────────────────────────────────

    public function test_invalid_report_reasons_are_rejected(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.report', $this->comment), [
            'reason' => 'totally_made_up_reason',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('reason');
    }

    public function test_empty_or_oversized_comment_edit_is_rejected(): void
    {
        // Empty comment
        $responseEmpty = $this->actingAs($this->commentAuthor)->putJson(route('fitness.comment.update', $this->comment), [
            'comment' => '',
        ]);
        $responseEmpty->assertStatus(422);
        $responseEmpty->assertJsonValidationErrors('comment');

        // Oversized comment (> 1000 chars)
        $responseOversized = $this->actingAs($this->commentAuthor)->putJson(route('fitness.comment.update', $this->comment), [
            'comment' => str_repeat('A', 1001),
        ]);
        $responseOversized->assertStatus(422);
        $responseOversized->assertJsonValidationErrors('comment');
    }

    // ──────────────────────────────────────────
    // 14. Copy action does not expose sensitive data
    // ──────────────────────────────────────────

    public function test_copy_action_does_not_expose_sensitive_data(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->getJson(route('fitness.comment.copy', $this->comment));

        $response->assertOk();
        $response->assertExactJson([
            'comment' => 'Incredible achievement, well done!',
        ]);

        // Ensure user email, password, or private fields are NOT present
        $data = $response->json();
        $this->assertArrayNotHasKey('email', $data);
        $this->assertArrayNotHasKey('password', $data);
        $this->assertArrayNotHasKey('user', $data);
    }

    // ──────────────────────────────────────────
    // 15. Unauthenticated requests are rejected
    // ──────────────────────────────────────────

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->putJson(route('fitness.comment.update', $this->comment), ['comment' => 'Test'])->assertStatus(401);
        $this->deleteJson(route('fitness.comment.destroy', $this->comment))->assertStatus(401);
        $this->postJson(route('fitness.comment.hide', $this->comment))->assertStatus(401);
        $this->deleteJson(route('fitness.comment.unhide', $this->comment))->assertStatus(401);
        $this->postJson(route('fitness.comment.report', $this->comment), ['reason' => 'spam'])->assertStatus(401);
        $this->getJson(route('fitness.comment.copy', $this->comment))->assertStatus(401);
    }
}
