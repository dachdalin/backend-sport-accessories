<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { CalendarRangeIcon, EyeIcon, PackageIcon } from '@lucide/vue';
import StockClearanceSetupController from '@/actions/App/Http/Controllers/Backend/StockClearanceSetupController';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
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
import { create, edit, index } from '@/routes/stock-clearance-setups';

type StockClearanceSetup = {
    id: number;
    discount_type: string;
    discount_amount: string;
    is_active: boolean;
    show_in_homepage: boolean;
    show_in_shop: boolean;
    duration_start_date: string;
    duration_end_date: string;
    items_count: number;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

defineProps<{
    stockClearanceSetups: Paginated<StockClearanceSetup>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Stock clearance',
                href: index(),
            },
        ],
    },
});

type CampaignPhase = 'scheduled' | 'live' | 'ending-soon' | 'ended' | 'paused';

const PHASE_COPY: Record<
    CampaignPhase,
    {
        label: string;
        badge: 'default' | 'secondary' | 'destructive' | 'outline';
        accent: string;
    }
> = {
    scheduled: { label: 'Scheduled', badge: 'outline', accent: 'bg-sky-500' },
    live: { label: 'Live', badge: 'default', accent: 'bg-emerald-500' },
    'ending-soon': {
        label: 'Ending soon',
        badge: 'destructive',
        accent: 'bg-amber-500',
    },
    ended: {
        label: 'Ended',
        badge: 'secondary',
        accent: 'bg-muted-foreground/40',
    },
    paused: {
        label: 'Paused',
        badge: 'secondary',
        accent: 'bg-muted-foreground/40',
    },
};

function phaseOf(setup: StockClearanceSetup): CampaignPhase {
    if (!setup.is_active) {
        return 'paused';
    }

    const today = new Date().setHours(0, 0, 0, 0);
    const start = new Date(setup.duration_start_date).setHours(0, 0, 0, 0);
    const end = new Date(setup.duration_end_date).setHours(0, 0, 0, 0);

    if (today < start) {
        return 'scheduled';
    }

    if (today > end) {
        return 'ended';
    }

    if (end - today <= 3 * 86400000) {
        return 'ending-soon';
    }

    return 'live';
}

function daysRemaining(setup: StockClearanceSetup): number {
    const today = new Date().setHours(0, 0, 0, 0);
    const end = new Date(setup.duration_end_date).setHours(0, 0, 0, 0);

    return Math.ceil((end - today) / 86400000);
}

function phaseDetail(setup: StockClearanceSetup): string {
    const phase = phaseOf(setup);

    if (phase === 'paused') {
        return 'Not currently running';
    }

    if (phase === 'ended') {
        return 'Campaign has closed';
    }

    if (phase === 'scheduled') {
        return `Starts ${setup.duration_start_date}`;
    }

    const remaining = daysRemaining(setup);

    return remaining <= 0
        ? 'Ends today'
        : `${remaining} ${remaining === 1 ? 'day' : 'days'} left`;
}

function discountLabel(setup: StockClearanceSetup): string {
    return `${setup.discount_amount}${setup.discount_type === 'percent' ? '%' : ''}`;
}
</script>

<template>
    <Head title="Stock clearance" />

    <div class="flex flex-col gap-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Stock clearance"
                description="Manage stock clearance campaigns and the products included in each"
            />
            <Button as-child class="w-full sm:w-auto">
                <Link :href="create()">Add setup</Link>
            </Button>
        </div>

        <!-- Desktop / tablet table -->
        <div
            class="hidden overflow-x-auto rounded-xl border border-sidebar-border/70 md:block dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Discount</th>
                        <th class="p-3 font-medium">Products</th>
                        <th class="p-3 font-medium">Duration</th>
                        <th class="p-3 font-medium">Visibility</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="setup in stockClearanceSetups.data"
                        :key="setup.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ discountLabel(setup) }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            <span class="inline-flex items-center gap-1.5">
                                <PackageIcon
                                    class="size-3.5 shrink-0"
                                    aria-hidden="true"
                                />
                                {{ setup.items_count }}
                            </span>
                        </td>
                        <td class="p-3 text-muted-foreground">
                            <div class="flex items-center gap-1.5">
                                <CalendarRangeIcon
                                    class="size-3.5 shrink-0"
                                    aria-hidden="true"
                                />
                                <span
                                    >{{ setup.duration_start_date }} –
                                    {{ setup.duration_end_date }}</span
                                >
                            </div>
                        </td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-1">
                                <Badge
                                    v-if="setup.show_in_homepage"
                                    variant="secondary"
                                    >Homepage</Badge
                                >
                                <Badge
                                    v-if="setup.show_in_shop"
                                    variant="secondary"
                                    >Shop</Badge
                                >
                                <span
                                    v-if="
                                        !setup.show_in_homepage &&
                                        !setup.show_in_shop
                                    "
                                    class="text-muted-foreground"
                                    >Hidden</span
                                >
                            </div>
                        </td>
                        <td class="p-3">
                            <Badge :variant="PHASE_COPY[phaseOf(setup)].badge">
                                {{ PHASE_COPY[phaseOf(setup)].label }}
                            </Badge>
                            <div class="mt-1 text-xs text-muted-foreground">
                                {{ phaseDetail(setup) }}
                            </div>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(setup)">Edit</Link>
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
                                                StockClearanceSetupController.destroy.form(
                                                    setup,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete setup #{{
                                                        setup.id
                                                    }}?</DialogTitle
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

                    <tr v-if="stockClearanceSetups.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No stock clearance setups yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile card list -->
        <div class="flex flex-col gap-3 md:hidden">
            <p
                v-if="stockClearanceSetups.data.length === 0"
                class="rounded-xl border border-sidebar-border/70 p-6 text-center text-sm text-muted-foreground dark:border-sidebar-border"
            >
                No stock clearance setups yet.
            </p>

            <div
                v-for="setup in stockClearanceSetups.data"
                :key="setup.id"
                class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <span
                    class="absolute inset-y-0 left-0 w-1"
                    :class="PHASE_COPY[phaseOf(setup)].accent"
                    aria-hidden="true"
                />
                <div class="flex flex-col gap-3 p-4 pl-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-lg font-semibold">
                                {{ discountLabel(setup) }} off
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {{ phaseDetail(setup) }}
                            </div>
                        </div>
                        <Badge :variant="PHASE_COPY[phaseOf(setup)].badge">
                            {{ PHASE_COPY[phaseOf(setup)].label }}
                        </Badge>
                    </div>

                    <div
                        class="flex flex-col gap-1.5 text-sm text-muted-foreground"
                    >
                        <div class="flex items-center gap-1.5">
                            <PackageIcon
                                class="size-3.5 shrink-0"
                                aria-hidden="true"
                            />
                            <span
                                >{{ setup.items_count }}
                                {{
                                    setup.items_count === 1
                                        ? 'product'
                                        : 'products'
                                }}</span
                            >
                        </div>
                        <div class="flex items-center gap-1.5">
                            <CalendarRangeIcon
                                class="size-3.5 shrink-0"
                                aria-hidden="true"
                            />
                            <span
                                >{{ setup.duration_start_date }} –
                                {{ setup.duration_end_date }}</span
                            >
                        </div>
                        <div class="flex items-center gap-1.5">
                            <EyeIcon
                                class="size-3.5 shrink-0"
                                aria-hidden="true"
                            />
                            <span
                                v-if="
                                    setup.show_in_homepage || setup.show_in_shop
                                "
                            >
                                {{
                                    [
                                        setup.show_in_homepage && 'Homepage',
                                        setup.show_in_shop && 'Shop',
                                    ]
                                        .filter(Boolean)
                                        .join(' · ')
                                }}
                            </span>
                            <span v-else>Hidden from storefront</span>
                        </div>
                    </div>

                    <div
                        class="flex gap-2 border-t border-sidebar-border/70 pt-3 dark:border-sidebar-border"
                    >
                        <Button
                            variant="outline"
                            size="sm"
                            as-child
                            class="flex-1"
                        >
                            <Link :href="edit(setup)">Edit</Link>
                        </Button>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    class="flex-1"
                                    >Delete</Button
                                >
                            </DialogTrigger>
                            <DialogContent>
                                <Form
                                    v-bind="
                                        StockClearanceSetupController.destroy.form(
                                            setup,
                                        )
                                    "
                                    :options="{ preserveScroll: true }"
                                    v-slot="{ processing }"
                                >
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle
                                            >Delete setup #{{
                                                setup.id
                                            }}?</DialogTitle
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
                </div>
            </div>
        </div>

        <Pagination
            :meta="stockClearanceSetups"
            label="stock clearance setups"
        />
    </div>
</template>
