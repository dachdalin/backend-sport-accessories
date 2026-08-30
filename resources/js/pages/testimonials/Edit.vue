<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Camera, Eye, Quote, Rocket, User } from '@lucide/vue';
import TestimonialController from '@/actions/App/Http/Controllers/Backend/TestimonialController';
import Heading from '@/components/Heading.vue';
import ImageDropzone from '@/components/ImageDropzone.vue';
import InputError from '@/components/InputError.vue';
import StarRatingInput from '@/components/StarRatingInput.vue';
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
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/testimonials';

type Testimonial = {
    id: number;
    customer_name: string;
    customer_role: string | null;
    content: string;
    rating: number;
    avatar_url: string;
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

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit testimonial"
            :description="`Update the testimonial from ${props.testimonial.customer_name}`"
        />

        <Form
            v-bind="TestimonialController.update.form(props.testimonial)"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <User
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Customer</CardTitle>
                        </div>
                        <CardDescription>
                            Who is speaking, and how to credit them.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
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
                                :default-value="
                                    props.testimonial.customer_role ?? ''
                                "
                                placeholder="Marathon runner"
                            />
                            <InputError :message="errors.customer_role" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Quote
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Testimonial</CardTitle>
                        </div>
                        <CardDescription>
                            The words customers read on the storefront.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="content">Content</Label>
                            <Textarea
                                id="content"
                                name="content"
                                required
                                :default-value="props.testimonial.content"
                                class="min-h-28"
                            />
                            <InputError :message="errors.content" />
                        </div>

                        <StarRatingInput
                            id="rating"
                            name="rating"
                            label="Rating"
                            :default-value="props.testimonial.rating"
                            :error="errors.rating"
                        />
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Camera
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Photo</CardTitle>
                        </div>
                        <CardDescription>
                            Shown beside the quote. Replace it or leave it as
                            is.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ImageDropzone
                            name="avatar"
                            label="Avatar"
                            hint="PNG, JPG or WEBP, up to 2MB."
                            :error="errors.avatar"
                            :processing="processing"
                            :initial-previews="[props.testimonial.avatar_url]"
                        />
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
                            Whether this testimonial is shown to customers.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <label
                            for="status"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                value="1"
                                :default-value="props.testimonial.status"
                            />
                            <span class="text-sm font-medium">Active</span>
                        </label>
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
                            Save your changes to this testimonial.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save testimonial
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>
