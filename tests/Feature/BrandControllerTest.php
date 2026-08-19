<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_brands_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Brand::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('brands.index'));

        $response->assertOk();
    }

    public function test_brand_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('brands.create'));

        $response->assertOk();
    }

    public function test_brand_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('brands.store'), [
                'name' => 'Nike',
                'image_alt_text' => 'Nike logo',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('brands.index'));

        $brand = Brand::sole();

        $this->assertSame('Nike', $brand->name);
        $this->assertSame('def.png', $brand->image);
        $this->assertTrue($brand->status);
    }

    public function test_brand_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('brands.store'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_brand_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('brands.edit', $brand));

        $response->assertOk();
    }

    public function test_brand_can_be_updated(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('brands.update', $brand), [
                'name' => 'Adidas',
                'image_alt_text' => $brand->image_alt_text,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('brands.index'));

        $brand->refresh();

        $this->assertSame('Adidas', $brand->name);
        $this->assertFalse($brand->status);
    }

    public function test_brand_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('brands.destroy', $brand));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('brands.index'));

        $this->assertModelMissing($brand);
    }
}
