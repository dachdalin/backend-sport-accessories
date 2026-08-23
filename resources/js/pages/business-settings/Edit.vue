<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import BusinessSettingController from '@/actions/App/Http/Controllers/Backend/BusinessSettingController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { edit } from '@/routes/business-settings';

type BusinessSettings = {
    site_name: string;
    logo: string;
    contact_email: string;
    contact_phone: string;
    address: string;
    currency_symbol: string;
    minimum_order_amount: string;
    free_delivery_over_amount: string;
    tax_included_in_price: string;
    maintenance_mode: string;
    copyright_text: string;
    meta_title: string;
    meta_description: string;
};

const props = defineProps<{
    settings: BusinessSettings;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Business settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Business settings" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Business settings"
            description="Store-wide settings used across the storefront"
        />

        <Form
            v-bind="BusinessSettingController.update.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="site_name">Site name</Label>
                <Input
                    id="site_name"
                    name="site_name"
                    required
                    autofocus
                    :default-value="props.settings.site_name"
                />
                <InputError :message="errors.site_name" />
            </div>

            <div class="grid gap-2">
                <div
                    class="flex size-16 items-center justify-center rounded-md border bg-white"
                >
                    <img
                        v-if="props.settings.logo !== 'def.png'"
                        :src="`/storage/${props.settings.logo}`"
                        alt="Store logo"
                        class="size-full rounded-md object-contain p-1"
                    />
                    <span v-else class="text-xs text-muted-foreground"
                        >No logo</span
                    >
                </div>
                <Label for="logo">{{
                    props.settings.logo !== 'def.png'
                        ? 'Replace logo'
                        : 'Upload logo'
                }}</Label>
                <Input id="logo" name="logo" type="file" accept="image/*" />
                <InputError :message="errors.logo" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="contact_email">Contact email</Label>
                    <Input
                        id="contact_email"
                        name="contact_email"
                        type="email"
                        :default-value="props.settings.contact_email"
                        placeholder="support@example.com"
                    />
                    <InputError :message="errors.contact_email" />
                </div>

                <div class="grid gap-2">
                    <Label for="contact_phone">Contact phone</Label>
                    <Input
                        id="contact_phone"
                        name="contact_phone"
                        :default-value="props.settings.contact_phone"
                        placeholder="+1 555 000 0000"
                    />
                    <InputError :message="errors.contact_phone" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    :default-value="props.settings.address"
                    placeholder="123 Main Street, London"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="grid gap-2">
                    <Label for="currency_symbol">Currency symbol</Label>
                    <Input
                        id="currency_symbol"
                        name="currency_symbol"
                        required
                        :default-value="props.settings.currency_symbol"
                        placeholder="$"
                    />
                    <InputError :message="errors.currency_symbol" />
                </div>

                <div class="grid gap-2">
                    <Label for="minimum_order_amount">Min. order amount</Label>
                    <Input
                        id="minimum_order_amount"
                        name="minimum_order_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :default-value="props.settings.minimum_order_amount"
                    />
                    <InputError :message="errors.minimum_order_amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="free_delivery_over_amount"
                        >Free delivery over</Label
                    >
                    <Input
                        id="free_delivery_over_amount"
                        name="free_delivery_over_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :default-value="props.settings.free_delivery_over_amount"
                    />
                    <InputError :message="errors.free_delivery_over_amount" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="copyright_text">Copyright text</Label>
                <Input
                    id="copyright_text"
                    name="copyright_text"
                    :default-value="props.settings.copyright_text"
                    placeholder="© 2026 Sport Accessories Store"
                />
                <InputError :message="errors.copyright_text" />
            </div>

            <div class="grid gap-2">
                <Label for="meta_title">Default meta title</Label>
                <Input
                    id="meta_title"
                    name="meta_title"
                    :default-value="props.settings.meta_title"
                    placeholder="SEO title"
                />
                <InputError :message="errors.meta_title" />
            </div>

            <div class="grid gap-2">
                <Label for="meta_description">Default meta description</Label>
                <Textarea
                    id="meta_description"
                    name="meta_description"
                    :default-value="props.settings.meta_description"
                    placeholder="SEO description"
                    rows="2"
                />
                <InputError :message="errors.meta_description" />
            </div>

            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-2">
                    <Checkbox
                        id="tax_included_in_price"
                        name="tax_included_in_price"
                        :default-value="props.settings.tax_included_in_price === '1'"
                    />
                    <Label for="tax_included_in_price"
                        >Tax included in displayed price</Label
                    >
                    <InputError :message="errors.tax_included_in_price" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox
                        id="maintenance_mode"
                        name="maintenance_mode"
                        :default-value="props.settings.maintenance_mode === '1'"
                    />
                    <Label for="maintenance_mode">Maintenance mode</Label>
                    <InputError :message="errors.maintenance_mode" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save settings</Button>
            </div>
        </Form>
    </div>
</template>
