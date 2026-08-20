<?php

namespace Tests\Feature;

use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DeliveryZoneControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_delivery_zones_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        DeliveryZone::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('delivery-zones.index'));

        $response->assertOk();
    }

    public function test_guests_cannot_view_delivery_zones(): void
    {
        $response = $this->get(route('delivery-zones.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_delivery_zone_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('delivery-zones.create'));

        $response->assertOk();
    }

    public function test_delivery_zone_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('delivery-zones.store'), [
                'zip_code' => '90210',
                'city' => 'Beverly Hills',
                'delivery_charge' => '7.50',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('delivery-zones.index'));

        $zone = DeliveryZone::sole();

        $this->assertSame('90210', $zone->zip_code);
        $this->assertSame('Beverly Hills', $zone->city);
        $this->assertSame('7.50', $zone->delivery_charge);
        $this->assertTrue($zone->status);
    }

    public function test_delivery_zone_zip_code_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('delivery-zones.store'), [
                'zip_code' => '',
                'delivery_charge' => '5',
            ]);

        $response->assertSessionHasErrors('zip_code');
    }

    public function test_delivery_zone_zip_code_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        DeliveryZone::factory()->create(['zip_code' => '90210']);

        $response = $this
            ->actingAs($user)
            ->post(route('delivery-zones.store'), [
                'zip_code' => '90210',
                'delivery_charge' => '5',
            ]);

        $response->assertSessionHasErrors('zip_code');
    }

    public function test_delivery_zone_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $zone = DeliveryZone::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('delivery-zones.edit', $zone));

        $response->assertOk();
    }

    public function test_delivery_zone_can_be_updated(): void
    {
        $user = User::factory()->create();
        $zone = DeliveryZone::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('delivery-zones.update', $zone), [
                'zip_code' => '10001',
                'city' => 'New York',
                'delivery_charge' => '12.00',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('delivery-zones.index'));

        $zone->refresh();

        $this->assertSame('10001', $zone->zip_code);
        $this->assertSame('New York', $zone->city);
        $this->assertFalse($zone->status);
    }

    public function test_delivery_zone_zip_code_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        DeliveryZone::factory()->create(['zip_code' => '90210']);
        $zone = DeliveryZone::factory()->create(['zip_code' => '10001']);

        $response = $this
            ->actingAs($user)
            ->put(route('delivery-zones.update', $zone), [
                'zip_code' => '90210',
                'delivery_charge' => $zone->delivery_charge,
            ]);

        $response->assertSessionHasErrors('zip_code');
    }

    public function test_delivery_zone_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $zone = DeliveryZone::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('delivery-zones.destroy', $zone));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('delivery-zones.index'));

        $this->assertModelMissing($zone);
    }
}
