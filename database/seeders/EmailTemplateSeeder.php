<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Seed the transactional email templates the store ships with.
     */
    public function run(): void
    {
        foreach ($this->templates() as $template) {
            EmailTemplate::query()->updateOrCreate(
                ['name' => $template['name']],
                $template,
            );
        }
    }

    /**
     * @return array<int, array{name: string, subject: string, body: string, status: bool}>
     */
    private function templates(): array
    {
        return [
            [
                'name' => 'Order confirmation',
                'subject' => 'Your order {{order_number}} is confirmed',
                'body' => <<<'HTML'
                    <h2>Thanks for your order, {{customer_name}}</h2>
                    <p>We've got order <strong>{{order_number}}</strong> and we're getting it ready to ship.</p>
                    <ul>
                        <li>Order total: {{order_amount}}</li>
                        <li>Shipping to: {{shipping_address}}</li>
                    </ul>
                    <p>You'll get another email as soon as it's on its way. Questions? Just reply to this email.</p>
                    HTML,
                'status' => true,
            ],
            [
                'name' => 'Order shipped',
                'subject' => 'Your order {{order_number}} is on its way',
                'body' => <<<'HTML'
                    <h2>It's on the way</h2>
                    <p>Good news — order <strong>{{order_number}}</strong> has shipped.</p>
                    <p>Track it here: <a href="{{tracking_url}}">{{tracking_url}}</a></p>
                    <p>Estimated delivery: {{estimated_delivery}}</p>
                    HTML,
                'status' => true,
            ],
            [
                'name' => 'Welcome new customer',
                'subject' => 'Welcome to {{site_name}}',
                'body' => <<<'HTML'
                    <h2>Welcome, {{customer_name}}</h2>
                    <p>Thanks for creating an account with {{site_name}}. Here's what to do next:</p>
                    <ul>
                        <li>Browse new arrivals</li>
                        <li>Save your favorites to a wishlist</li>
                        <li>Set up fast checkout for next time</li>
                    </ul>
                    <p>See you on the field.</p>
                    HTML,
                'status' => true,
            ],
        ];
    }
}
