<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    Building2,
    House,
    MapPin,
    Send,
    Star,
    Tag,
    Truck,
    UserRound,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import ShippingAddressController from '@/actions/App/Http/Controllers/Backend/ShippingAddressController';
import Heading from '@/components/Heading.vue';
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
import { edit, index } from '@/routes/shipping-addresses';

interface SelectOption {
    value: number;
    label: string;
}

interface ShippingAddress {
    id: number;
    customer_id: number;
    contact_person_name: string;
    phone: string | null;
    address_type: string;
    address: string;
    city: string;
    state: string | null;
    zip: string | null;
    country: string;
    is_default: boolean;
}

const props = defineProps<{
    shippingAddress: ShippingAddress;
    customers: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { shippingAddress: ShippingAddress }) => ({
        breadcrumbs: [
            {
                title: 'Shipping addresses',
                href: index(),
            },
            {
                title: 'Edit shipping address',
                href: edit(pageProps.shippingAddress),
            },
        ],
    }),
});

const ADDRESS_TYPES = [
    { value: 'home', label: 'Home', icon: House },
    { value: 'office', label: 'Office', icon: Building2 },
    { value: 'other', label: 'Other', icon: MapPin },
] as const;

// Mirrors the form's native inputs so the label preview can react live —
// the `name` attributes below still own the actual submission.
const contactPersonName = ref(props.shippingAddress.contact_person_name);
const phone = ref(props.shippingAddress.phone ?? '');
const addressType = ref<(typeof ADDRESS_TYPES)[number]['value']>(
    props.shippingAddress.address_type as (typeof ADDRESS_TYPES)[number]['value'],
);
const address = ref(props.shippingAddress.address);
const city = ref(props.shippingAddress.city);
const state = ref(props.shippingAddress.state ?? '');
const zip = ref(props.shippingAddress.zip ?? '');
const country = ref(props.shippingAddress.country);
const isDefault = ref(props.shippingAddress.is_default);

const addressTypeMeta = computed(
    () => ADDRESS_TYPES.find((option) => option.value === addressType.value)!,
);

const cityLine = computed(() =>
    [city.value, state.value].filter(Boolean).join(', '),
);
</script>

<template>
    <Head title="Edit shipping address" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit shipping address"
            :description="`Update the address for ${props.shippingAddress.contact_person_name}`"
        />

        <Form
            v-bind="
                ShippingAddressController.update.form(props.shippingAddress)
            "
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <UserRound
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Recipient</CardTitle>
                        </div>
                        <CardDescription>
                            Who receives this shipment.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="customer_id">Customer</Label>
                            <Select
                                name="customer_id"
                                :default-value="
                                    String(props.shippingAddress.customer_id)
                                "
                            >
                                <SelectTrigger id="customer_id" class="w-full">
                                    <SelectValue placeholder="Select customer" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="option in customers"
                                        :key="option.value"
                                        :value="String(option.value)"
                                    >
                                        {{ option.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.customer_id" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="contact_person_name"
                                    >Contact person</Label
                                >
                                <Input
                                    id="contact_person_name"
                                    name="contact_person_name"
                                    v-model="contactPersonName"
                                    required
                                    autofocus
                                />
                                <InputError
                                    :message="errors.contact_person_name"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="phone">Phone</Label>
                                <Input
                                    id="phone"
                                    name="phone"
                                    v-model="phone"
                                    placeholder="+1 555 0100"
                                />
                                <InputError :message="errors.phone" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Truck
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Delivery address</CardTitle>
                        </div>
                        <CardDescription>
                            Where the carrier drops this off.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label>Address type</Label>
                            <div class="grid grid-cols-3 gap-2">
                                <label
                                    v-for="option in ADDRESS_TYPES"
                                    :key="option.value"
                                    class="group relative flex cursor-pointer flex-col items-center gap-1.5 rounded-lg border border-input px-2 py-3 text-center text-muted-foreground transition-colors hover:bg-accent/40 has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:font-semibold has-[:checked]:text-primary"
                                >
                                    <input
                                        type="radio"
                                        name="address_type"
                                        class="absolute inset-0 z-10 h-full w-full cursor-pointer appearance-none rounded-lg opacity-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        :value="option.value"
                                        :checked="
                                            option.value ===
                                            props.shippingAddress.address_type
                                        "
                                        @change="addressType = option.value"
                                    />
                                    <component
                                        :is="option.icon"
                                        class="size-4"
                                        aria-hidden="true"
                                    />
                                    <span class="text-xs font-medium sm:text-sm">{{
                                        option.label
                                    }}</span>
                                </label>
                            </div>
                            <InputError :message="errors.address_type" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="address">Address</Label>
                            <Input
                                id="address"
                                name="address"
                                v-model="address"
                                required
                            />
                            <InputError :message="errors.address" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="city">City</Label>
                                <Input
                                    id="city"
                                    name="city"
                                    v-model="city"
                                    required
                                />
                                <InputError :message="errors.city" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="state">State</Label>
                                <Input id="state" name="state" v-model="state" />
                                <InputError :message="errors.state" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="zip">ZIP code</Label>
                                <Input id="zip" name="zip" v-model="zip" />
                                <InputError :message="errors.zip" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="country">Country</Label>
                                <Input
                                    id="country"
                                    name="country"
                                    v-model="country"
                                    required
                                />
                                <InputError :message="errors.country" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Tag
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Shipping label</CardTitle>
                        </div>
                        <CardDescription>
                            How this address prints on the parcel.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div
                            class="rounded-lg border border-dashed p-4 transition-colors"
                            :class="
                                isDefault
                                    ? 'border-primary/60 bg-primary/5'
                                    : 'border-border bg-muted/30'
                            "
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span
                                    class="font-mono text-[10px] font-semibold tracking-[0.2em] text-muted-foreground uppercase"
                                >
                                    Ship to
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    <Star
                                        v-if="isDefault"
                                        class="size-3 fill-primary text-primary"
                                        aria-hidden="true"
                                    />
                                    <component
                                        :is="addressTypeMeta.icon"
                                        class="size-3"
                                        aria-hidden="true"
                                    />
                                    {{ addressTypeMeta.label }}
                                </span>
                            </div>

                            <p class="mt-3 truncate text-sm font-semibold">
                                {{ contactPersonName || 'Recipient name' }}
                            </p>
                            <p
                                v-if="phone"
                                class="truncate text-xs text-muted-foreground"
                            >
                                {{ phone }}
                            </p>

                            <p
                                class="mt-2 text-xs leading-relaxed text-muted-foreground"
                            >
                                {{ address || 'Street address' }}<br />
                                {{ cityLine || 'City, state' }}
                                {{ zip }}<br />
                                {{ country || 'Country' }}
                            </p>

                            <div class="barcode mt-4" aria-hidden="true" />
                        </div>

                        <label
                            for="is_default"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="is_default"
                                name="is_default"
                                value="1"
                                v-model="isDefault"
                            />
                            <span class="text-sm font-medium"
                                >Set as default address</span
                            >
                        </label>
                        <InputError :message="errors.is_default" />
                    </CardContent>
                </Card>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Send
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Save</CardTitle>
                        </div>
                        <CardDescription>
                            Save changes to this shipping address.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save shipping address
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>

<style scoped>
.barcode {
    height: 18px;
    opacity: 0.4;
    background-image: repeating-linear-gradient(
        to right,
        currentColor 0 2px,
        transparent 2px 5px,
        currentColor 5px 6px,
        transparent 6px 10px,
        currentColor 10px 13px,
        transparent 13px 15px,
        currentColor 15px 17px,
        transparent 17px 22px
    );
    color: var(--muted-foreground);
}
</style>
