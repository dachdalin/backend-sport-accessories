<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Resources backed by a standard view/create/edit/delete Route::resource() group in
     * routes/web.php. Keep this in sync when a new admin resource is added.
     *
     * @var array<int, string>
     */
    private array $crudResources = [
        'attributes', 'colors', 'sizes', 'materials', 'brands', 'banners', 'tags',
        'currencies', 'customers', 'categories', 'blog categories', 'blogs', 'help topics',
        'shipping methods', 'shipping addresses', 'search functions', 'offline payment methods',
        'social medias', 'credentials', 'testimonials', 'faqs', 'coupons',
        'newsletter subscribers', 'tax rates', 'warehouses', 'return policies', 'suppliers',
        'pages', 'store locations', 'gift cards', 'team members', 'loyalty tiers',
        'job openings', 'analytic scripts', 'delivery zones', 'products', 'orders', 'reviews',
        'wishlists', 'contacts', 'support tickets', 'email templates', 'flash deals',
        'feature deals', 'stock clearance setups', 'deal of the days', 'most demandeds',
        'refund requests', 'roles', 'users',
    ];

    /**
     * Catalog, merchandising, and sales resources a hands-on operations role
     * needs full control over — distinct from the read-only `manager` role
     * above, which sees everything but can't change anything. No access to
     * Users/Roles, Customers, content pages, or customer-service resources.
     *
     * @var array<int, string>
     */
    private array $catalogManagerCrudResources = [
        'products', 'categories', 'attributes', 'colors', 'sizes', 'materials', 'brands', 'tags',
        'coupons', 'flash deals', 'feature deals', 'deal of the days', 'most demandeds',
        'stock clearance setups', 'gift cards', 'orders', 'refund requests', 'reviews',
        'shipping methods', 'delivery zones',
    ];

    /**
     * Resources a customer-support agent needs full control over, to run
     * their ticket/contact workflow end to end.
     *
     * @var array<int, string>
     */
    private array $supportCrudResources = ['support tickets', 'contacts'];

    /**
     * Resources a support agent only needs read access to, for context on who
     * they're helping — not to edit customer records or orders directly.
     *
     * @var array<int, string>
     */
    private array $supportViewOnlyResources = ['customers', 'orders'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->crudResources as $resource) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                Permission::findOrCreate("{$action} {$resource}");
            }
        }

        Permission::findOrCreate('view dashboard');
        Permission::findOrCreate('view business settings');
        Permission::findOrCreate('edit business settings');
        Permission::findOrCreate('view messages');
        Permission::findOrCreate('create messages');

        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions(Permission::all());

        // Read-only role: sees every resource but cannot create, edit, or delete anything,
        // and has no access to business settings or user/role management.
        $manager = Role::findOrCreate('manager');
        $manager->syncPermissions(
            Permission::query()
                ->where('name', 'like', 'view %')
                ->whereNotIn('name', ['view users', 'view roles', 'view business settings'])
                ->get()
        );

        $catalogManager = Role::findOrCreate('catalog manager');
        $catalogManager->syncPermissions([
            ...$this->crudPermissionNames($this->catalogManagerCrudResources),
            'view dashboard',
        ]);

        $support = Role::findOrCreate('support');
        $support->syncPermissions([
            ...$this->crudPermissionNames($this->supportCrudResources),
            ...array_map(fn (string $resource) => "view {$resource}", $this->supportViewOnlyResources),
            'view dashboard',
        ]);
    }

    /**
     * Expand resource names into their full view/create/edit/delete permission names.
     *
     * @param  array<int, string>  $resources
     * @return array<int, string>
     */
    private function crudPermissionNames(array $resources): array
    {
        $names = [];

        foreach ($resources as $resource) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $names[] = "{$action} {$resource}";
            }
        }

        return $names;
    }
}
