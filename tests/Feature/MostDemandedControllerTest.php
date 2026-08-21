<?php

namespace Tests\Feature;

use App\Models\MostDemanded;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MostDemandedControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_most_demandeds_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        MostDemanded::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('most-demandeds.index'));

        $response->assertOk();
    }

    public function test_most_demanded_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('most-demandeds.create'));

        $response->assertOk();
    }

    public function test_most_demanded_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('most-demandeds.store'), [
                'product_id' => $product->id,
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('most-demandeds.index'));

        $mostDemanded = MostDemanded::sole();

        $this->assertSame($product->id, $mostDemanded->product_id);
        $this->assertSame('def.png', $mostDemanded->banner);
        $this->assertTrue($mostDemanded->status);
    }

    public function test_most_demanded_product_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('most-demandeds.store'), [
                'product_id' => '',
            ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_most_demanded_product_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('most-demandeds.store'), [
                'product_id' => 999999,
            ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_most_demanded_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $mostDemanded = MostDemanded::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('most-demandeds.edit', $mostDemanded));

        $response->assertOk();
    }

    public function test_most_demanded_can_be_updated(): void
    {
        $user = User::factory()->create();
        $mostDemanded = MostDemanded::factory()->create(['status' => false]);
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('most-demandeds.update', $mostDemanded), [
                'product_id' => $product->id,
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('most-demandeds.index'));

        $mostDemanded->refresh();

        $this->assertSame($product->id, $mostDemanded->product_id);
        $this->assertTrue($mostDemanded->status);
    }

    public function test_most_demanded_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $mostDemanded = MostDemanded::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('most-demandeds.destroy', $mostDemanded));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('most-demandeds.index'));

        $this->assertModelMissing($mostDemanded);
    }

    public function test_guest_cannot_access_most_demandeds(): void
    {
        $response = $this->get(route('most-demandeds.index'));

        $response->assertRedirect(route('login'));
    }
}
