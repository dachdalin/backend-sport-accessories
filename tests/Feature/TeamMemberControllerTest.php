<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_members_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        TeamMember::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('team-members.index'));

        $response->assertOk();
    }

    public function test_guests_cannot_view_team_members(): void
    {
        $response = $this->get(route('team-members.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_team_member_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('team-members.create'));

        $response->assertOk();
    }

    public function test_team_member_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('team-members.store'), [
                'name' => 'Jane Doe',
                'role' => 'Head Coach',
                'bio' => 'Leads the coaching staff.',
                'photo_alt_text' => 'Jane Doe headshot',
                'sort_order' => '3',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('team-members.index'));

        $teamMember = TeamMember::sole();

        $this->assertSame('Jane Doe', $teamMember->name);
        $this->assertSame('Head Coach', $teamMember->role);
        $this->assertSame('def.png', $teamMember->photo);
        $this->assertSame(3, $teamMember->sort_order);
        $this->assertTrue($teamMember->status);
    }

    public function test_team_member_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('team-members.store'), [
                'name' => '',
                'role' => 'Coach',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_team_member_role_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('team-members.store'), [
                'name' => 'Jane Doe',
                'role' => '',
            ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_team_member_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $teamMember = TeamMember::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('team-members.edit', $teamMember));

        $response->assertOk();
    }

    public function test_team_member_can_be_updated(): void
    {
        $user = User::factory()->create();
        $teamMember = TeamMember::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('team-members.update', $teamMember), [
                'name' => 'John Smith',
                'role' => 'Assistant Coach',
                'bio' => $teamMember->bio,
                'sort_order' => '7',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('team-members.index'));

        $teamMember->refresh();

        $this->assertSame('John Smith', $teamMember->name);
        $this->assertSame('Assistant Coach', $teamMember->role);
        $this->assertSame(7, $teamMember->sort_order);
        $this->assertFalse($teamMember->status);
    }

    public function test_team_member_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $teamMember = TeamMember::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('team-members.destroy', $teamMember));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('team-members.index'));

        $this->assertModelMissing($teamMember);
    }
}
