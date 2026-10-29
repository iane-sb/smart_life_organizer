<?php

namespace Tests\Feature;

use App\Models\FitnessPost;
use App\Models\FitnessPostComment;
use App\Models\User;
use App\Notifications\FitnessActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Tests for Fitness Community notification behaviour.
 *
 * Covers:
 *  - Like notification sent to post author (but not to self)
 *  - Comment notification sent to post author (but not to self)
 *  - Reply notification sent via storeComment (but not to self)
 *  - Reply notification sent via replyComment endpoint (but not to self)
 *  - Mark-as-read route clears unread notifications
 *  - Unauthenticated access to mark-as-read is blocked
 */
class FitnessNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $postOwner;
    protected User $commenter;
    protected FitnessPost $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postOwner = User::factory()->create(['name' => 'Post Owner']);
        $this->commenter = User::factory()->create(['name' => 'Commenter']);

        $this->post = FitnessPost::create([
            'user_id'      => $this->postOwner->id,
            'caption'      => 'A great workout today!',
            'workout_type' => 'running',
        ]);
    }

    // ──────────────────────────────────────────
    // 1. Like notifications
    // ──────────────────────────────────────────

    public function test_liking_a_post_sends_notification_to_post_author(): void
    {
        Notification::fake();

        $this->actingAs($this->commenter)->post(route('fitness.like', $this->post));

        Notification::assertSentTo(
            $this->postOwner,
            FitnessActivityNotification::class,
            function (FitnessActivityNotification $notification) {
                return str_contains($notification->message, 'liked your post')
                    && str_contains($notification->message, 'Commenter');
            }
        );
    }

    public function test_unliking_a_post_does_not_send_notification(): void
    {
        Notification::fake();

        // Like first to set up the existing record
        $this->actingAs($this->commenter)->post(route('fitness.like', $this->post));
        Notification::assertSentTo($this->postOwner, FitnessActivityNotification::class);

        Notification::fake(); // Reset

        // Unlike — should not send another notification
        $this->actingAs($this->commenter)->post(route('fitness.like', $this->post));

        Notification::assertNothingSent();
    }

    public function test_liking_own_post_does_not_send_notification(): void
    {
        Notification::fake();

        $this->actingAs($this->postOwner)->post(route('fitness.like', $this->post));

        Notification::assertNothingSent();
    }

    // ──────────────────────────────────────────
    // 2. Comment notifications
    // ──────────────────────────────────────────

    public function test_commenting_on_post_sends_notification_to_post_author(): void
    {
        Notification::fake();

        $this->actingAs($this->commenter)->post(route('fitness.comment', $this->post), [
            'comment' => 'Nice effort!',
        ]);

        Notification::assertSentTo(
            $this->postOwner,
            FitnessActivityNotification::class,
            function (FitnessActivityNotification $notification) {
                return str_contains($notification->message, 'commented on your post')
                    && str_contains($notification->message, 'Commenter');
            }
        );
    }

    public function test_commenting_on_own_post_does_not_send_notification(): void
    {
        Notification::fake();

        $this->actingAs($this->postOwner)->post(route('fitness.comment', $this->post), [
            'comment' => 'My own update!',
        ]);

        Notification::assertNothingSent();
    }

    // ──────────────────────────────────────────
    // 3. Reply notifications (via storeComment with parent_id)
    // ──────────────────────────────────────────

    public function test_replying_to_comment_via_store_sends_notification_to_comment_author(): void
    {
        Notification::fake();

        $topComment = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->postOwner->id,
            'comment'         => 'Great run!',
        ]);

        $replier = User::factory()->create(['name' => 'Replier']);

        $this->actingAs($replier)->postJson(route('fitness.comment', $this->post), [
            'comment'   => 'I agree!',
            'parent_id' => $topComment->id,
        ]);

        Notification::assertSentTo(
            $this->postOwner,
            FitnessActivityNotification::class,
            function (FitnessActivityNotification $notification) {
                return str_contains($notification->message, 'replied to your comment')
                    && str_contains($notification->message, 'Replier');
            }
        );
    }

    public function test_replying_to_own_comment_does_not_notify_self(): void
    {
        Notification::fake();

        $ownComment = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->commenter->id,
            'comment'         => 'My own comment',
        ]);

        $this->actingAs($this->commenter)->postJson(route('fitness.comment', $this->post), [
            'comment'   => 'Replying to myself',
            'parent_id' => $ownComment->id,
        ]);

        Notification::assertNothingSent();
    }

    // ──────────────────────────────────────────
    // 4. Reply notifications (via replyComment endpoint)
    // ──────────────────────────────────────────

    public function test_reply_endpoint_sends_notification_to_comment_author(): void
    {
        Notification::fake();

        $topComment = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->commenter->id,
            'comment'         => 'Original comment',
        ]);

        $replier = User::factory()->create(['name' => 'Direct Replier']);

        $this->actingAs($replier)->postJson(route('fitness.comment.reply', $topComment), [
            'comment' => 'Via reply endpoint!',
        ]);

        Notification::assertSentTo(
            $this->commenter,
            FitnessActivityNotification::class,
            function (FitnessActivityNotification $notification) {
                return str_contains($notification->message, 'replied to your comment')
                    && str_contains($notification->message, 'Direct Replier');
            }
        );
    }

    public function test_reply_endpoint_does_not_notify_when_replying_to_own_comment(): void
    {
        Notification::fake();

        $ownComment = FitnessPostComment::create([
            'fitness_post_id' => $this->post->id,
            'user_id'         => $this->commenter->id,
            'comment'         => 'My comment',
        ]);

        $this->actingAs($this->commenter)->postJson(route('fitness.comment.reply', $ownComment), [
            'comment' => 'My own reply',
        ]);

        Notification::assertNothingSent();
    }

    // ──────────────────────────────────────────
    // 5. Mark-as-read route
    // ──────────────────────────────────────────

    public function test_mark_notifications_read_clears_unread_fitness_notifications(): void
    {
        // Create a real unread notification for postOwner
        $this->postOwner->notify(
            new FitnessActivityNotification('Commenter liked your post.', route('fitness.index'))
        );

        $this->assertSame(
            1,
            $this->postOwner->unreadNotifications()->where('data->type', 'fitness_activity')->count()
        );

        $response = $this->actingAs($this->postOwner)
            ->postJson(route('fitness.notifications.read'));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->postOwner->refresh();

        $this->assertSame(
            0,
            $this->postOwner->unreadNotifications()->where('data->type', 'fitness_activity')->count()
        );
    }

    public function test_mark_notifications_read_only_affects_authenticated_users_notifications(): void
    {
        $otherUser = User::factory()->create();

        // Give otherUser a notification
        $otherUser->notify(
            new FitnessActivityNotification('Someone liked your post.', route('fitness.index'))
        );

        // postOwner marks their own (empty) read
        $this->actingAs($this->postOwner)
            ->postJson(route('fitness.notifications.read'));

        // otherUser's notification must be untouched
        $otherUser->refresh();
        $this->assertSame(
            1,
            $otherUser->unreadNotifications()->where('data->type', 'fitness_activity')->count()
        );
    }

    public function test_unauthenticated_user_cannot_mark_notifications_read(): void
    {
        $response = $this->postJson(route('fitness.notifications.read'));
        $response->assertStatus(401);
    }

    // ──────────────────────────────────────────
    // 6. Notification stored in database
    // ──────────────────────────────────────────

    public function test_like_notification_is_persisted_to_database(): void
    {
        $this->actingAs($this->commenter)->post(route('fitness.like', $this->post));

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id'   => $this->postOwner->id,
            'type'            => \App\Notifications\FitnessActivityNotification::class,
            'read_at'         => null,
        ]);
    }

    public function test_notification_dot_is_driven_by_real_unread_database_state(): void
    {
        // Initially no unread notifications → fitness page renders without dot span
        $response = $this->actingAs($this->postOwner)->get(route('fitness.index'));
        $response->assertStatus(200);
        // The dot span only exists when there are unread notifications.
        // We check for the aria-label rather than the CSS class (which appears in <style>).
        $response->assertDontSee('aria-label="Unread notifications"', false);

        // Commenter likes the post → notification created
        $this->actingAs($this->commenter)->post(route('fitness.like', $this->post));

        // Now postOwner sees the dot (aria-label is on the rendered span)
        $response = $this->actingAs($this->postOwner)->get(route('fitness.index'));
        $response->assertStatus(200);
        $response->assertSee('aria-label="Unread notifications"', false);

        // Mark as read
        $this->actingAs($this->postOwner)->postJson(route('fitness.notifications.read'));

        // Dot is gone
        $response = $this->actingAs($this->postOwner)->get(route('fitness.index'));
        $response->assertStatus(200);
        $response->assertDontSee('aria-label="Unread notifications"', false);
    }
}
