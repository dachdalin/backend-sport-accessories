<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TestimonialController from '@/actions/App/Http/Controllers/Backend/TestimonialController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/testimonials';

type Testimonial = {
    id: number;
    customer_name: string;
    customer_role: string | null;
    content: string;
    rating: number;
    avatar: string;
    status: boolean;
};

const props = defineProps<{
    testimonial: Testimonial;
}>();

defineOptions({
    layout: (pageProps: { testimonial: Testimonial }) => ({
        breadcrumbs: [
            {
                title: 'Testimonials',
                href: index(),
            },
            {
                title: 'Edit testimonial',
                href: edit(pageProps.testimonial),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit testimonial" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit testimonial"
            :description="`Update the testimonial from ${props.testimonial.customer_name}`"
        />

        <Form
            v-bind="TestimonialController.update.form(props.testimonial)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="customer_name">Customer name</Label>
                <Input
                    id="customer_name"
                    name="customer_name"
                    required
                    autofocus
                    :default-value="props.testimonial.customer_name"
                />
                <InputError :message="errors.customer_name" />
            </div>

            <div class="grid gap-2">
                <Label for="customer_role">Customer role</Label>
                <Input
                    id="customer_role"
                    name="customer_role"
                    :default-value="props.testimonial.customer_role ?? ''"
                    placeholder="Marathon runner"
                />
                <InputError :message="errors.customer_role" />
            </div>

            <div class="grid gap-2">
                <Label for="content">Content</Label>
                <Textarea
                    id="content"
                    name="content"
                    required
                    :default-value="props.testimonial.content"
                />
                <InputError :message="errors.content" />
            </div>

            <div class="grid gap-2">
                <Label for="rating">Rating</Label>
                <Input
                    id="rating"
                    name="rating"
                    type="number"
                    min="1"
                    max="5"
                    :default-value="props.testimonial.rating"
                />
                <InputError :message="errors.rating" />
            </div>

            <div class="grid gap-2">
                <img
                    :src="`/storage/${props.testimonial.avatar}`"
                    :alt="props.testimonial.customer_name"
                    class="h-16 w-16 rounded-full object-cover"
                />
                <Label for="avatar">Replace avatar</Label>
                <Input id="avatar" name="avatar" type="file" accept="image/*" />
                <InputError :message="errors.avatar" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.testimonial.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save testimonial</Button>
            </div>
        </Form>
    </div>
</template>
