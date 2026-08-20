<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouses_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Warehouse::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('warehouses.index'));

        $response->assertOk();
    }

    public function test_warehouse_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('warehouses.create'));

        $response->assertOk();
    }

    public function test_warehouse_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('warehouses.store'), [
                'name' => 'Central Warehouse',
                'code' => 'wh-001',
                'city' => 'London',
                'country' => 'United Kingdom',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('warehouses.index'));

        $warehouse = Warehouse::sole();

        $this->assertSame('Central Warehouse', $warehouse->name);
        $this->assertSame('WH-001', $warehouse->code);
        $this->assertTrue($warehouse->status);
    }

    public function test_warehouse_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('warehouses.store'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_warehouse_code_must_be_unique(): void
    {
        $user = User::factory()->create();
        Warehouse::factory()->create(['code' => 'WH-001']);

        $response = $this
            ->actingAs($user)
            ->post(route('warehouses.store'), [
                'name' => 'Second Warehouse',
                'code' => 'wh-001',
            ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_warehouse_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('warehouses.edit', $warehouse));

        $response->assertOk();
    }

    public function test_warehouse_can_be_updated(): void
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('warehouses.update', $warehouse), [
                'name' => 'Northern Depot',
                'code' => $warehouse->code,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('warehouses.index'));

        $warehouse->refresh();

        $this->assertSame('Northern Depot', $warehouse->name);
        $this->assertFalse($warehouse->status);
    }

    public function test_warehouse_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('warehouses.destroy', $warehouse));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('warehouses.index'));

        $this->assertModelMissing($warehouse);
    }
}
