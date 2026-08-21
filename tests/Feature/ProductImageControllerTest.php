<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_can_be_uploaded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.images.store', $product), [
                'image' => UploadedFile::fake()->image('gallery.jpg'),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $image = ProductImage::sole();

        $this->assertSame($product->id, $image->product_id);
        Storage::disk('public')->assertExists($image->image);
    }

    public function test_product_image_upload_requires_an_image(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.images.store', $product), []);

        $response->assertSessionHasErrors('image');
    }

    public function test_product_image_can_be_deleted(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $path = UploadedFile::fake()->image('gallery.jpg')->store('products/gallery', 'public');
        $image = $product->images()->create([
            'image' => $path,
            'image_storage_type' => 'public',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('images.destroy', $image));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertModelMissing($image);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_guest_cannot_upload_product_images(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('products.images.store', $product), [
            'image' => UploadedFile::fake()->image('gallery.jpg'),
        ]);

        $response->assertRedirect(route('login'));
    }
}
