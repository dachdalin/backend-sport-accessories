<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    Clock,
    Eye,
    Image as ImageIcon,
    Info,
    Layers,
    Palette,
    Plus,
    Rocket,
    Zap,
} from '@lucide/vue';
import { ref } from 'vue';
import FlashDealController from '@/actions/App/Http/Controllers/Backend/FlashDealController';
import ColorSwatchInput from '@/components/ColorSwatchInput.vue';
import FlashDealPreview from '@/components/FlashDealPreview.vue';
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
import { edit, index } from '@/routes/flash-deals';

interface SelectOption {
    value: number | string;
    label: string;
}

type FlashDealItem = {
    id: number;
    product_id: number | null;
    discount: string;
    discount_type: string;
};

type FlashDeal = {
    id: number;
    title: string;
    start_date: string;
    end_date: string;
    status: boolean;
    featured: boolean;
    background_color: string | null;
    text_color: string | null;
    banner: string;
    items: FlashDealItem[];
};

const props = defineProps<{
    flashDeal: FlashDeal;
    products: SelectOption[];
    discountTypes: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { flashDeal: FlashDeal }) => ({
        breadcrumbs: [
            {
                title: 'Flash deals',
                href: index(),
            },
            {
                title: `Edit ${pageProps.flashDeal.title}`,
                href: edit(pageProps.flashDeal),
            },
        ],
    }),
});

const title = ref(props.flashDeal.title);
const startDate = ref(props.flashDeal.start_date);
const endDate = ref(props.flashDeal.end_date);
const backgroundColor = ref(props.flashDeal.background_color ?? '');
const textColor = ref(props.flashDeal.text_color ?? '');

let nextKey = 0;

function itemFromFlashDeal(item: FlashDealItem) {
    return {
        key: nextKey++,
        product_id: item.product_id ?? '',
        discount: item.discount,
        discount_type: item.discount_type,
    };
}

const items = ref(
    props.flashDeal.items.length > 0
        ? props.flashDeal.items.map(itemFromFlashDeal)
        : [
              {
                  key: nextKey++,
                  product_id: '',
                  discount: '',
                  discount_type: 'percent',
              },
          ],
);

function addItem() {
    items.value.push({
        key: nextKey++,
        product_id: '',
        discount: '',
        discount_type: 'percent',
    });
}

function removeItem(index: number) {
    items.value.splice(index, 1);
}
</script>

<template>
    <Head :title="`Edit ${flashDeal.title}`" />

    <div class="flex flex-col gap-6">
        <Heading
            :title="`Edit ${flashDeal.title}`"
            description="Update this flash deal's schedule and discounted products"
        />

        <Form
            v-bind="FlashDealController.update.form(flashDeal)"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Info
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Details</CardTitle>
                        </div>
                        <CardDescription>
                            The name shoppers see on the deal banner.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="title">Title</Label>
                            <Input
                                id="title"
                                v-model="title"
                                name="title"
                                required
                                autofocus
                            />
                            <InputError :message="errors.title" />
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
                            <CardTitle>Schedule</CardTitle>
                        </div>
                        <CardDescription>
                            When this deal starts and stops discounting
                            products.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="start_date">Start date</Label>
                                <Input
                                    id="start_date"
                                    v-model="startDate"
                                    name="start_date"
                                    type="date"
                                    required
                                />
                                <InputError :message="errors.start_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="end_date">End date</Label>
                                <Input
                                    id="end_date"
                                    v-model="endDate"
                                    name="end_date"
                                    type="date"
                                    required
                                />
                                <InputError :message="errors.end_date" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <ImageIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Banner</CardTitle>
                        </div>
                        <CardDescription>
                            Shown at the top of the deal's storefront page.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ImageDropzone
                            name="banner"
                            label="Replace banner"
                            hint="PNG, JPG or WEBP, up to 2MB. Leave empty to keep the current banner."
                            :error="errors.banner"
                            :processing="processing"
                            :initial-previews="[`/storage/${flashDeal.banner}`]"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Palette
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Appearance</CardTitle>
                        </div>
                        <CardDescription>
                            Colors for the deal badge. Leave blank to use the
                            default styling.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <ColorSwatchInput
                            id="background_color"
                            v-model="backgroundColor"
                            name="background_color"
                            label="Background color"
                            placeholder="#ff0000"
                            :error="errors.background_color"
                        />

                        <ColorSwatchInput
                            id="text_color"
                            v-model="textColor"
                            name="text_color"
                            label="Text color"
                            placeholder="#ffffff"
                            :error="errors.text_color"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Layers
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Deal products</CardTitle>
                        </div>
                        <CardDescription>
                            Add each product this discount applies to, and set
                            its own discount.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-3">
                        <div class="flex items-center justify-end">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="addItem"
                            >
                                <Plus class="size-4" aria-hidden="true" />
                                Add product
                            </Button>
                        </div>
                        <InputError :message="errors.items" />

                        <div
                            v-for="(item, itemIndex) in items"
                            :key="item.key"
                            class="grid grid-cols-1 gap-3 rounded-lg border border-input p-3 sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-start sm:gap-2"
                        >
                            <div class="grid gap-2">
                                <Label :for="`item-product-${item.key}`"
                                    >Product</Label
                                >
                                <Select
                                    v-model="item.product_id"
                                    :name="`items[${itemIndex}][product_id]`"
                                >
                                    <SelectTrigger
                                        :id="`item-product-${item.key}`"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select product"
                                        />
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
                                <InputError
                                    :message="
                                        errors[`items.${itemIndex}.product_id`]
                                    "
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label :for="`item-discount-${item.key}`"
                                    >Discount</Label
                                >
                                <Input
                                    :id="`item-discount-${item.key}`"
                                    v-model="item.discount"
                                    :name="`items[${itemIndex}][discount]`"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                />
                                <InputError
                                    :message="
                                        errors[`items.${itemIndex}.discount`]
                                    "
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label :for="`item-discount-type-${item.key}`"
                                    >Type</Label
                                >
                                <Select
                                    v-model="item.discount_type"
                                    :name="`items[${itemIndex}][discount_type]`"
                                >
                                    <SelectTrigger
                                        :id="`item-discount-type-${item.key}`"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select type"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in discountTypes"
                                            :key="option.value"
                                            :value="String(option.value)"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="
                                        errors[
                                            `items.${itemIndex}.discount_type`
                                        ]
                                    "
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label class="invisible hidden sm:block"
                                    >Remove</Label
                                >
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="sm"
                                    class="w-full sm:w-auto"
                                    :disabled="items.length === 1"
                                    @click="removeItem(itemIndex)"
                                >
                                    Remove
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Zap
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Preview</CardTitle>
                        </div>
                        <CardDescription>
                            What shoppers will see when this deal goes live.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <FlashDealPreview
                            :title="title"
                            :start-date="startDate"
                            :end-date="endDate"
                            :background-color="backgroundColor"
                            :text-color="textColor"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Eye
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Visibility</CardTitle>
                        </div>
                        <CardDescription>
                            How this deal behaves in the storefront.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-2">
                        <label
                            for="featured"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="featured"
                                name="featured"
                                value="1"
                                :default-value="flashDeal.featured"
                            />
                            <span class="text-sm font-medium">Featured</span>
                        </label>

                        <label
                            for="status"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                value="1"
                                :default-value="flashDeal.status"
                            />
                            <span class="text-sm font-medium">Active</span>
                        </label>

                        <InputError :message="errors.featured" />
                        <InputError :message="errors.status" />
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
                            Save your changes to this flash deal.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save flash deal
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
