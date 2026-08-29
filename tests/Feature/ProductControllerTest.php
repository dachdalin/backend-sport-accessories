<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Product::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/Index')
                ->has('products.data', 3),
            );
    }

    public function test_products_index_can_be_filtered_by_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $category->id]);
        Product::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.index', ['category_id' => $category->id]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/Index')
                ->has('products.data', 2)
                ->where('filters.category_id', (string) $category->id),
            );
    }

    public function test_products_index_can_be_filtered_by_brand(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();
        Product::factory()->count(2)->create(['brand_id' => $brand->id]);
        Product::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.index', ['brand_id' => $brand->id]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/Index')
                ->has('products.data', 2)
                ->where('filters.brand_id', (string) $brand->id),
            );
    }

    public function test_product_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.create'));

        $response->assertOk();
    }

    public function test_product_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Running Shoes Pro',
                'unit_price' => '99.99',
                'current_stock' => 50,
                'minimum_order_qty' => 1,
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $product = Product::sole();

        $this->assertSame('Running Shoes Pro', $product->name);
        $this->assertSame('running-shoes-pro', $product->slug);
        $this->assertSame('99.99', $product->unit_price);
        $this->assertSame('def.png', $product->thumbnail);
        $this->assertTrue($product->status);
    }

    public function test_product_can_be_created_with_gallery_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Running Shoes Pro',
                'unit_price' => '99.99',
                'current_stock' => 50,
                'minimum_order_qty' => 1,
                'status' => '1',
                'images' => [
                    UploadedFile::fake()->image('gallery-1.jpg'),
                    UploadedFile::fake()->image('gallery-2.jpg'),
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $product = Product::sole();

        $this->assertCount(2, $product->images);
        $product->images->each(
            fn ($image) => Storage::disk('public')->assertExists($image->image),
        );
    }

    public function test_product_can_be_created_with_variants_and_attributes(): void
    {
        $user = User::factory()->create();
        $red = Color::factory()->create(['name' => 'Red']);
        $blue = Color::factory()->create(['name' => 'Blue']);
        $sizeM = Size::factory()->create(['name' => 'M']);
        $cotton = Material::factory()->create();
        $waterproof = Attribute::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Running Shoes Pro',
                'unit_price' => '99.99',
                'current_stock' => 50,
                'minimum_order_qty' => 1,
                'status' => '1',
                'variants' => [
                    ['color_id' => $red->id, 'size_id' => $sizeM->id, 'material_id' => $cotton->id, 'stock' => 10],
                    ['color_id' => $blue->id, 'size_id' => $sizeM->id, 'material_id' => $cotton->id, 'stock' => 5],
                ],
                'attributes' => [$waterproof->id],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $product = Product::sole();

        $this->assertCount(2, $product->variants);
        $this->assertSame(10, $product->variants()->where('color_id', $red->id)->sole()->stock);
        $this->assertTrue($product->attributes()->where('attributes.id', $waterproof->id)->exists());
    }

    public function test_product_variant_combination_must_be_unique(): void
    {
        $user = User::factory()->create();
        $red = Color::factory()->create();
        $sizeM = Size::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Running Shoes Pro',
                'unit_price' => '99.99',
                'current_stock' => 50,
                'minimum_order_qty' => 1,
                'variants' => [
                    ['color_id' => $red->id, 'size_id' => $sizeM->id, 'stock' => 10],
                    ['color_id' => $red->id, 'size_id' => $sizeM->id, 'stock' => 5],
                ],
            ]);

        $response->assertSessionHasErrors('variants.1');
    }

    public function test_product_variant_requires_at_least_one_attribute_selected(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Running Shoes Pro',
                'unit_price' => '99.99',
                'current_stock' => 50,
                'minimum_order_qty' => 1,
                'variants' => [
                    ['color_id' => null, 'size_id' => null, 'material_id' => null, 'stock' => 10],
                ],
            ]);

        $response->assertSessionHasErrors('variants.0');
    }

    public function test_product_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => '',
                'unit_price' => '10.00',
                'current_stock' => 0,
                'minimum_order_qty' => 1,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_unit_price_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Test Product',
                'unit_price' => '',
                'current_stock' => 0,
                'minimum_order_qty' => 1,
            ]);

        $response->assertSessionHasErrors('unit_price');
    }

    public function test_product_name_must_be_unique(): void
    {
        $user = User::factory()->create();
        Product::factory()->create(['name' => 'Existing Product']);

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Existing Product',
                'unit_price' => '10.00',
                'current_stock' => 0,
                'minimum_order_qty' => 1,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.edit', $product));

        $response->assertOk();
    }

    public function test_product_can_be_updated(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('products.update', $product), [
                'name' => 'Updated Shoes',
                'unit_price' => '149.99',
                'current_stock' => 25,
                'minimum_order_qty' => 2,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $product->refresh();

        $this->assertSame('Updated Shoes', $product->name);
        $this->assertSame('updated-shoes', $product->slug);
        $this->assertSame('149.99', $product->unit_price);
        $this->assertSame(25, $product->current_stock);
        $this->assertFalse($product->status);
    }

    public function test_product_variants_are_replaced_on_update(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $product->variants()->create(['color_id' => Color::factory()->create()->id, 'stock' => 3]);
        $newColor = Color::factory()->create();
        $newSize = Size::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('products.update', $product), [
                'name' => $product->name,
                'unit_price' => $product->unit_price,
                'current_stock' => $product->current_stock,
                'minimum_order_qty' => $product->minimum_order_qty,
                'variants' => [
                    ['color_id' => $newColor->id, 'size_id' => $newSize->id, 'stock' => 20],
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $product->refresh();

        $this->assertCount(1, $product->variants);
        $this->assertSame($newColor->id, $product->variants->sole()->color_id);
        $this->assertSame(20, $product->variants->sole()->stock);
    }

    public function test_product_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('products.destroy', $product));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $this->assertModelMissing($product);
    }

    public function test_guest_cannot_access_products(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertRedirect(route('login'));
    }
}
