<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_suppliers_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Supplier::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('suppliers.index'));

        $response->assertOk();
    }

    public function test_supplier_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('suppliers.create'));

        $response->assertOk();
    }

    public function test_supplier_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('suppliers.store'), [
                'name' => 'Acme Sporting Goods Ltd',
                'contact_person' => 'Jane Smith',
                'email' => 'sales@acme.example',
                'phone' => '+44 20 1234 5678',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('suppliers.index'));

        $supplier = Supplier::sole();

        $this->assertSame('Acme Sporting Goods Ltd', $supplier->name);
        $this->assertTrue($supplier->status);
    }

    public function test_supplier_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('suppliers.store'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_supplier_email_must_be_unique(): void
    {
        $user = User::factory()->create();
        Supplier::factory()->create(['email' => 'sales@acme.example']);

        $response = $this
            ->actingAs($user)
            ->post(route('suppliers.store'), [
                'name' => 'Another Supplier',
                'email' => 'sales@acme.example',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_supplier_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('suppliers.edit', $supplier));

        $response->assertOk();
    }

    public function test_supplier_can_be_updated(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('suppliers.update', $supplier), [
                'name' => 'Updated Supplier Co',
                'email' => $supplier->email,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('suppliers.index'));

        $supplier->refresh();

        $this->assertSame('Updated Supplier Co', $supplier->name);
        $this->assertFalse($supplier->status);
    }

    public function test_supplier_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('suppliers.destroy', $supplier));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('suppliers.index'));

        $this->assertModelMissing($supplier);
    }
}
