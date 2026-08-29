<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    CircleDollarSign,
    Landmark,
    MapPin,
    Rocket,
    SearchCheck,
    Store,
    TriangleAlert,
} from '@lucide/vue';
import { computed } from 'vue';
import BusinessSettingController from '@/actions/App/Http/Controllers/Backend/BusinessSettingController';
import Heading from '@/components/Heading.vue';
import ImageDropzone from '@/components/ImageDropzone.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
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

const logoPreviews = computed(() =>
    props.settings.logo !== 'def.png' ? [`/storage/${props.settings.logo}`] : [],
);
</script>

<template>
    <Head title="Business settings" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Business settings"
            description="Store-wide settings used across the storefront"
        />

        <Form
            v-bind="BusinessSettingController.update.form()"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Store
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Store identity</CardTitle>
                        </div>
                        <CardDescription>
                            The name and logo shown across the storefront and
                            admin.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="site_name">Site name</Label>
                            <Input
                                id="site_name"
                                name="site_name"
                                required
                                autofocus
                                :default-value="settings.site_name"
                            />
                            <InputError :message="errors.site_name" />
                        </div>

                        <ImageDropzone
                            name="logo"
                            :label="
                                logoPreviews.length ? 'Replace logo' : 'Upload logo'
                            "
                            hint="A wide logo with a transparent background works best."
                            :error="errors.logo"
                            :processing="processing"
                            :initial-previews="logoPreviews"
                        />

                        <div class="grid gap-2">
                            <Label for="copyright_text">Copyright text</Label>
                            <Input
                                id="copyright_text"
                                name="copyright_text"
                                :default-value="settings.copyright_text"
                                placeholder="© 2026 Sport Accessories Store"
                            />
                            <InputError :message="errors.copyright_text" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <MapPin
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Contact &amp; location</CardTitle>
                        </div>
                        <CardDescription>
                            How customers reach you, shown in the storefront
                            footer.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="contact_email">Contact email</Label>
                                <Input
                                    id="contact_email"
                                    name="contact_email"
                                    type="email"
                                    :default-value="settings.contact_email"
                                    placeholder="support@example.com"
                                />
                                <InputError :message="errors.contact_email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="contact_phone">Contact phone</Label>
                                <Input
                                    id="contact_phone"
                                    name="contact_phone"
                                    :default-value="settings.contact_phone"
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
                                :default-value="settings.address"
                                placeholder="123 Main Street, London"
                            />
                            <InputError :message="errors.address" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <CircleDollarSign
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Commerce</CardTitle>
                        </div>
                        <CardDescription>
                            Currency, order minimums, and how tax is displayed.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="currency_symbol"
                                    >Currency symbol</Label
                                >
                                <Input
                                    id="currency_symbol"
                                    name="currency_symbol"
                                    required
                                    :default-value="settings.currency_symbol"
                                    placeholder="$"
                                />
                                <InputError
                                    :message="errors.currency_symbol"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="minimum_order_amount"
                                    >Min. order amount</Label
                                >
                                <Input
                                    id="minimum_order_amount"
                                    name="minimum_order_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :default-value="
                                        settings.minimum_order_amount
                                    "
                                />
                                <InputError
                                    :message="errors.minimum_order_amount"
                                />
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
                                    :default-value="
                                        settings.free_delivery_over_amount
                                    "
                                />
                                <InputError
                                    :message="errors.free_delivery_over_amount"
                                />
                            </div>
                        </div>

                        <label
                            for="tax_included_in_price"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="tax_included_in_price"
                                name="tax_included_in_price"
                                :default-value="
                                    settings.tax_included_in_price === '1'
                                "
                            />
                            <span class="text-sm font-medium"
                                >Tax included in displayed price</span
                            >
                        </label>
                        <InputError :message="errors.tax_included_in_price" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <SearchCheck
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Search visibility</CardTitle>
                        </div>
                        <CardDescription>
                            Default title and description search engines show
                            for storefront pages.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="meta_title">Default meta title</Label>
                            <Input
                                id="meta_title"
                                name="meta_title"
                                :default-value="settings.meta_title"
                                placeholder="SEO title"
                            />
                            <InputError :message="errors.meta_title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="meta_description"
                                >Default meta description</Label
                            >
                            <Textarea
                                id="meta_description"
                                name="meta_description"
                                :default-value="settings.meta_description"
                                placeholder="SEO description"
                                rows="2"
                            />
                            <InputError :message="errors.meta_description" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Landmark
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>System</CardTitle>
                        </div>
                        <CardDescription>
                            Site-wide switches that affect every visitor.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-2">
                        <label
                            for="maintenance_mode"
                            class="flex cursor-pointer items-start gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-destructive has-[[data-state=checked]]:bg-destructive/5"
                        >
                            <Checkbox
                                id="maintenance_mode"
                                name="maintenance_mode"
                                class="mt-0.5"
                                :default-value="
                                    settings.maintenance_mode === '1'
                                "
                            />
                            <span class="flex flex-col gap-0.5">
                                <span class="flex items-center gap-1.5 text-sm font-medium">
                                    <TriangleAlert
                                        class="size-3.5 text-destructive"
                                        aria-hidden="true"
                                    />
                                    Maintenance mode
                                </span>
                                <span class="text-xs text-muted-foreground">
                                    Closes the storefront to customers until
                                    turned off.
                                </span>
                            </span>
                        </label>
                        <InputError :message="errors.maintenance_mode" />
                    </CardContent>
                </Card>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Rocket
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Publish</CardTitle>
                        </div>
                        <CardDescription>
                            Save your changes to update the storefront.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save settings
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>
