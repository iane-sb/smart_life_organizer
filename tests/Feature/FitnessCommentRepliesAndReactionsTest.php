<?php

namespace Tests\Feature;

use App\Models\FitnessCommentReaction;
use App\Models\FitnessPost;
use App\Models\FitnessPostComment;
use App\Models\FitnessPostLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FitnessCommentRepliesAndReactionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $postOwner;
    protected User $commentAuthor;
    protected User $replyAuthor;
    protected User $unrelatedUser;
    protected FitnessPost $post;
    protected FitnessPostComment $topComment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postOwner = User::factory()->create(['name' => 'Post Owner']);
        $this->commentAuthor = User::factory()->create(['name' => 'Comment Author']);
        $this->replyAuthor = User::factory()->create(['name' => 'Reply Author']);
        $this->unrelatedUser = User::factory()->create(['name' => 'Unrelated User']);

        $this->post = FitnessPost::create([
            'user_id'          => $this->postOwner->id,
            'caption'          => 'Completed 10 miles trail run today!',
            'workout_type'     => 'running',
            'duration_minutes' => 75,
            'calories_burned'  => 850,
        ]);

        $this->topComment = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->commentAuthor->id,
            'comment'         => 'What an inspiring pace! Keep grinding.',
        ]);
    }

    // ──────────────────────────────────────────
    // 1. Threaded Replies
    // ──────────────────────────────────────────

    public function test_user_can_reply_to_top_level_comment_via_post_endpoint(): void
    {
        $response = $this->actingAs($this->replyAuthor)->postJson(route('fitness.comment', $this->post), [
            'comment'   => 'Totally agree with you!',
            'parent_id' => $this->topComment->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'   => true,
            'parent_id' => $this->topComment->id,
        ]);

        $this->assertDatabaseHas('fitness_post_comments', [
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Totally agree with you!',
        ]);

        $reply = FitnessPostComment::where('parent_id', $this->topComment->id)->first();
        $this->assertNotNull($reply);
        $this->assertTrue($reply->isReply());
        $this->assertEquals($this->topComment->id, $reply->parent->id);
    }

    public function test_user_can_reply_via_reply_endpoint(): void
    {
        $response = $this->actingAs($this->replyAuthor)->postJson(route('fitness.comment.reply', $this->topComment), [
            'comment' => 'Direct reply endpoint test!',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'   => true,
            'parent_id' => $this->topComment->id,
        ]);

        $this->assertDatabaseHas('fitness_post_comments', [
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Direct reply endpoint test!',
        ]);
    }

    public function test_rejects_reply_to_nonexistent_parent(): void
    {
        $response = $this->actingAs($this->replyAuthor)->postJson(route('fitness.comment', $this->post), [
            'comment'   => 'Replying to ghost comment',
            'parent_id' => 999999,
        ]);

        $response->assertStatus(422);
    }

    public function test_rejects_cross_post_reply(): void
    {
        $otherPost = FitnessPost::create([
            'user_id'      => $this->unrelatedUser->id,
            'caption'      => 'A different post entirely',
            'workout_type' => 'yoga',
        ]);

        // Attempt to reply on $otherPost using $this->topComment (which belongs to $this->post)
        $response = $this->actingAs($this->replyAuthor)->postJson(route('fitness.comment', $otherPost), [
            'comment'   => 'Trying cross-post attack',
            'parent_id' => $this->topComment->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_reply_to_a_reply_is_flattened_to_top_level_parent(): void
    {
        $reply1 = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'First reply',
        ]);

        // Now a user replies to $reply1
        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.reply', $reply1), [
            'comment' => 'Replying to the reply, should stay single-level',
        ]);

        $response->assertOk();

        // The new reply's parent_id must be topComment->id, NOT reply1->id!
        $this->assertDatabaseHas('fitness_post_comments', [
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->unrelatedUser->id,
            'comment'         => 'Replying to the reply, should stay single-level',
        ]);
    }

    public function test_cannot_reply_to_hidden_comment(): void
    {
        // $this->replyAuthor hides the top comment
        $this->actingAs($this->replyAuthor)->postJson(route('fitness.comment.hide', $this->topComment));

        // Now $this->replyAuthor attempts to reply to the hidden comment
        $response = $this->actingAs($this->replyAuthor)->postJson(route('fitness.comment.reply', $this->topComment), [
            'comment' => 'Trying to reply to a comment I hid',
        ]);

        $response->assertStatus(422);
    }

    // ──────────────────────────────────────────
    // 2. Permissions on Replies
    // ──────────────────────────────────────────

    public function test_reply_author_can_edit_own_reply(): void
    {
        $reply = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Original reply text',
        ]);

        $this->travel(2)->seconds();

        $response = $this->actingAs($this->replyAuthor)->putJson(route('fitness.comment.update', $reply), [
            'comment' => 'Edited reply text',
        ]);

        $response->assertOk();
        $reply->refresh();
        $this->assertEquals('Edited reply text', $reply->comment);
        $this->assertTrue($reply->isEdited());
    }

    public function test_non_author_cannot_edit_reply(): void
    {
        $reply = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Original reply text',
        ]);

        $response = $this->actingAs($this->unrelatedUser)->putJson(route('fitness.comment.update', $reply), [
            'comment' => 'Hacked reply text',
        ]);

        $response->assertStatus(403);
    }

    public function test_reply_author_can_delete_own_reply(): void
    {
        $reply = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Reply to delete',
        ]);

        $response = $this->actingAs($this->replyAuthor)->deleteJson(route('fitness.comment.destroy', $reply));

        $response->assertOk();
        $this->assertDatabaseMissing('fitness_post_comments', ['id' => $reply->id]);
        $this->assertDatabaseHas('fitness_post_comments', ['id' => $this->topComment->id]);
    }

    public function test_parent_post_owner_can_delete_reply_for_moderation(): void
    {
        $reply = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Spam reply',
        ]);

        $response = $this->actingAs($this->postOwner)->deleteJson(route('fitness.comment.destroy', $reply));

        $response->assertOk();
        $this->assertDatabaseMissing('fitness_post_comments', ['id' => $reply->id]);
    }

    public function test_unrelated_user_cannot_delete_reply(): void
    {
        $reply = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Protected reply',
        ]);

        $response = $this->actingAs($this->unrelatedUser)->deleteJson(route('fitness.comment.destroy', $reply));

        $response->assertStatus(403);
        $this->assertDatabaseHas('fitness_post_comments', ['id' => $reply->id]);
    }

    public function test_deleting_top_comment_cascades_and_deletes_its_replies(): void
    {
        $reply = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'Reply that should cascade delete',
        ]);

        $this->actingAs($this->commentAuthor)->deleteJson(route('fitness.comment.destroy', $this->topComment));

        $this->assertDatabaseMissing('fitness_post_comments', ['id' => $this->topComment->id]);
        $this->assertDatabaseMissing('fitness_post_comments', ['id' => $reply->id]);
    }

    // ──────────────────────────────────────────
    // 3. Reactions
    // ──────────────────────────────────────────

    public function test_user_can_react_to_comment(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.react', $this->topComment), [
            'reaction_type' => 'fire',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'         => true,
            'user_reaction'   => 'fire',
            'total_reactions' => 1,
            'reaction_counts' => [
                'fire' => 1,
            ],
        ]);

        $this->assertDatabaseHas('fitness_comment_reactions', [
            'fitness_post_comment_id' => $this->topComment->id,
            'user_id'                 => $this->unrelatedUser->id,
            'reaction_type'           => 'fire',
        ]);
    }

    public function test_user_can_react_to_a_reply(): void
    {
        $reply = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'parent_id'       => $this->topComment->id,
            'user_id'         => $this->replyAuthor->id,
            'comment'         => 'A great reply',
        ]);

        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.react', $reply), [
            'reaction_type' => 'celebrate',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'       => true,
            'user_reaction' => 'celebrate',
        ]);

        $this->assertDatabaseHas('fitness_comment_reactions', [
            'fitness_post_comment_id' => $reply->id,
            'user_id'                 => $this->unrelatedUser->id,
            'reaction_type'           => 'celebrate',
        ]);
    }

    public function test_clicking_same_reaction_removes_it(): void
    {
        FitnessCommentReaction::create([
            'fitness_post_comment_id' => $this->topComment->id,
            'user_id'                 => $this->unrelatedUser->id,
            'reaction_type'           => 'love',
        ]);

        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.react', $this->topComment), [
            'reaction_type' => 'love',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'         => true,
            'user_reaction'   => null,
            'total_reactions' => 0,
        ]);

        $this->assertDatabaseMissing('fitness_comment_reactions', [
            'fitness_post_comment_id' => $this->topComment->id,
            'user_id'                 => $this->unrelatedUser->id,
        ]);
    }

    public function test_changing_reaction_type_updates_existing_row(): void
    {
        FitnessCommentReaction::create([
            'fitness_post_comment_id' => $this->topComment->id,
            'user_id'                 => $this->unrelatedUser->id,
            'reaction_type'           => 'like',
        ]);

        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.react', $this->topComment), [
            'reaction_type' => 'fire',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'       => true,
            'user_reaction' => 'fire',
        ]);

        // Must still be only 1 reaction row
        $count = FitnessCommentReaction::where('fitness_post_comment_id', $this->topComment->id)
            ->where('user_id', $this->unrelatedUser->id)
            ->count();
        $this->assertEquals(1, $count);

        $this->assertDatabaseHas('fitness_comment_reactions', [
            'fitness_post_comment_id' => $this->topComment->id,
            'user_id'                 => $this->unrelatedUser->id,
            'reaction_type'           => 'fire',
        ]);
    }

    public function test_rejects_unsupported_reaction_type(): void
    {
        $response = $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.react', $this->topComment), [
            'reaction_type' => 'unsupported_reaction_emoji',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('reaction_type');
    }

    public function test_comment_reactions_are_completely_separate_from_post_likes(): void
    {
        // Add like to post
        $this->actingAs($this->unrelatedUser)->post(route('fitness.like', $this->post));
        $this->assertDatabaseHas('fitness_post_likes', [
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->unrelatedUser->id,
        ]);

        // Add reaction to comment
        $this->actingAs($this->unrelatedUser)->postJson(route('fitness.comment.react', $this->topComment), [
            'reaction_type' => 'fire',
        ]);

        // Both tables maintain their independent records
        $this->assertEquals(1, FitnessPostLike::where('fitness_post_id', $this->post->id)->count());
        $this->assertEquals(1, FitnessCommentReaction::where('fitness_post_comment_id', $this->topComment->id)->count());

        // Unlike post does not touch comment reaction
        $this->actingAs($this->unrelatedUser)->post(route('fitness.like', $this->post));
        $this->assertEquals(0, FitnessPostLike::where('fitness_post_id', $this->post->id)->count());
        $this->assertEquals(1, FitnessCommentReaction::where('fitness_post_comment_id', $this->topComment->id)->count());
    }

    public function test_unauthenticated_user_cannot_reply_or_react(): void
    {
        $this->postJson(route('fitness.comment.reply', $this->topComment), [
            'comment' => 'Unauthenticated reply',
        ])->assertStatus(401);

        $this->postJson(route('fitness.comment.react', $this->topComment), [
            'reaction_type' => 'like',
        ])->assertStatus(401);
    }
}
