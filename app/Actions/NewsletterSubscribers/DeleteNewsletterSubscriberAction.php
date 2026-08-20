<?php

namespace App\Actions\NewsletterSubscribers;

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\DB;

class DeleteNewsletterSubscriberAction
{
    public function handle(NewsletterSubscriber $subscriber): void
    {
        DB::transaction(function () use ($subscriber) {
            $subscriber->delete();
        });
    }
}
