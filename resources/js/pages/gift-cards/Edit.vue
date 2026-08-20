<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import GiftCardController from '@/actions/App/Http/Controllers/Backend/GiftCardController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/gift-cards';

type GiftCard = {
    id: number;
    code: string;
    initial_balance: string;
    balance: string;
    expires_at: string | null;
    status: boolean;
};

const props = defineProps<{
    giftCard: GiftCard;
}>();

defineOptions({
    layout: (pageProps: { giftCard: GiftCard }) => ({
        breadcrumbs: [
            {
                title: 'Gift cards',
                href: index(),
            },
            {
                title: 'Edit gift card',
                href: edit(pageProps.giftCard),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit gift card" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit gift card"
            :description="`Update the details for ${props.giftCard.code}`"
        />

        <Form
            v-bind="GiftCardController.update.form(props.giftCard)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="code">Code</Label>
                <Input
                    id="code"
                    name="code"
                    required
                    autofocus
                    :default-value="props.giftCard.code"
                />
                <InputError :message="errors.code" />
            </div>

            <div class="grid gap-2">
                <Label for="balance"
                    >Remaining balance (max
                    {{ Number(props.giftCard.initial_balance).toFixed(2) }})</Label
                >
                <Input
                    id="balance"
                    name="balance"
                    type="number"
                    step="0.01"
                    min="0"
                    :max="props.giftCard.initial_balance"
                    required
                    :default-value="props.giftCard.balance"
                />
                <InputError :message="errors.balance" />
            </div>

            <div class="grid gap-2">
                <Label for="expires_at">Expires at</Label>
                <Input
                    id="expires_at"
                    name="expires_at"
                    type="date"
                    :default-value="props.giftCard.expires_at ?? ''"
                />
                <InputError :message="errors.expires_at" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.giftCard.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save gift card</Button>
            </div>
        </Form>
    </div>
</template>
