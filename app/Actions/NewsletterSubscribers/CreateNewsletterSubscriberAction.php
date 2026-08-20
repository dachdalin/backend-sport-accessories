<?php

namespace App\Actions\NewsletterSubscribers;

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\DB;

class CreateNewsletterSubscriberAction
{
    /**
     * @param  array{email: string, status?: bool}  $data
     */
    public function handle(array $data): NewsletterSubscriber
    {
        return DB::transaction(fn () => NewsletterSubscriber::create($data));
    }
}
