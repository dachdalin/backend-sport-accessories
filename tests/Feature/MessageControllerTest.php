<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUserWithPermission(string $permission): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate($permission));

        $this->actingAs($user);

        return $user;
    }

    /**
     * UserFactory assigns the admin role by default (so existing bare-user tests keep passing),
     * which bypasses every permission check via the Gate::before admin override. Access-denial
     * tests need a genuinely role-less user, so strip roles here instead of using the factory directly.
     */
    private function actingAsUserWithoutPermissions(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([]);

        $this->actingAs($user);

        return $user;
    }

    public function test_messages_index_page_is_displayed(): void
    {
        $this->actingAsUserWithPermission('view messages');
        User::factory()->count(2)->create();

        $response = $this->get(route('messages.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('messages/Index')
                ->has('conversations', 2)
                ->where('selectedUser', null),
            );
    }

    public function test_selecting_a_conversation_loads_its_thread_and_marks_it_read(): void
    {
        $user = $this->actingAsUserWithPermission('view messages');
        $other = User::factory()->create();

        Message::factory()->create([
            'sender_id' => $other->id,
            'receiver_id' => $user->id,
            'body' => 'Hey there',
        ]);

        $response = $this->get(route('messages.index', ['user' => $other->id]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('messages/Index')
                ->where('selectedUser.id', $other->id)
                ->has('messages', 1),
            );

        $this->assertNotNull(
            Message::sole()->read_at,
        );
    }

    public function test_users_without_the_view_permission_cannot_view_messages(): void
    {
        $this->actingAsUserWithoutPermissions();

        $response = $this->get(route('messages.index'));

        $response->assertForbidden();
    }

    public function test_a_message_can_be_sent(): void
    {
        $user = $this->actingAsUserWithPermission('create messages');
        $other = User::factory()->create();

        $response = $this->post(route('messages.store'), [
            'receiver_id' => $other->id,
            'body' => 'Hello!',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('messages.index', ['user' => $other->id]));

        $message = Message::sole();

        $this->assertSame($user->id, $message->sender_id);
        $this->assertSame($other->id, $message->receiver_id);
        $this->assertSame('Hello!', $message->body);
    }

    public function test_users_without_the_create_permission_cannot_send_a_message(): void
    {
        $this->actingAsUserWithoutPermissions();
        $other = User::factory()->create();

        $response = $this->post(route('messages.store'), [
            'receiver_id' => $other->id,
            'body' => 'Hello!',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_receiver_id_must_exist(): void
    {
        $this->actingAsUserWithPermission('create messages');

        $response = $this->post(route('messages.store'), [
            'receiver_id' => 999,
            'body' => 'Hello!',
        ]);

        $response->assertSessionHasErrors('receiver_id');
    }

    public function test_user_cannot_message_themselves(): void
    {
        $user = $this->actingAsUserWithPermission('create messages');

        $response = $this->post(route('messages.store'), [
            'receiver_id' => $user->id,
            'body' => 'Hello!',
        ]);

        $response->assertSessionHasErrors('receiver_id');
    }

    public function test_body_is_required(): void
    {
        $this->actingAsUserWithPermission('create messages');
        $other = User::factory()->create();

        $response = $this->post(route('messages.store'), [
            'receiver_id' => $other->id,
        ]);

        $response->assertSessionHasErrors('body');
    }

    public function test_guest_cannot_access_messages(): void
    {
        $response = $this->get(route('messages.index'));

        $response->assertRedirect(route('login'));
    }
}
