<?php

namespace App\Actions\NewsletterSubscribers;

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\DB;

class UpdateNewsletterSubscriberAction
{
    /**
     * @param  array{email: string, status?: bool}  $data
     */
    public function handle(NewsletterSubscriber $subscriber, array $data): NewsletterSubscriber
    {
        DB::transaction(function () use ($subscriber, $data) {
            $subscriber->update($data);
        });

        return $subscriber;
    }
}
