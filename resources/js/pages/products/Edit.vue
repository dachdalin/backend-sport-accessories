<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { onBeforeUnmount, ref, computed } from 'vue';
import ProductController from '@/actions/App/Http/Controllers/Backend/ProductController';
import ProductImageController from '@/actions/App/Http/Controllers/Backend/ProductImageController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
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
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/products';

interface SelectOption {
    value: number | string;
    label: string;
}

type Product = {
    id: number;
    name: string;
    code: string | null;
    description: string | null;
    thumbnail: string;
    unit_price: string;
    purchase_price: string | null;
    current_stock: number;
    minimum_order_qty: number;
    category_id: number | null;
    brand_id: number | null;
    tax: string;
    tax_type: string | null;
    discount: string;
    discount_type: string | null;
    free_shipping: boolean;
    refundable: boolean;
    featured: boolean;
    meta_title: string | null;
    meta_description: string | null;
    status: boolean;
};

type ProductImage = {
    id: number;
    image: string;
};

const props = defineProps<{
    product: Product;
    images: ProductImage[];
    categories: SelectOption[];
    brands: SelectOption[];
    taxTypes: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { product: Product }) => ({
        breadcrumbs: [
            {
                title: 'Products',
                href: index(),
            },
            {
                title: 'Edit product',
                href: edit(pageProps.product),
            },
        ],
    }),
});

const thumbnailPreview = ref<string>(`/storage/${props.product.thumbnail}`);
const uploadedThumbnailPreview = ref<string | null>(null);

function onThumbnailChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (uploadedThumbnailPreview.value) {
        URL.revokeObjectURL(uploadedThumbnailPreview.value);
    }

    uploadedThumbnailPreview.value = file ? URL.createObjectURL(file) : null;
}

onBeforeUnmount(() => {
    if (uploadedThumbnailPreview.value) {
        URL.revokeObjectURL(uploadedThumbnailPreview.value);
    }
});

const unitPrice = ref(props.product.unit_price);
const discount = ref(props.product.discount);
const discountType = ref(props.product.discount_type ?? 'percent');

const unitPriceNumber = computed(() => parseFloat(unitPrice.value) || 0);
const discountNumber = computed(() => parseFloat(discount.value) || 0);
const hasDiscount = computed(
    () => unitPriceNumber.value > 0 && discountNumber.value > 0,
);
const finalPrice = computed(() => {
    if (discountNumber.value <= 0) {
        return unitPriceNumber.value;
    }

    const reduced =
        discountType.value === 'percent'
            ? unitPriceNumber.value -
              (unitPriceNumber.value * discountNumber.value) / 100
            : unitPriceNumber.value - discountNumber.value;

    return Math.max(0, reduced);
});
</script>

<template>
    <Head title="Edit product" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit product"
            :description="`Update the details for ${product.name}`"
        />

        <Form
            v-bind="ProductController.update.form(product)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <Heading
                variant="small"
                title="Basic info"
                description="What the product is called and how it's described."
            />

            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    autofocus
                    :default-value="product.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="code">SKU / Code</Label>
                <Input
                    id="code"
                    name="code"
                    :default-value="product.code ?? ''"
                    placeholder="SKU-0001"
                />
                <InputError :message="errors.code" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    name="description"
                    :default-value="product.description ?? ''"
                    placeholder="Product description"
                    rows="4"
                />
                <InputError :message="errors.description" />
            </div>

            <Separator />

            <Heading
                variant="small"
                title="Media"
                description="Replacing the thumbnail swaps what's shown in listings."
            />

            <div class="grid gap-2">
                <Label for="thumbnail">Thumbnail</Label>
                <div class="flex items-center gap-3">
                    <img
                        :src="uploadedThumbnailPreview ?? thumbnailPreview"
                        :alt="product.name"
                        class="size-16 shrink-0 rounded-md border border-input object-cover"
                    />
                    <Input
                        id="thumbnail"
                        name="thumbnail"
                        type="file"
                        accept="image/*"
                        @change="onThumbnailChange"
                    />
                </div>
                <InputError :message="errors.thumbnail" />
            </div>

            <Separator />

            <Heading
                variant="small"
                title="Pricing & inventory"
                description="What it costs, what it's sold for, and how much is in stock."
            />

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="unit_price">Unit price</Label>
                    <Input
                        id="unit_price"
                        name="unit_price"
                        type="number"
                        step="0.01"
                        min="0"
                        required
                        v-model="unitPrice"
                    />
                    <InputError :message="errors.unit_price" />
                </div>

                <div class="grid gap-2">
                    <Label for="purchase_price">Purchase price</Label>
                    <Input
                        id="purchase_price"
                        name="purchase_price"
                        type="number"
                        step="0.01"
                        min="0"
                        :default-value="product.purchase_price ?? ''"
                    />
                    <InputError :message="errors.purchase_price" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="tax">Tax</Label>
                    <Input
                        id="tax"
                        name="tax"
                        type="number"
                        step="0.01"
                        min="0"
                        :default-value="product.tax"
                    />
                    <InputError :message="errors.tax" />
                </div>

                <div class="grid gap-2">
                    <Label for="tax_type">Tax type</Label>
                    <Select
                        name="tax_type"
                        :default-value="product.tax_type ?? undefined"
                    >
                        <SelectTrigger id="tax_type" class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in taxTypes"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.tax_type" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="discount">Discount</Label>
                    <Input
                        id="discount"
                        name="discount"
                        type="number"
                        step="0.01"
                        min="0"
                        v-model="discount"
                    />
                    <InputError :message="errors.discount" />
                </div>

                <div class="grid gap-2">
                    <Label for="discount_type">Discount type</Label>
                    <Select name="discount_type" v-model="discountType">
                        <SelectTrigger id="discount_type" class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in taxTypes"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.discount_type" />
                </div>
            </div>

            <div
                v-if="unitPriceNumber > 0"
                class="flex flex-wrap items-center gap-3 rounded-lg border border-dashed border-input p-3"
            >
                <span class="text-lg font-semibold"
                    >${{ finalPrice.toFixed(2) }}</span
                >
                <span
                    v-if="hasDiscount"
                    class="text-sm text-muted-foreground line-through"
                    >${{ unitPriceNumber.toFixed(2) }}</span
                >
                <Badge v-if="hasDiscount" variant="secondary">
                    {{
                        discountType === 'percent'
                            ? `${discountNumber}% off`
                            : `$${discountNumber.toFixed(2)} off`
                    }}
                </Badge>
                <span class="w-full text-xs text-muted-foreground">
                    Price shown to customers after your discount.
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="current_stock">Current stock</Label>
                    <Input
                        id="current_stock"
                        name="current_stock"
                        type="number"
                        min="0"
                        required
                        :default-value="String(product.current_stock)"
                    />
                    <InputError :message="errors.current_stock" />
                </div>

                <div class="grid gap-2">
                    <Label for="minimum_order_qty">Min. order qty</Label>
                    <Input
                        id="minimum_order_qty"
                        name="minimum_order_qty"
                        type="number"
                        min="1"
                        required
                        :default-value="String(product.minimum_order_qty)"
                    />
                    <InputError :message="errors.minimum_order_qty" />
                </div>
            </div>

            <Separator />

            <Heading
                variant="small"
                title="Organization"
                description="Where this product lives in your catalog."
            />

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="category_id">Category</Label>
                    <Select
                        name="category_id"
                        :default-value="
                            product.category_id
                                ? String(product.category_id)
                                : undefined
                        "
                    >
                        <SelectTrigger id="category_id" class="w-full">
                            <SelectValue placeholder="Select category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in categories"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="brand_id">Brand</Label>
                    <Select
                        name="brand_id"
                        :default-value="
                            product.brand_id
                                ? String(product.brand_id)
                                : undefined
                        "
                    >
                        <SelectTrigger id="brand_id" class="w-full">
                            <SelectValue placeholder="Select brand" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in brands"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.brand_id" />
                </div>
            </div>

            <Separator />

            <Heading
                variant="small"
                title="Visibility"
                description="How this product behaves in the storefront."
            />

            <div class="grid gap-2 sm:grid-cols-2">
                <label
                    for="status"
                    class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                >
                    <Checkbox
                        id="status"
                        name="status"
                        :default-value="product.status"
                    />
                    <span class="text-sm font-medium">Active</span>
                </label>

                <label
                    for="featured"
                    class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                >
                    <Checkbox
                        id="featured"
                        name="featured"
                        :default-value="product.featured"
                    />
                    <span class="text-sm font-medium">Featured</span>
                </label>

                <label
                    for="free_shipping"
                    class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                >
                    <Checkbox
                        id="free_shipping"
                        name="free_shipping"
                        :default-value="product.free_shipping"
                    />
                    <span class="text-sm font-medium">Free shipping</span>
                </label>

                <label
                    for="refundable"
                    class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                >
                    <Checkbox
                        id="refundable"
                        name="refundable"
                        :default-value="product.refundable"
                    />
                    <span class="text-sm font-medium">Refundable</span>
                </label>
            </div>
            <InputError :message="errors.free_shipping" />
            <InputError :message="errors.refundable" />
            <InputError :message="errors.featured" />
            <InputError :message="errors.status" />

            <Separator />

            <Heading
                variant="small"
                title="Search visibility"
                description="Optional. Controls how this product looks in search engine results."
            />

            <div class="grid gap-2">
                <Label for="meta_title">Meta title</Label>
                <Input
                    id="meta_title"
                    name="meta_title"
                    :default-value="product.meta_title ?? ''"
                    placeholder="SEO title"
                />
                <InputError :message="errors.meta_title" />
            </div>

            <div class="grid gap-2">
                <Label for="meta_description">Meta description</Label>
                <Textarea
                    id="meta_description"
                    name="meta_description"
                    :default-value="product.meta_description ?? ''"
                    placeholder="SEO description"
                    rows="2"
                />
                <InputError :message="errors.meta_description" />
            </div>

            <Separator />

            <div class="flex items-center gap-3">
                <Button :disabled="processing">
                    <Spinner v-if="processing" />
                    Save product
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>

        <div class="max-w-xl space-y-4">
            <Heading
                title="Gallery"
                description="Extra images shown on the product page"
            />

            <div v-if="images.length" class="grid grid-cols-4 gap-3">
                <div
                    v-for="galleryImage in images"
                    :key="galleryImage.id"
                    class="group relative"
                >
                    <img
                        :src="`/storage/${galleryImage.image}`"
                        alt="Product gallery image"
                        class="aspect-square w-full rounded object-cover"
                    />
                    <Form
                        v-bind="
                            ProductImageController.destroy.form(galleryImage)
                        "
                        v-slot="{ processing: destroying }"
                    >
                        <Button
                            type="submit"
                            variant="destructive"
                            size="sm"
                            :disabled="destroying"
                            class="absolute top-1 right-1 opacity-0 group-hover:opacity-100"
                        >
                            Remove
                        </Button>
                    </Form>
                </div>
            </div>

            <Form
                v-bind="ProductImageController.store.form(product)"
                v-slot="{ errors, processing: uploading }"
                class="grid gap-2"
            >
                <Label for="image">Add image</Label>
                <Input
                    id="image"
                    name="image"
                    type="file"
                    accept="image/*"
                    required
                />
                <InputError :message="errors.image" />
                <div>
                    <Button
                        :disabled="uploading"
                        type="submit"
                        variant="outline"
                        >Upload</Button
                    >
                </div>
            </Form>
        </div>
    </div>
</template>
