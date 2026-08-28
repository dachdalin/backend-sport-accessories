<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    DollarSign,
    FolderTree,
    Eye,
    Image as ImageIcon,
    Images,
    Info,
    Rocket,
    SearchCheck,
} from '@lucide/vue';
import { ref, computed } from 'vue';
import ProductController from '@/actions/App/Http/Controllers/Backend/ProductController';
import ProductImageController from '@/actions/App/Http/Controllers/Backend/ProductImageController';
import Heading from '@/components/Heading.vue';
import ImageDropzone from '@/components/ImageDropzone.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
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

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit product"
            :description="`Update the details for ${product.name}`"
        />

        <Form
            v-bind="ProductController.update.form(product)"
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
                            <CardTitle>Basic info</CardTitle>
                        </div>
                        <CardDescription>
                            What the product is called and how it's described.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
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
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <ImageIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Media</CardTitle>
                        </div>
                        <CardDescription>
                            Replacing the thumbnail swaps what's shown in
                            listings.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ImageDropzone
                            name="thumbnail"
                            label="Thumbnail"
                            hint="Square images work best in listings."
                            :error="errors.thumbnail"
                            :processing="processing"
                            :initial-previews="[
                                `/storage/${product.thumbnail}`,
                            ]"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <DollarSign
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Pricing &amp; inventory</CardTitle>
                        </div>
                        <CardDescription>
                            What it costs, what it's sold for, and how much is
                            in stock.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
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
                                <Label for="purchase_price"
                                    >Purchase price</Label
                                >
                                <Input
                                    id="purchase_price"
                                    name="purchase_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :default-value="
                                        product.purchase_price ?? ''
                                    "
                                />
                                <InputError :message="errors.purchase_price" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
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
                                    :default-value="
                                        product.tax_type ?? undefined
                                    "
                                >
                                    <SelectTrigger id="tax_type" class="w-full">
                                        <SelectValue
                                            placeholder="Select type"
                                        />
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

                        <div class="grid gap-4 sm:grid-cols-2">
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
                                <Select
                                    name="discount_type"
                                    v-model="discountType"
                                >
                                    <SelectTrigger
                                        id="discount_type"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select type"
                                        />
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

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="current_stock">Current stock</Label>
                                <Input
                                    id="current_stock"
                                    name="current_stock"
                                    type="number"
                                    min="0"
                                    required
                                    :default-value="
                                        String(product.current_stock)
                                    "
                                />
                                <InputError :message="errors.current_stock" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="minimum_order_qty"
                                    >Min. order qty</Label
                                >
                                <Input
                                    id="minimum_order_qty"
                                    name="minimum_order_qty"
                                    type="number"
                                    min="1"
                                    required
                                    :default-value="
                                        String(product.minimum_order_qty)
                                    "
                                />
                                <InputError
                                    :message="errors.minimum_order_qty"
                                />
                            </div>
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
                            Optional. Controls how this product looks in search
                            engine results.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
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
                            <Label for="meta_description"
                                >Meta description</Label
                            >
                            <Textarea
                                id="meta_description"
                                name="meta_description"
                                :default-value="product.meta_description ?? ''"
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
                            <FolderTree
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Organization</CardTitle>
                        </div>
                        <CardDescription>
                            Where this product lives in your catalog.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
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
                                    <SelectValue
                                        placeholder="Select category"
                                    />
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
                            How this product behaves in the storefront.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-2">
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
                            <span class="text-sm font-medium"
                                >Free shipping</span
                            >
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

                        <InputError :message="errors.free_shipping" />
                        <InputError :message="errors.refundable" />
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
                            Save your changes to update this product.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save product
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>

        <Card>
            <CardHeader>
                <div class="flex items-center gap-2.5">
                    <Images
                        class="size-4.5 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <CardTitle>Gallery</CardTitle>
                </div>
                <CardDescription>
                    Extra images shown on the product page.
                </CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
                <div
                    v-if="images.length"
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                >
                    <div
                        v-for="galleryImage in images"
                        :key="galleryImage.id"
                        class="group relative"
                    >
                        <img
                            :src="`/storage/${galleryImage.image}`"
                            alt="Product gallery image"
                            class="aspect-square w-full rounded-md border border-input object-cover"
                        />
                        <Form
                            v-bind="
                                ProductImageController.destroy.form(
                                    galleryImage,
                                )
                            "
                            v-slot="{ processing: destroying }"
                        >
                            <Button
                                type="submit"
                                variant="destructive"
                                size="sm"
                                :disabled="destroying"
                                class="absolute top-1 right-1 opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                Remove
                            </Button>
                        </Form>
                    </div>
                </div>
                <p v-else class="text-sm text-muted-foreground">
                    No gallery images yet.
                </p>

                <Form
                    v-bind="ProductImageController.store.form(product)"
                    v-slot="{ errors, processing: uploading }"
                    class="flex flex-col gap-3 border-t pt-4"
                >
                    <ImageDropzone
                        name="image"
                        label="Add image"
                        :error="errors.image"
                        :processing="uploading"
                    />
                    <div>
                        <Button
                            :disabled="uploading"
                            type="submit"
                            variant="outline"
                        >
                            <Spinner v-if="uploading" />
                            Upload
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
