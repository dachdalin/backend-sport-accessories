<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        User::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('users.index'));

        $response->assertOk();
    }

    public function test_user_show_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('users.show', $target));

        $response->assertOk();
    }

    public function test_user_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('users.create'));

        $response->assertOk();
    }

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create();
        $role = Role::findOrCreate('admin');

        $response = $this
            ->actingAs($user)
            ->post(route('users.store'), [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'roles' => [$role->id],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.index'));

        $created = User::where('email', 'jane@example.com')->sole();

        $this->assertSame('Jane Smith', $created->name);
        $this->assertNotNull($created->email_verified_at);
        $this->assertTrue($created->hasRole('admin'));
        $this->assertTrue($created->status);
    }

    public function test_user_roles_are_synced_when_submitted_as_strings(): void
    {
        // Real browser form submissions send roles[] as strings, not ints.
        $user = User::factory()->create();
        $target = User::factory()->create();
        $role = Role::findOrCreate('admin');

        $response = $this
            ->actingAs($user)
            ->put(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'status' => '1',
                'roles' => [(string) $role->id],
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertTrue($target->fresh()->hasRole('admin'));
    }

    public function test_user_name_email_and_password_are_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('users.store'), [
                'name' => '',
                'email' => '',
                'password' => '',
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_user_email_must_be_unique(): void
    {
        $user = User::factory()->create();
        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this
            ->actingAs($user)
            ->post(route('users.store'), [
                'name' => 'Another User',
                'email' => 'jane@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('users.edit', $target));

        $response->assertOk();
    }

    public function test_user_can_be_updated_without_changing_password(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();
        $originalPassword = $target->password;

        $response = $this
            ->actingAs($user)
            ->put(route('users.update', $target), [
                'name' => 'Updated Name',
                'email' => $target->email,
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.index'));

        $target->refresh();

        $this->assertSame('Updated Name', $target->name);
        $this->assertSame($originalPassword, $target->password);
    }

    public function test_user_password_can_be_changed_on_update(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
                'status' => '1',
            ]);

        $response->assertSessionHasNoErrors();

        $target->refresh();

        $this->assertTrue(Hash::check('new-password', $target->password));
    }

    public function test_user_can_be_banned(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.index'));

        $target->refresh();

        $this->assertFalse($target->status);
    }

    public function test_user_cannot_ban_their_own_account(): void
    {
        $user = User::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertTrue($user->status);
    }

    public function test_user_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('users.destroy', $target));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.index'));

        $this->assertModelMissing($target);
    }

    public function test_user_cannot_delete_their_own_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('users.destroy', $user));

        $response->assertRedirect();

        $this->assertModelExists($user);
    }
}
