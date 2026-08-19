<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import CurrencyController from '@/actions/App/Http/Controllers/Backend/CurrencyController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/currencies';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Currencies',
                href: index(),
            },
            {
                title: 'Add currency',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add currency" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add currency"
            description="Create a new currency for checkout"
        />

        <Form
            v-bind="CurrencyController.store.form()"
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
                    placeholder="US Dollar"
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
                        placeholder="USD"
                        maxlength="10"
                    />
                    <InputError :message="errors.code" />
                </div>

                <div class="grid gap-2">
                    <Label for="symbol">Symbol</Label>
                    <Input
                        id="symbol"
                        name="symbol"
                        required
                        placeholder="$"
                        maxlength="10"
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
                    placeholder="1.0000"
                />
                <InputError :message="errors.exchange_rate" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="false" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create currency</Button>
            </div>
        </Form>
    </div>
</template>
