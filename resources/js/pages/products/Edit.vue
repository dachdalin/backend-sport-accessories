<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ProductController from '@/actions/App/Http/Controllers/Backend/ProductController';
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

defineProps<{
    product: Product;
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

            <div class="grid gap-2">
                <img
                    :src="`/storage/${product.thumbnail}`"
                    :alt="product.name"
                    class="size-16 rounded object-cover"
                />
                <Label for="thumbnail">Replace thumbnail</Label>
                <Input
                    id="thumbnail"
                    name="thumbnail"
                    type="file"
                    accept="image/*"
                />
                <InputError :message="errors.thumbnail" />
            </div>

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
                        :default-value="product.unit_price"
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

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="category_id">Category</Label>
                    <Select
                        name="category_id"
                        :default-value="product.category_id ? String(product.category_id) : undefined"
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
                        :default-value="product.brand_id ? String(product.brand_id) : undefined"
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
                        :default-value="product.discount"
                    />
                    <InputError :message="errors.discount" />
                </div>

                <div class="grid gap-2">
                    <Label for="discount_type">Discount type</Label>
                    <Select
                        name="discount_type"
                        :default-value="product.discount_type ?? undefined"
                    >
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

            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-2">
                    <Checkbox
                        id="free_shipping"
                        name="free_shipping"
                        :default-value="product.free_shipping"
                    />
                    <Label for="free_shipping">Free shipping</Label>
                    <InputError :message="errors.free_shipping" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox
                        id="refundable"
                        name="refundable"
                        :default-value="product.refundable"
                    />
                    <Label for="refundable">Refundable</Label>
                    <InputError :message="errors.refundable" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox
                        id="featured"
                        name="featured"
                        :default-value="product.featured"
                    />
                    <Label for="featured">Featured</Label>
                    <InputError :message="errors.featured" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox
                        id="status"
                        name="status"
                        :default-value="product.status"
                    />
                    <Label for="status">Active</Label>
                    <InputError :message="errors.status" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save product</Button>
            </div>
        </Form>
    </div>
</template>
