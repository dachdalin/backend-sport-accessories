<?php

namespace App\Actions\Faqs;

use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class CreateFaqAction
{
    /**
     * @param  array{question: string, answer: string, sort_order: int, status: bool}  $data
     */
    public function handle(array $data): Faq
    {
        return DB::transaction(fn () => Faq::create($data));
    }
}
