<?php

namespace App\Actions\NewsletterSubscribers;

use App\Mail\NewsletterMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendNewsletterAction
{
    /**
     * @param  Collection<int, NewsletterSubscriber>  $subscribers
     * @param  array{subject: string, body: string}  $data
     */
    public function handle(Collection $subscribers, array $data): int
    {
        $subscribers->each(
            fn (NewsletterSubscriber $subscriber) => Mail::to($subscriber->email)->send(
                new NewsletterMail($data['subject'], $data['body']),
            ),
        );

        return $subscribers->count();
    }
}
