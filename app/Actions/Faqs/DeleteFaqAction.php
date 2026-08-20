<?php

namespace App\Actions\Faqs;

use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class DeleteFaqAction
{
    public function handle(Faq $faq): void
    {
        DB::transaction(function () use ($faq) {
            $faq->delete();
        });
    }
}
