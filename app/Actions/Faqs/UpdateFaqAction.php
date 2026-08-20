<?php

namespace App\Actions\Faqs;

use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class UpdateFaqAction
{
    /**
     * @param  array{question: string, answer: string, sort_order: int, status: bool}  $data
     */
    public function handle(Faq $faq, array $data): Faq
    {
        DB::transaction(function () use ($faq, $data) {
            $faq->update($data);
        });

        return $faq;
    }
}
