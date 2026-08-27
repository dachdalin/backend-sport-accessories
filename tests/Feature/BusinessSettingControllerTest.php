<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BusinessSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUserWithPermission(string $permission): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate($permission));

        $this->actingAs($user);

        return $user;
    }

    /**
     * UserFactory assigns the admin role by default (so existing bare-user tests keep passing),
     * which bypasses every permission check via the Gate::before admin override. Access-denial
     * tests need a genuinely role-less user, so strip roles here instead of using the factory directly.
     */
    private function actingAsUserWithoutPermissions(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([]);

        $this->actingAs($user);

        return $user;
    }

    public function test_business_settings_page_is_displayed(): void
    {
        $this->actingAsUserWithPermission('view business settings');

        $response = $this->get(route('business-settings.edit'));

        $response->assertOk();
    }

    public function test_users_without_the_view_permission_cannot_view_business_settings(): void
    {
        $this->actingAsUserWithoutPermissions();

        $response = $this->get(route('business-settings.edit'));

        $response->assertForbidden();
    }

    public function test_business_settings_can_be_updated(): void
    {
        $this->actingAsUserWithPermission('edit business settings');

        $response = $this->patch(route('business-settings.update'), [
            'site_name' => 'Acme Sports',
            'contact_email' => 'support@acme.test',
            'contact_phone' => '+1 555 000 0000',
            'address' => '123 Main Street',
            'currency_symbol' => '$',
            'minimum_order_amount' => '10.00',
            'free_delivery_over_amount' => '50.00',
            'tax_included_in_price' => true,
            'maintenance_mode' => false,
            'copyright_text' => '© 2026 Acme Sports',
            'meta_title' => 'Acme Sports',
            'meta_description' => 'Gear up with Acme Sports.',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('business-settings.edit'));

        $this->assertSame('Acme Sports', BusinessSetting::query()->where('key', 'site_name')->value('value'));
        $this->assertSame('1', BusinessSetting::query()->where('key', 'tax_included_in_price')->value('value'));
        $this->assertSame('0', BusinessSetting::query()->where('key', 'maintenance_mode')->value('value'));
    }

    public function test_users_without_the_edit_permission_cannot_update_business_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('business-settings.update'), [
            'site_name' => 'Acme Sports',
            'currency_symbol' => '$',
        ]);

        $response->assertForbidden();
    }

    public function test_business_settings_site_name_is_required(): void
    {
        $this->actingAsUserWithPermission('edit business settings');

        $response = $this->patch(route('business-settings.update'), [
            'site_name' => '',
            'currency_symbol' => '$',
        ]);

        $response->assertSessionHasErrors('site_name');
    }

    public function test_guest_cannot_access_business_settings(): void
    {
        $response = $this->get(route('business-settings.edit'));

        $response->assertRedirect(route('login'));
    }
}
