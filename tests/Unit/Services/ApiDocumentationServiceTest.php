<?php

namespace Tests\Unit\Services;

use App\Services\ApiDocumentationService;
use Tests\TestCase;

class ApiDocumentationServiceTest extends TestCase
{
    public function test_groups_are_returned_in_a_fixed_order_with_authentication_first(): void
    {
        $groups = app(ApiDocumentationService::class)->groups();

        $labels = array_column($groups, 'label');

        $this->assertSame('Authentication', $labels[0]);
        $this->assertContains('Catalog', $labels);
    }

    public function test_every_group_has_at_least_one_endpoint(): void
    {
        $groups = app(ApiDocumentationService::class)->groups();

        foreach ($groups as $group) {
            $this->assertNotEmpty($group['endpoints'], "Group [{$group['label']}] has no endpoints.");
        }
    }

    public function test_endpoint_metadata_is_derived_from_the_live_route_table(): void
    {
        $groups = app(ApiDocumentationService::class)->groups();

        $endpoints = collect($groups)->flatMap(fn (array $group) => $group['endpoints']);
        $login = $endpoints->firstWhere('name', 'api.v1.auth.login');

        $this->assertNotNull($login);
        $this->assertSame('POST', $login['method']);
        $this->assertSame('/api/v1/auth/login', $login['uri']);
        $this->assertSame('none', $login['auth']);
        $this->assertNotEmpty($login['fields']);
    }
}
