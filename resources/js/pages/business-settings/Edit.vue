<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    Clock,
    CircleDollarSign,
    Landmark,
    LayoutGrid,
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { edit } from '@/routes/business-settings';

type BusinessSettings = {
    site_name: string;
    logo: string;
    contact_email: string;
    contact_phone: string;
    address: string;
    detail_location: string;
    currency_symbol: string;
    minimum_order_amount: string;
    free_delivery_over_amount: string;
    tax_included_in_price: string;
    guest_checkout: string;
    invoice_prefix: string;
    maintenance_mode: string;
    copyright_text: string;
    meta_title: string;
    meta_description: string;
    working_hours_open: string;
    working_hours_close: string;
    working_days: string;
    time_zone: string;
    pagination_limit: string;
    max_login_attempts: string;
};

const props = defineProps<{
    settings: BusinessSettings;
    timezones: string[];
}>();

const workingDayOptions = [
    { value: 'mon', label: 'Monday' },
    { value: 'tue', label: 'Tuesday' },
    { value: 'wed', label: 'Wednesday' },
    { value: 'thu', label: 'Thursday' },
    { value: 'fri', label: 'Friday' },
    { value: 'sat', label: 'Saturday' },
    { value: 'sun', label: 'Sunday' },
];

const selectedWorkingDays = computed(() =>
    props.settings.working_days
        ? props.settings.working_days.split(',').filter(Boolean)
        : [],
);

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

                        <div class="grid gap-2">
                            <Label for="detail_location"
                                >Location details</Label
                            >
                            <Textarea
                                id="detail_location"
                                name="detail_location"
                                :default-value="settings.detail_location"
                                placeholder="Landmark, floor, or directions to help customers find you"
                                rows="2"
                            />
                            <InputError :message="errors.detail_location" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Clock
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Hours &amp; availability</CardTitle>
                        </div>
                        <CardDescription>
                            When the store operates, and which time zone that
                            schedule is in.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="working_hours_open"
                                    >Opens at</Label
                                >
                                <Input
                                    id="working_hours_open"
                                    name="working_hours_open"
                                    type="time"
                                    :default-value="
                                        settings.working_hours_open
                                    "
                                />
                                <InputError
                                    :message="errors.working_hours_open"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="working_hours_close"
                                    >Closes at</Label
                                >
                                <Input
                                    id="working_hours_close"
                                    name="working_hours_close"
                                    type="time"
                                    :default-value="
                                        settings.working_hours_close
                                    "
                                />
                                <InputError
                                    :message="errors.working_hours_close"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label>Working days</Label>
                            <div
                                class="grid grid-cols-2 gap-2 sm:grid-cols-4"
                            >
                                <label
                                    v-for="day in workingDayOptions"
                                    :key="day.value"
                                    :for="`working-day-${day.value}`"
                                    class="flex cursor-pointer items-center gap-2 rounded-lg border border-input px-3 py-2 text-sm transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                                >
                                    <Checkbox
                                        :id="`working-day-${day.value}`"
                                        name="working_days[]"
                                        :value="day.value"
                                        :default-value="
                                            selectedWorkingDays.includes(
                                                day.value,
                                            )
                                        "
                                    />
                                    <span class="font-medium">{{
                                        day.label
                                    }}</span>
                                </label>
                            </div>
                            <InputError :message="errors.working_days" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="time_zone">Time zone</Label>
                            <Select
                                name="time_zone"
                                :default-value="settings.time_zone"
                            >
                                <SelectTrigger id="time_zone" class="w-full">
                                    <SelectValue
                                        placeholder="Select time zone"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="tz in timezones"
                                        :key="tz"
                                        :value="tz"
                                    >
                                        {{ tz }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.time_zone" />
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

                        <div class="grid max-w-40 gap-2">
                            <Label for="invoice_prefix"
                                >Invoice prefix</Label
                            >
                            <Input
                                id="invoice_prefix"
                                name="invoice_prefix"
                                :default-value="settings.invoice_prefix"
                                placeholder="INV-"
                            />
                            <InputError :message="errors.invoice_prefix" />
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

                        <label
                            for="guest_checkout"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="guest_checkout"
                                name="guest_checkout"
                                :default-value="
                                    settings.guest_checkout === '1'
                                "
                            />
                            <span class="text-sm font-medium"
                                >Allow guest checkout</span
                            >
                        </label>
                        <InputError :message="errors.guest_checkout" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <LayoutGrid
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Catalog display</CardTitle>
                        </div>
                        <CardDescription>
                            How many items show per page across storefront
                            and admin listings.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid max-w-40 gap-2">
                            <Label for="pagination_limit"
                                >Items per page</Label
                            >
                            <Input
                                id="pagination_limit"
                                name="pagination_limit"
                                type="number"
                                min="5"
                                max="100"
                                required
                                :default-value="settings.pagination_limit"
                            />
                            <InputError :message="errors.pagination_limit" />
                        </div>
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
                            Site-wide switches and security limits.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="max_login_attempts"
                                >Max login attempts</Label
                            >
                            <Input
                                id="max_login_attempts"
                                name="max_login_attempts"
                                type="number"
                                min="1"
                                max="20"
                                required
                                :default-value="settings.max_login_attempts"
                            />
                            <InputError
                                :message="errors.max_login_attempts"
                            />
                        </div>

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
