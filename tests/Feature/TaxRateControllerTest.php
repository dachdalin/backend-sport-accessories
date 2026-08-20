<?php

namespace Tests\Feature;

use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxRateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tax_rates_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        TaxRate::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('tax-rates.index'));

        $response->assertOk();
    }

    public function test_tax_rate_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('tax-rates.create'));

        $response->assertOk();
    }

    public function test_tax_rate_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('tax-rates.store'), [
                'name' => 'Standard VAT',
                'region' => 'United Kingdom',
                'rate' => '20.00',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tax-rates.index'));

        $taxRate = TaxRate::sole();

        $this->assertSame('Standard VAT', $taxRate->name);
        $this->assertTrue($taxRate->status);
    }

    public function test_tax_rate_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('tax-rates.store'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_tax_rate_cannot_exceed_100_percent(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('tax-rates.store'), [
                'name' => 'Excessive Tax',
                'rate' => '150',
            ]);

        $response->assertSessionHasErrors('rate');
    }

    public function test_setting_a_tax_rate_as_default_unsets_the_previous_default(): void
    {
        $user = User::factory()->create();
        $existingDefault = TaxRate::factory()->create(['is_default' => true]);

        $response = $this
            ->actingAs($user)
            ->post(route('tax-rates.store'), [
                'name' => 'New Default',
                'rate' => '15.00',
                'is_default' => '1',
            ]);

        $response->assertSessionHasNoErrors();

        $existingDefault->refresh();
        $newDefault = TaxRate::where('name', 'New Default')->sole();

        $this->assertFalse($existingDefault->is_default);
        $this->assertTrue($newDefault->is_default);
    }

    public function test_tax_rate_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $taxRate = TaxRate::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('tax-rates.edit', $taxRate));

        $response->assertOk();
    }

    public function test_tax_rate_can_be_updated(): void
    {
        $user = User::factory()->create();
        $taxRate = TaxRate::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('tax-rates.update', $taxRate), [
                'name' => 'Reduced VAT',
                'rate' => '5.00',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tax-rates.index'));

        $taxRate->refresh();

        $this->assertSame('Reduced VAT', $taxRate->name);
        $this->assertFalse($taxRate->status);
    }

    public function test_tax_rate_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $taxRate = TaxRate::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('tax-rates.destroy', $taxRate));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tax-rates.index'));

        $this->assertModelMissing($taxRate);
    }
}
