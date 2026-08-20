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
import { create, index } from '@/routes/testimonials';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Testimonials',
                href: index(),
            },
            {
                title: 'Add testimonial',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add testimonial" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add testimonial"
            description="Create a new customer testimonial"
        />

        <Form
            v-bind="TestimonialController.store.form()"
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
                    placeholder="Jane Doe"
                />
                <InputError :message="errors.customer_name" />
            </div>

            <div class="grid gap-2">
                <Label for="customer_role">Customer role</Label>
                <Input
                    id="customer_role"
                    name="customer_role"
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
                    placeholder="What the customer said"
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
                    default-value="5"
                />
                <InputError :message="errors.rating" />
            </div>

            <div class="grid gap-2">
                <Label for="avatar">Avatar</Label>
                <Input id="avatar" name="avatar" type="file" accept="image/*" />
                <InputError :message="errors.avatar" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create testimonial</Button>
            </div>
        </Form>
    </div>
</template>
