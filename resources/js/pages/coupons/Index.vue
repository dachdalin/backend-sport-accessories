<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import CouponController from '@/actions/App/Http/Controllers/Backend/CouponController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/coupons';

type Coupon = {
    id: number;
    code: string;
    type: 'fixed' | 'percentage';
    value: string;
    min_order_amount: string | null;
    usage_limit: number | null;
    expires_at: string | null;
    status: boolean;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
};

defineProps<{
    coupons: Paginated<Coupon>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Coupons',
                href: index(),
            },
        ],
    },
});

function formatValue(coupon: Coupon) {
    return coupon.type === 'percentage'
        ? `${coupon.value}%`
        : `$${coupon.value}`;
}
</script>

<template>
    <Head title="Coupons" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Coupons"
                description="Manage discount coupons for orders"
            />
            <Button as-child>
                <Link :href="create()">Add coupon</Link>
            </Button>
        </div>

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Code</th>
                        <th class="p-3 font-medium">Value</th>
                        <th class="p-3 font-medium">Min order</th>
                        <th class="p-3 font-medium">Usage limit</th>
                        <th class="p-3 font-medium">Expires</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="coupon in coupons.data"
                        :key="coupon.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ coupon.code }}</td>
                        <td class="p-3">{{ formatValue(coupon) }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ coupon.min_order_amount ?? '—' }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ coupon.usage_limit ?? 'Unlimited' }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ coupon.expires_at ?? 'Never' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    coupon.status ? 'default' : 'secondary'
                                "
                            >
                                {{ coupon.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(coupon)">Edit</Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                CouponController.destroy.form(
                                                    coupon,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        coupon.code
                                                    }}"?</DialogTitle
                                                >
                                            </DialogHeader>

                                            <DialogFooter class="mt-6 gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary"
                                                        >Cancel</Button
                                                    >
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

                    <tr v-if="coupons.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="7"
                        >
                            No coupons yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="coupons.links.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="(link, index) in coupons.links" :key="index">
                <span
                    v-if="!link.url"
                    class="rounded-md px-3 py-1.5 text-sm text-muted-foreground"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    class="rounded-md px-3 py-1.5 text-sm"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                    "
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
