<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TaxRateController from '@/actions/App/Http/Controllers/Backend/TaxRateController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/tax-rates';

type TaxRate = {
    id: number;
    name: string;
    region: string | null;
    rate: string;
    is_default: boolean;
    status: boolean;
};

const props = defineProps<{
    taxRate: TaxRate;
}>();

defineOptions({
    layout: (pageProps: { taxRate: TaxRate }) => ({
        breadcrumbs: [
            {
                title: 'Tax rates',
                href: index(),
            },
            {
                title: 'Edit tax rate',
                href: edit(pageProps.taxRate),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit tax rate" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit tax rate"
            :description="`Update the details for ${props.taxRate.name}`"
        />

        <Form
            v-bind="TaxRateController.update.form(props.taxRate)"
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
                    :default-value="props.taxRate.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="region">Region</Label>
                <Input
                    id="region"
                    name="region"
                    :default-value="props.taxRate.region ?? ''"
                    placeholder="e.g. United Kingdom"
                />
                <InputError :message="errors.region" />
            </div>

            <div class="grid gap-2">
                <Label for="rate">Rate (%)</Label>
                <Input
                    id="rate"
                    name="rate"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    required
                    :default-value="props.taxRate.rate"
                />
                <InputError :message="errors.rate" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="is_default"
                    name="is_default"
                    :default-value="props.taxRate.is_default"
                />
                <Label for="is_default">Set as default rate</Label>
                <InputError :message="errors.is_default" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.taxRate.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save tax rate</Button>
            </div>
        </Form>
    </div>
</template>
