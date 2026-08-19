<?php

namespace Tests\Unit;

use App\Services\Tags\TagService;
use PHPUnit\Framework\TestCase;

class TagServiceTest extends TestCase
{
    public function test_normalize_trims_and_collapses_whitespace(): void
    {
        $service = new TagService;

        $this->assertSame('Running Shoes', $service->normalize('  Running    Shoes  '));
    }

    public function test_normalize_is_a_no_op_for_already_clean_input(): void
    {
        $service = new TagService;

        $this->assertSame('Sandals', $service->normalize('Sandals'));
    }
}
