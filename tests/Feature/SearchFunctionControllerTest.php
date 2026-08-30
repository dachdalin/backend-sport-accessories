<?php

namespace Tests\Feature;

use App\Enums\SearchFunctionVisibility;
use App\Models\SearchFunction;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SearchFunctionControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_search_functions_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $searchFunction = SearchFunction::factory()->create();

        $response = $this->actingAs($user)->get(route('search-functions.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('search-functions/Index')
                ->has('searchFunctions', 1)
                ->where('searchFunctions.0.id', $searchFunction->id)
                ->has('visibilities', 3),
            );
    }

    public function test_guests_cannot_view_search_functions(): void
    {
        $response = $this->get(route('search-functions.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_search_function_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('search-functions.store'), [
            'key' => 'New arrivals',
            'url' => '/products?sort=new',
            'visible_for' => 'admin',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('search-functions.index'));

        $this->assertDatabaseHas('search_functions', [
            'key' => 'New arrivals',
            'url' => '/products?sort=new',
            'visible_for' => 'admin',
        ]);
    }

    public function test_same_key_is_allowed_for_a_different_audience(): void
    {
        $user = User::factory()->create();
        SearchFunction::factory()->create(['key' => 'New arrivals', 'visible_for' => SearchFunctionVisibility::Admin]);

        $response = $this->actingAs($user)->post(route('search-functions.store'), [
            'key' => 'New arrivals',
            'url' => '/new',
            'visible_for' => 'customer',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_duplicate_key_for_the_same_audience_is_rejected(): void
    {
        $user = User::factory()->create();
        SearchFunction::factory()->create(['key' => 'New arrivals', 'visible_for' => SearchFunctionVisibility::Admin]);

        $response = $this->actingAs($user)->post(route('search-functions.store'), [
            'key' => 'New arrivals',
            'url' => '/new',
            'visible_for' => 'admin',
        ]);

        $response->assertSessionHasErrors('key');
    }

    public function test_url_with_an_unsafe_scheme_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('search-functions.store'), [
            'key' => 'Malicious',
            'url' => 'javascript:alert(1)',
            'visible_for' => 'admin',
        ]);

        $response->assertSessionHasErrors('url');
    }

    public function test_invalid_visibility_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('search-functions.store'), [
            'key' => 'New arrivals',
            'url' => '/new',
            'visible_for' => 'not-a-real-audience',
        ]);

        $response->assertSessionHasErrors('visible_for');
    }

    public function test_search_function_can_be_updated(): void
    {
        $user = User::factory()->create();
        $searchFunction = SearchFunction::factory()->create();

        $response = $this->actingAs($user)->put(route('search-functions.update', $searchFunction), [
            'key' => 'Updated key',
            'url' => '/updated',
            'visible_for' => 'seller',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('search-functions.index'));

        $searchFunction->refresh();

        $this->assertSame('Updated key', $searchFunction->key);
        $this->assertSame('/updated', $searchFunction->url);
        $this->assertSame(SearchFunctionVisibility::Seller, $searchFunction->visible_for);
    }

    public function test_search_function_can_keep_its_own_key_and_audience_when_updated(): void
    {
        $user = User::factory()->create();
        $searchFunction = SearchFunction::factory()->create(['key' => 'New arrivals', 'visible_for' => SearchFunctionVisibility::Admin]);

        $response = $this->actingAs($user)->put(route('search-functions.update', $searchFunction), [
            'key' => 'New arrivals',
            'url' => '/new',
            'visible_for' => 'admin',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_search_function_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $searchFunction = SearchFunction::factory()->create();

        $response = $this->actingAs($user)->delete(route('search-functions.destroy', $searchFunction));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('search-functions.index'));

        $this->assertModelMissing($searchFunction);
    }
}
