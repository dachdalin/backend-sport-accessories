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
import { create, index } from '@/routes/most-demandeds';

interface SelectOption {
    value: number | string;
    label: string;
}

defineProps<{
    products: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Most demanded',
                href: index(),
            },
            {
                title: 'Add entry',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add entry" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add entry"
            description="Feature a product on the most demanded list"
        />

        <Form
            v-bind="MostDemandedController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="product_id">Product</Label>
                <Select name="product_id">
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
                <Label for="banner">Banner image</Label>
                <Input id="banner" name="banner" type="file" accept="image/*" />
                <InputError :message="errors.banner" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create entry</Button>
            </div>
        </Form>
    </div>
</template>
