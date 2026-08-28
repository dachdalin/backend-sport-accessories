<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import WishlistController from '@/actions/App/Http/Controllers/Backend/WishlistController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as wishlistsIndex } from '@/routes/wishlists';

interface SelectOption {
    value: number;
    label: string;
}

interface Wishlist {
    id: number;
    product_id: number;
    customer_name: string;
    customer_email: string | null;
    product: {
        id: number;
        name: string;
    };
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
}

defineProps<{
    wishlists: Paginated<Wishlist>;
    products: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Wishlists',
                href: wishlistsIndex(),
            },
        ],
    },
});

const createOpen = ref(false);
const editingWishlist = ref<Wishlist | null>(null);

function openEdit(wishlist: Wishlist) {
    editingWishlist.value = wishlist;
}
</script>

<template>
    <Head title="Wishlists" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Wishlists"
                description="Track which products customers have wishlisted"
            />

            <Dialog v-model:open="createOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus />
                        Add wishlist entry
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="WishlistController.store.form()"
                        reset-on-success
                        @success="createOpen = false"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <DialogHeader>
                            <DialogTitle>Add wishlist entry</DialogTitle>
                            <DialogDescription>
                                Record a product a customer has wishlisted.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="create-product_id">Product</Label>
                            <Select name="product_id">
                                <SelectTrigger
                                    id="create-product_id"
                                    class="w-full"
                                >
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
                            <Label for="create-customer_name"
                                >Customer name</Label
                            >
                            <Input
                                id="create-customer_name"
                                name="customer_name"
                                placeholder="Jane Doe"
                                required
                            />
                            <InputError :message="errors.customer_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="create-customer_email"
                                >Customer email</Label
                            >
                            <Input
                                id="create-customer_email"
                                name="customer_email"
                                type="email"
                                placeholder="jane@example.com"
                            />
                            <InputError :message="errors.customer_email" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="processing">
                                Save
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>

        <div
            class="relative overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-4 py-3 font-medium">Product</th>
                        <th class="px-4 py-3 font-medium">Customer</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="wishlist in wishlists.data"
                        :key="wishlist.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">{{ wishlist.product.name }}</td>
                        <td class="px-4 py-3">
                            {{ wishlist.customer_name }}
                        </td>
                        <td class="px-4 py-3">
                            {{ wishlist.customer_email ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(wishlist)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                        >
                                            <Trash2 />
                                            <span class="sr-only"
                                                >Delete</span
                                            >
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                WishlistController.destroy.form(
                                                    {
                                                        wishlist: wishlist.id,
                                                    },
                                                )
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete this wishlist
                                                    entry?</DialogTitle
                                                >
                                                <DialogDescription>
                                                    This cannot be undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button
                                                        variant="secondary"
                                                    >
                                                        Cancel
                                                    </Button>
                                                </DialogClose>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="wishlists.data.length === 0">
                        <td
                            colspan="4"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No wishlist entries yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="wishlists" label="wishlists" />

        <Dialog
            :open="editingWishlist !== null"
            @update:open="(open) => !open && (editingWishlist = null)"
        >
            <DialogContent v-if="editingWishlist">
                <Form
                    v-bind="
                        WishlistController.update.form({
                            wishlist: editingWishlist.id,
                        })
                    "
                    @success="editingWishlist = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit wishlist entry</DialogTitle>
                        <DialogDescription>
                            Update the product or customer details.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-product_id">Product</Label>
                        <Select
                            name="product_id"
                            :default-value="String(editingWishlist.product_id)"
                        >
                            <SelectTrigger
                                id="edit-product_id"
                                class="w-full"
                            >
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
                        <Label for="edit-customer_name">Customer name</Label>
                        <Input
                            id="edit-customer_name"
                            name="customer_name"
                            :default-value="editingWishlist.customer_name"
                            required
                        />
                        <InputError :message="errors.customer_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-customer_email"
                            >Customer email</Label
                        >
                        <Input
                            id="edit-customer_email"
                            name="customer_email"
                            type="email"
                            :default-value="
                                editingWishlist.customer_email ?? undefined
                            "
                        />
                        <InputError :message="errors.customer_email" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing">
                            Save
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>
