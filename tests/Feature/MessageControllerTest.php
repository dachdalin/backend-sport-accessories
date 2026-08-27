<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_messages_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        User::factory()->count(2)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('messages.index'));

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
        $user = User::factory()->create();
        $other = User::factory()->create();

        Message::factory()->create([
            'sender_id' => $other->id,
            'receiver_id' => $user->id,
            'body' => 'Hey there',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('messages.index', ['user' => $other->id]));

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

    public function test_a_message_can_be_sent(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('messages.store'), [
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

    public function test_receiver_id_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('messages.store'), [
                'receiver_id' => 999,
                'body' => 'Hello!',
            ]);

        $response->assertSessionHasErrors('receiver_id');
    }

    public function test_user_cannot_message_themselves(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('messages.store'), [
                'receiver_id' => $user->id,
                'body' => 'Hello!',
            ]);

        $response->assertSessionHasErrors('receiver_id');
    }

    public function test_body_is_required(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('messages.store'), [
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
