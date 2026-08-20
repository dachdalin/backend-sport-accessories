<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import TestimonialController from '@/actions/App/Http/Controllers/Backend/TestimonialController';
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

defineProps<{
    testimonials: Testimonial[];
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

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Avatar</th>
                        <th class="p-3 font-medium">Customer</th>
                        <th class="p-3 font-medium">Content</th>
                        <th class="p-3 font-medium">Rating</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="testimonial in testimonials"
                        :key="testimonial.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${testimonial.avatar}`"
                                :alt="testimonial.customer_name"
                                class="h-10 w-10 rounded-full object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">
                            {{ testimonial.customer_name }}
                            <div
                                v-if="testimonial.customer_role"
                                class="text-xs font-normal text-muted-foreground"
                            >
                                {{ testimonial.customer_role }}
                            </div>
                        </td>
                        <td class="max-w-md truncate p-3 text-muted-foreground">
                            {{ testimonial.content }}
                        </td>
                        <td class="p-3">{{ testimonial.rating }} / 5</td>
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

                    <tr v-if="testimonials.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No testimonials yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
