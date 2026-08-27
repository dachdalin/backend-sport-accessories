<?php

namespace App\Services;

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionMethod;

class ApiDocumentationService
{
    /**
     * Human labels for endpoint groups, keyed by the route-name segments between
     * `api.v1.` and the trailing action (e.g. `products.reviews`). Anything not
     * listed here falls back to a title-cased version of that key.
     *
     * @var array<string, string>
     */
    private const GROUP_LABELS = [
        'auth' => 'Authentication',
        'products' => 'Catalog',
        'products.reviews' => 'Catalog',
        'banners' => 'Catalog',
        'brands' => 'Catalog',
        'categories' => 'Catalog',
        'flash-deals' => 'Catalog',
        'blog-categories' => 'Catalog',
        'blogs' => 'Catalog',
        'testimonials' => 'Catalog',
        'pages' => 'Catalog',
        'faqs' => 'Catalog',
        'help-topics' => 'Catalog',
        'store-locations' => 'Catalog',
        'job-openings' => 'Catalog',
        'shipping-methods' => 'Catalog',
        'delivery-zones' => 'Catalog',
        'offline-payment-methods' => 'Catalog',
        'social-medias' => 'Catalog',
        'currencies' => 'Catalog',
        'coupons' => 'Cart & checkout',
        'gift-cards' => 'Cart & checkout',
        'cart-items' => 'Cart & checkout',
        'orders' => 'Cart & checkout',
        'profile' => 'Account',
        'wishlists' => 'Account',
        'shipping-addresses' => 'Account',
        'support-tickets' => 'Support',
        'contacts' => 'Support',
        'newsletter-subscribers' => 'Support',
    ];

    /**
     * @var array<int, string>
     */
    private const GROUP_ORDER = ['Authentication', 'Catalog', 'Cart & checkout', 'Account', 'Support'];

    /**
     * Request body fields for write endpoints, keyed by route name. Mirrors each
     * endpoint's Form Request rules() one-for-one — update this whenever that
     * Form Request's fields change, it does not introspect them at runtime.
     *
     * @var array<string, array<int, array{name: string, type: string, required: bool, example: mixed, notes: ?string}>>
     */
    private const REQUEST_FIELDS = [
        'api.v1.auth.register' => [
            ['name' => 'name', 'type' => 'string', 'required' => true, 'example' => 'Jane Doe', 'notes' => null],
            ['name' => 'email', 'type' => 'string', 'required' => true, 'example' => 'jane@example.com', 'notes' => 'Must be unique.'],
            ['name' => 'password', 'type' => 'string', 'required' => true, 'example' => 'Passw0rd!', 'notes' => null],
            ['name' => 'password_confirmation', 'type' => 'string', 'required' => true, 'example' => 'Passw0rd!', 'notes' => 'Must match password.'],
            ['name' => 'phone', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'address', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
        ],
        'api.v1.auth.login' => [
            ['name' => 'email', 'type' => 'string', 'required' => true, 'example' => 'jane@example.com', 'notes' => null],
            ['name' => 'password', 'type' => 'string', 'required' => true, 'example' => 'Passw0rd!', 'notes' => null],
            ['name' => 'device_name', 'type' => 'string', 'required' => true, 'example' => 'iPhone 15', 'notes' => 'Labels the issued token, e.g. the device it came from.'],
        ],
        'api.v1.coupons.apply' => [
            ['name' => 'code', 'type' => 'string', 'required' => true, 'example' => 'SAVE10', 'notes' => null],
            ['name' => 'order_amount', 'type' => 'number', 'required' => true, 'example' => 100, 'notes' => 'Must be at least 0.01.'],
        ],
        'api.v1.gift-cards.check' => [
            ['name' => 'code', 'type' => 'string', 'required' => true, 'example' => 'GIFT100', 'notes' => null],
        ],
        'api.v1.contacts.store' => [
            ['name' => 'name', 'type' => 'string', 'required' => true, 'example' => 'Jane Doe', 'notes' => null],
            ['name' => 'email', 'type' => 'string', 'required' => true, 'example' => 'jane@example.com', 'notes' => null],
            ['name' => 'phone', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'subject', 'type' => 'string', 'required' => true, 'example' => 'Question about an order', 'notes' => null],
            ['name' => 'message', 'type' => 'string', 'required' => true, 'example' => 'Hi, I had a question...', 'notes' => null],
        ],
        'api.v1.newsletter-subscribers.store' => [
            ['name' => 'email', 'type' => 'string', 'required' => true, 'example' => 'jane@example.com', 'notes' => 'Must be unique.'],
        ],
        'api.v1.profile.update' => [
            ['name' => 'name', 'type' => 'string', 'required' => true, 'example' => 'Jane Doe', 'notes' => null],
            ['name' => 'email', 'type' => 'string', 'required' => true, 'example' => 'jane@example.com', 'notes' => 'Must be unique.'],
            ['name' => 'phone', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'address', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
        ],
        'api.v1.wishlists.store' => [
            ['name' => 'product_id', 'type' => 'integer', 'required' => true, 'example' => 1, 'notes' => 'Must be an existing product not already wishlisted by this customer.'],
        ],
        'api.v1.cart-items.store' => [
            ['name' => 'product_id', 'type' => 'integer', 'required' => true, 'example' => 1, 'notes' => null],
            ['name' => 'quantity', 'type' => 'integer', 'required' => true, 'example' => 1, 'notes' => 'Minimum 1.'],
        ],
        'api.v1.products.reviews.store' => [
            ['name' => 'rating', 'type' => 'integer', 'required' => true, 'example' => 5, 'notes' => 'Between 1 and 5.'],
            ['name' => 'comment', 'type' => 'string', 'required' => true, 'example' => 'Great product!', 'notes' => null],
        ],
        'api.v1.support-tickets.store' => [
            ['name' => 'subject', 'type' => 'string', 'required' => true, 'example' => 'Order not received', 'notes' => null],
            ['name' => 'type', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'priority', 'type' => 'string', 'required' => true, 'example' => 'medium', 'notes' => 'One of: low, medium, high.'],
            ['name' => 'description', 'type' => 'string', 'required' => true, 'example' => 'My order has not arrived...', 'notes' => null],
            ['name' => 'attachment', 'type' => 'file', 'required' => false, 'example' => null, 'notes' => 'jpg, jpeg, png, webp, or pdf — max 5MB. Not testable from this JSON console.'],
        ],
        'api.v1.shipping-addresses.store' => [
            ['name' => 'contact_person_name', 'type' => 'string', 'required' => true, 'example' => 'Jane Doe', 'notes' => null],
            ['name' => 'phone', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'address_type', 'type' => 'string', 'required' => true, 'example' => 'home', 'notes' => 'One of: home, office, other.'],
            ['name' => 'address', 'type' => 'string', 'required' => true, 'example' => '123 Main St', 'notes' => null],
            ['name' => 'city', 'type' => 'string', 'required' => true, 'example' => 'Springfield', 'notes' => null],
            ['name' => 'state', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'zip', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'country', 'type' => 'string', 'required' => true, 'example' => 'USA', 'notes' => null],
            ['name' => 'is_default', 'type' => 'boolean', 'required' => false, 'example' => false, 'notes' => null],
        ],
        'api.v1.orders.store' => [
            ['name' => 'shipping_address_id', 'type' => 'integer', 'required' => true, 'example' => 1, 'notes' => 'Must belong to the authenticated customer.'],
            ['name' => 'shipping_method_id', 'type' => 'integer', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'payment_method', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'coupon_code', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
            ['name' => 'order_note', 'type' => 'string', 'required' => false, 'example' => null, 'notes' => null],
        ],
    ];

    /**
     * Every documented endpoint, grouped for the API docs page. Endpoint metadata
     * (method, path, path params, auth requirement) and each endpoint's purpose are
     * read straight from the live route table and controller docblocks, so this
     * can never drift out of sync with the actual API. Only the request-body field
     * lists above are hand-maintained.
     *
     * @return array<int, array{label: string, endpoints: array<int, array<string, mixed>>}>
     */
    public function groups(): array
    {
        $endpoints = collect(Route::getRoutes())
            ->filter(fn (RoutingRoute $route) => $route->getName() && Str::startsWith($route->getName(), 'api.v1.'))
            ->map(fn (RoutingRoute $route) => $this->describe($route))
            ->filter()
            ->sortBy('uri')
            ->values();

        return $endpoints
            ->groupBy('group')
            ->sortBy(function ($_, $group) {
                $position = array_search($group, self::GROUP_ORDER, true);

                return $position === false ? 99 : $position;
            })
            ->map(fn ($groupEndpoints, $group) => [
                'label' => $group,
                'endpoints' => $groupEndpoints->values()->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function describe(RoutingRoute $route): ?array
    {
        $action = $route->getActionName();

        if (! str_contains($action, '@')) {
            return null;
        }

        [$class, $method] = explode('@', $action);

        if (! method_exists($class, $method)) {
            return null;
        }

        $doc = $this->parseDocComment((new ReflectionMethod($class, $method))->getDocComment() ?: null);

        $name = $route->getName();
        $parts = explode('.', Str::after($name, 'api.v1.'));
        array_pop($parts);
        $resource = implode('.', $parts);

        $middleware = $route->gatherMiddleware();
        $methods = array_values(array_diff($route->methods(), ['HEAD']));

        return [
            'name' => $name,
            'method' => $methods[0] ?? 'GET',
            'uri' => '/'.ltrim($route->uri(), '/'),
            'params' => $this->pathParams($route->uri()),
            'auth' => in_array('auth:sanctum', $middleware, true) ? 'sanctum' : 'none',
            'throttle' => collect($middleware)->first(fn ($m) => str_starts_with((string) $m, 'throttle:')),
            'group' => self::GROUP_LABELS[$resource] ?? Str::headline(str_replace('.', ' ', $resource)),
            'summary' => $doc['summary'],
            'details' => $doc['details'],
            'fields' => self::REQUEST_FIELDS[$name] ?? [],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function pathParams(string $uri): array
    {
        preg_match_all('/\{([^}]+)\}/', $uri, $matches);

        return $matches[1];
    }

    /**
     * Split a docblock into its opening summary paragraph and any further detail
     * paragraphs, dropping `@tag` lines entirely.
     *
     * @return array{summary: ?string, details: ?string}
     */
    private function parseDocComment(?string $doc): array
    {
        if (! $doc) {
            return ['summary' => null, 'details' => null];
        }

        $lines = explode("\n", (string) preg_replace('#^/\*\*|\*/$#', '', $doc));
        $paragraphs = [];
        $current = [];

        foreach ($lines as $line) {
            $line = trim(trim($line), "* \t");

            if (str_starts_with($line, '@')) {
                break;
            }

            if ($line === '') {
                if ($current !== []) {
                    $paragraphs[] = implode(' ', $current);
                    $current = [];
                }

                continue;
            }

            $current[] = $line;
        }

        if ($current !== []) {
            $paragraphs[] = implode(' ', $current);
        }

        return [
            'summary' => $paragraphs[0] ?? null,
            'details' => count($paragraphs) > 1 ? implode("\n\n", array_slice($paragraphs, 1)) : null,
        ];
    }
}
