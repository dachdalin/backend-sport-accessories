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

        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions(Permission::all());
    }
}
