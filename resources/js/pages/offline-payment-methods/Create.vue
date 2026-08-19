<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import OfflinePaymentMethodController from '@/actions/App/Http/Controllers/Backend/OfflinePaymentMethodController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/offline-payment-methods';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Offline payment methods',
                href: index(),
            },
            {
                title: 'Add payment method',
                href: create(),
            },
        ],
    },
});

const textareaClass =
    'placeholder:text-muted-foreground dark:bg-input/30 border-input min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] md:text-sm';
</script>

<template>
    <Head title="Add payment method" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add payment method"
            description="Create a new manual payment option for checkout"
        />

        <Form
            v-bind="OfflinePaymentMethodController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="method_name">Name</Label>
                <Input
                    id="method_name"
                    name="method_name"
                    required
                    autofocus
                    placeholder="Bank Transfer"
                />
                <InputError :message="errors.method_name" />
            </div>

            <div class="grid gap-2">
                <Label for="method_fields"
                    >Fields customers must fill in</Label
                >
                <textarea
                    id="method_fields"
                    name="method_fields"
                    required
                    :class="textareaClass"
                    placeholder="Account name, account number, bank name"
                />
                <InputError :message="errors.method_fields" />
            </div>

            <div class="grid gap-2">
                <Label for="method_informations"
                    >Instructions shown to customers</Label
                >
                <textarea
                    id="method_informations"
                    name="method_informations"
                    required
                    :class="textareaClass"
                    placeholder="Transfer the order total to the account below and upload your receipt."
                />
                <InputError :message="errors.method_informations" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="false" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create payment method</Button>
            </div>
        </Form>
    </div>
</template>
