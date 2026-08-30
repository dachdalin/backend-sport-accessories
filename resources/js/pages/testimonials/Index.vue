<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Star } from '@lucide/vue';
import TestimonialController from '@/actions/App/Http/Controllers/Backend/TestimonialController';
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
import { create, edit, index } from '@/routes/testimonials';

type Testimonial = {
    id: number;
    customer_name: string;
    customer_role: string | null;
    content: string;
    rating: number;
    avatar: string;
    avatar_storage_type: string;
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
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

defineProps<{
    testimonials: Paginated<Testimonial>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Testimonials',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Testimonials" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Testimonials"
                description="Manage the customer testimonials shown on the storefront"
            />
            <Button as-child>
                <Link :href="create()">Add testimonial</Link>
            </Button>
        </div>

        <!-- Card grid: default from mobile up through tablet -->
        <div
            v-if="testimonials.data.length > 0"
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:hidden"
        >
            <div
                v-for="testimonial in testimonials.data"
                :key="testimonial.id"
                class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img
                            :src="`/storage/${testimonial.avatar}`"
                            :alt="testimonial.customer_name"
                            class="h-11 w-11 shrink-0 rounded-full object-cover ring-2 ring-border"
                        />
                        <div>
                            <p class="font-medium">
                                {{ testimonial.customer_name }}
                            </p>
                            <p
                                v-if="testimonial.customer_role"
                                class="text-xs text-muted-foreground"
                            >
                                {{ testimonial.customer_role }}
                            </p>
                        </div>
                    </div>
                    <Badge
                        :variant="testimonial.status ? 'default' : 'secondary'"
                    >
                        {{ testimonial.status ? 'Active' : 'Inactive' }}
                    </Badge>
                </div>

                <div
                    class="flex gap-0.5"
                    role="img"
                    :aria-label="`${testimonial.rating} out of 5 stars`"
                >
                    <Star
                        v-for="star in 5"
                        :key="star"
                        class="size-4"
                        :class="
                            star <= testimonial.rating
                                ? 'fill-[#FF8904] text-[#FF8904]'
                                : 'text-muted-foreground/30'
                        "
                    />
                </div>

                <p class="line-clamp-4 text-sm text-muted-foreground">
                    “{{ testimonial.content }}”
                </p>

                <div class="mt-auto flex justify-end gap-2 pt-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="edit(testimonial)">Edit</Link>
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
                                    TestimonialController.destroy.form(
                                        testimonial,
                                    )
                                "
                                :options="{ preserveScroll: true }"
                                v-slot="{ processing }"
                            >
                                <DialogHeader class="space-y-3">
                                    <DialogTitle
                                        >Delete testimonial from "{{
                                            testimonial.customer_name
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
            </div>
        </div>

        <div
            v-else
            class="rounded-xl border border-sidebar-border/70 p-6 text-center text-muted-foreground lg:hidden dark:border-sidebar-border"
        >
            No testimonials yet.
        </div>

        <!-- Table: desktop and up, where a quote column has room to breathe -->
        <div
            class="hidden overflow-x-auto rounded-xl border border-sidebar-border/70 lg:block dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Customer</th>
                        <th class="p-3 font-medium">Testimonial</th>
                        <th class="p-3 font-medium">Rating</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="testimonial in testimonials.data"
                        :key="testimonial.id"
                        class="border-b border-sidebar-border/70 align-top last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <img
                                    :src="`/storage/${testimonial.avatar}`"
                                    :alt="testimonial.customer_name"
                                    class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-border"
                                />
                                <div>
                                    <p class="font-medium">
                                        {{ testimonial.customer_name }}
                                    </p>
                                    <p
                                        v-if="testimonial.customer_role"
                                        class="text-xs text-muted-foreground"
                                    >
                                        {{ testimonial.customer_role }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="max-w-sm p-3">
                            <p class="line-clamp-2 text-muted-foreground">
                                “{{ testimonial.content }}”
                            </p>
                        </td>
                        <td class="p-3">
                            <div
                                class="flex gap-0.5"
                                role="img"
                                :aria-label="`${testimonial.rating} out of 5 stars`"
                            >
                                <Star
                                    v-for="star in 5"
                                    :key="star"
                                    class="size-4"
                                    :class="
                                        star <= testimonial.rating
                                            ? 'fill-[#FF8904] text-[#FF8904]'
                                            : 'text-muted-foreground/30'
                                    "
                                />
                            </div>
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    testimonial.status ? 'default' : 'secondary'
                                "
                            >
                                {{ testimonial.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(testimonial)">Edit</Link>
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
                                                TestimonialController.destroy.form(
                                                    testimonial,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete testimonial from "{{
                                                        testimonial.customer_name
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

                    <tr v-if="testimonials.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No testimonials yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="testimonials" label="testimonials" />
    </div>
</template>
