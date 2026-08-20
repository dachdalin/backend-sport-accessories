<?php

namespace App\Actions\Pages;

use App\Models\Page;
use Illuminate\Support\Facades\DB;

class DeletePageAction
{
    public function handle(Page $page): void
    {
        DB::transaction(function () use ($page) {
            $page->delete();
        });
    }
}
