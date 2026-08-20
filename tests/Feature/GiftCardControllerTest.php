<?php

namespace Tests\Feature;

use App\Models\GiftCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_cards_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        GiftCard::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('gift-cards.index'));

        $response->assertOk();
    }

    public function test_gift_card_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('gift-cards.create'));

        $response->assertOk();
    }

    public function test_gift_card_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('gift-cards.store'), [
                'code' => 'gift-2026-xyz',
                'initial_balance' => '50.00',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('gift-cards.index'));

        $giftCard = GiftCard::sole();

        $this->assertSame('GIFT-2026-XYZ', $giftCard->code);
        $this->assertSame('50.00', $giftCard->balance);
        $this->assertTrue($giftCard->status);
    }

    public function test_gift_card_code_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('gift-cards.store'), [
                'code' => '',
            ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_gift_card_balance_cannot_exceed_initial_balance(): void
    {
        $user = User::factory()->create();
        $giftCard = GiftCard::factory()->create([
            'initial_balance' => 50,
            'balance' => 50,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('gift-cards.update', $giftCard), [
                'code' => $giftCard->code,
                'balance' => '100.00',
            ]);

        $response->assertSessionHasErrors('balance');
    }

    public function test_gift_card_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $giftCard = GiftCard::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('gift-cards.edit', $giftCard));

        $response->assertOk();
    }

    public function test_gift_card_can_be_updated(): void
    {
        $user = User::factory()->create();
        $giftCard = GiftCard::factory()->create([
            'initial_balance' => 50,
            'balance' => 50,
            'status' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('gift-cards.update', $giftCard), [
                'code' => $giftCard->code,
                'balance' => '25.00',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('gift-cards.index'));

        $giftCard->refresh();

        $this->assertSame('25.00', $giftCard->balance);
        $this->assertFalse($giftCard->status);
    }

    public function test_gift_card_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $giftCard = GiftCard::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('gift-cards.destroy', $giftCard));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('gift-cards.index'));

        $this->assertModelMissing($giftCard);
    }
}
