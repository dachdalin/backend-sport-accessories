<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TestimonialControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_testimonials_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Testimonial::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('testimonials.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('testimonials/Index')
                ->has('testimonials.data', 3),
            );
    }

    public function test_testimonials_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        Testimonial::factory()->count(16)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('testimonials.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('testimonials/Index')
                ->has('testimonials.data', 15),
            );
    }

    public function test_testimonial_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('testimonials.create'));

        $response->assertOk();
    }

    public function test_testimonial_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('testimonials.store'), [
                'customer_name' => 'Jane Doe',
                'customer_role' => 'Marathon runner',
                'content' => 'Great products, fast shipping!',
                'rating' => '5',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('testimonials.index'));

        $testimonial = Testimonial::sole();

        $this->assertSame('Jane Doe', $testimonial->customer_name);
        $this->assertSame('def.png', $testimonial->avatar);
        $this->assertSame(5, $testimonial->rating);
        $this->assertTrue($testimonial->status);
    }

    public function test_testimonial_customer_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('testimonials.store'), [
                'customer_name' => '',
            ]);

        $response->assertSessionHasErrors('customer_name');
    }

    public function test_testimonial_rating_must_be_between_one_and_five(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('testimonials.store'), [
                'customer_name' => 'John Doe',
                'content' => 'Solid gear.',
                'rating' => '6',
            ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_testimonial_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $testimonial = Testimonial::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('testimonials.edit', $testimonial));

        $response->assertOk();
    }

    public function test_testimonial_can_be_updated(): void
    {
        $user = User::factory()->create();
        $testimonial = Testimonial::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('testimonials.update', $testimonial), [
                'customer_name' => 'John Smith',
                'customer_role' => $testimonial->customer_role,
                'content' => $testimonial->content,
                'rating' => '3',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('testimonials.index'));

        $testimonial->refresh();

        $this->assertSame('John Smith', $testimonial->customer_name);
        $this->assertSame(3, $testimonial->rating);
        $this->assertFalse($testimonial->status);
    }

    public function test_testimonial_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $testimonial = Testimonial::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('testimonials.destroy', $testimonial));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('testimonials.index'));

        $this->assertModelMissing($testimonial);
    }
}
