<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Link2Icon, EyeIcon, ImageIcon, SendIcon } from '@lucide/vue';
import { computed, ref } from 'vue';
import FeatureDealController from '@/actions/App/Http/Controllers/Backend/FeatureDealController';
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
import { Spinner } from '@/components/ui/spinner';
import { create, index } from '@/routes/feature-deals';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Feature deals',
                href: index(),
            },
            {
                title: 'Add feature deal',
                href: create(),
            },
        ],
    },
});

const url = ref('');
const status = ref(true);
const photoFiles = ref<File[]>([]);

const previewSrc = computed(() =>
    photoFiles.value[0] ? URL.createObjectURL(photoFiles.value[0]) : null,
);

const destinationLabel = computed(() => {
    if (!url.value) {
        return null;
    }

    try {
        return new URL(url.value).hostname;
    } catch {
        return url.value;
    }
});
</script>

<template>
    <Head title="Add feature deal" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Add feature deal"
            description="Create a new featured deal tile shown to customers"
        />

        <Form
            v-bind="FeatureDealController.store.form()"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <ImageIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Artwork</CardTitle>
                        </div>
                        <CardDescription>
                            A wide image works best — it fills the tile
                            without cropping the focal point.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ImageDropzone
                            name="photo"
                            label="Photo"
                            hint="PNG, JPG or WEBP, up to 5MB. 16:9 recommended."
                            :error="errors.photo"
                            :processing="processing"
                            @update:model-value="photoFiles = $event"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Link2Icon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Destination &amp; visibility</CardTitle>
                        </div>
                        <CardDescription>
                            Where the tile sends customers, and whether it's
                            live.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="url">URL</Label>
                            <Input
                                id="url"
                                v-model="url"
                                name="url"
                                type="url"
                                placeholder="https://example.com"
                            />
                            <InputError :message="errors.url" />
                        </div>

                        <label
                            for="status"
                            class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                        >
                            <Checkbox id="status" name="status" v-model="status" />
                            <span class="grid gap-0.5">
                                <span class="text-sm font-medium">Active</span>
                                <span class="text-xs text-muted-foreground">
                                    Shown to customers as soon as it's
                                    published.
                                </span>
                            </span>
                        </label>
                        <InputError :message="errors.status" />
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <EyeIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Preview</CardTitle>
                        </div>
                        <CardDescription>
                            How this tile will look on the storefront.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            class="group relative aspect-video w-full overflow-hidden rounded-lg border border-input bg-muted"
                        >
                            <img
                                v-if="previewSrc"
                                :src="previewSrc"
                                alt="Feature deal preview"
                                class="size-full object-cover"
                            />
                            <div
                                v-else
                                class="flex size-full flex-col items-center justify-center gap-1.5 text-muted-foreground"
                            >
                                <ImageIcon class="size-6" aria-hidden="true" />
                                <span class="text-xs"
                                    >Artwork appears here</span
                                >
                            </div>

                            <div
                                class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-gradient-to-t from-black/70 to-transparent p-3"
                            >
                                <span
                                    class="truncate text-xs font-medium text-white"
                                >
                                    <Link2Icon
                                        class="mr-1 inline size-3"
                                        aria-hidden="true"
                                    />
                                    {{ destinationLabel ?? 'No link yet' }}
                                </span>
                                <Badge
                                    :variant="status ? 'default' : 'secondary'"
                                >
                                    {{ status ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardFooter class="flex-col gap-3 pt-6 sm:flex-row">
                        <Button class="w-full sm:w-auto" :disabled="processing">
                            <Spinner v-if="processing" />
                            <SendIcon v-else class="size-4" aria-hidden="true" />
                            Create feature deal
                        </Button>
                        <Button
                            class="w-full sm:w-auto"
                            variant="outline"
                            as-child
                        >
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>
