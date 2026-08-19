<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import CurrencyController from '@/actions/App/Http/Controllers/Backend/CurrencyController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/currencies';

type Currency = {
    id: number;
    name: string;
    symbol: string;
    code: string;
    exchange_rate: string;
    status: boolean;
};

defineProps<{
    currency: Currency;
}>();

defineOptions({
    layout: (pageProps: { currency: Currency }) => ({
        breadcrumbs: [
            {
                title: 'Currencies',
                href: index(),
            },
            {
                title: 'Edit currency',
                href: edit(pageProps.currency),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit currency" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit currency"
            :description="`Update the details for ${currency.name}`"
        />

        <Form
            v-bind="CurrencyController.update.form(currency)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    autofocus
                    :default-value="currency.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="code">Code</Label>
                    <Input
                        id="code"
                        name="code"
                        required
                        maxlength="10"
                        :default-value="currency.code"
                    />
                    <InputError :message="errors.code" />
                </div>

                <div class="grid gap-2">
                    <Label for="symbol">Symbol</Label>
                    <Input
                        id="symbol"
                        name="symbol"
                        required
                        maxlength="10"
                        :default-value="currency.symbol"
                    />
                    <InputError :message="errors.symbol" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="exchange_rate">Exchange rate</Label>
                <Input
                    id="exchange_rate"
                    name="exchange_rate"
                    type="number"
                    step="0.0001"
                    min="0.0001"
                    required
                    :default-value="currency.exchange_rate"
                />
                <InputError :message="errors.exchange_rate" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="currency.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save currency</Button>
            </div>
        </Form>
    </div>
</template>
