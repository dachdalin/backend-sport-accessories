<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import MostDemandedController from '@/actions/App/Http/Controllers/Backend/MostDemandedController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { edit, index } from '@/routes/most-demandeds';

interface SelectOption {
    value: number | string;
    label: string;
}

type MostDemanded = {
    id: number;
    banner: string;
    product_id: number;
    status: boolean;
};

defineProps<{
    mostDemanded: MostDemanded;
    products: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { mostDemanded: MostDemanded }) => ({
        breadcrumbs: [
            {
                title: 'Most demanded',
                href: index(),
            },
            {
                title: 'Edit entry',
                href: edit(pageProps.mostDemanded),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit entry" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit entry"
            description="Update this most demanded product"
        />

        <Form
            v-bind="MostDemandedController.update.form(mostDemanded)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="product_id">Product</Label>
                <Select
                    name="product_id"
                    :default-value="String(mostDemanded.product_id)"
                >
                    <SelectTrigger id="product_id" class="w-full">
                        <SelectValue placeholder="Select product" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in products"
                            :key="option.value"
                            :value="String(option.value)"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.product_id" />
            </div>

            <div class="grid gap-2">
                <img
                    :src="`/storage/${mostDemanded.banner}`"
                    alt="Current banner"
                    class="size-16 rounded object-cover"
                />
                <Label for="banner">Replace banner image</Label>
                <Input id="banner" name="banner" type="file" accept="image/*" />
                <InputError :message="errors.banner" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="mostDemanded.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Update entry</Button>
            </div>
        </Form>
    </div>
</template>
