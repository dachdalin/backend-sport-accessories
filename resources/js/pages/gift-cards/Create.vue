<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import GiftCardController from '@/actions/App/Http/Controllers/Backend/GiftCardController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/gift-cards';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Gift cards',
                href: index(),
            },
            {
                title: 'Add gift card',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add gift card" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add gift card"
            description="Issue a new store gift card"
        />

        <Form
            v-bind="GiftCardController.store.form()"
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
                    placeholder="GIFT-2026-XYZ"
                />
                <InputError :message="errors.code" />
            </div>

            <div class="grid gap-2">
                <Label for="initial_balance">Initial balance</Label>
                <Input
                    id="initial_balance"
                    name="initial_balance"
                    type="number"
                    step="0.01"
                    min="1"
                    required
                    placeholder="50.00"
                />
                <InputError :message="errors.initial_balance" />
            </div>

            <div class="grid gap-2">
                <Label for="expires_at">Expires at</Label>
                <Input id="expires_at" name="expires_at" type="date" />
                <InputError :message="errors.expires_at" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create gift card</Button>
            </div>
        </Form>
    </div>
</template>
