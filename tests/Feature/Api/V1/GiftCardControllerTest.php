<?php

namespace Tests\Feature\Api\V1;

use App\Models\GiftCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_gift_card_is_checked(): void
    {
        $giftCard = GiftCard::factory()->create([
            'code' => 'GIFT100',
            'initial_balance' => 100,
            'balance' => 75,
        ]);

        $response = $this->postJson(route('api.v1.gift-cards.check'), [
            'code' => 'gift100',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.code', $giftCard->code)
            ->assertJsonPath('data.balance', '75.00');
    }

    public function test_unknown_code_is_rejected(): void
    {
        $response = $this->postJson(route('api.v1.gift-cards.check'), [
            'code' => 'MISSING',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_inactive_gift_card_is_rejected(): void
    {
        $giftCard = GiftCard::factory()->create(['status' => false]);

        $response = $this->postJson(route('api.v1.gift-cards.check'), [
            'code' => $giftCard->code,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_expired_gift_card_is_rejected(): void
    {
        $giftCard = GiftCard::factory()->create(['expires_at' => now()->subDay()]);

        $response = $this->postJson(route('api.v1.gift-cards.check'), [
            'code' => $giftCard->code,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_code_is_required(): void
    {
        $response = $this->postJson(route('api.v1.gift-cards.check'), [
            'code' => '',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }
}
