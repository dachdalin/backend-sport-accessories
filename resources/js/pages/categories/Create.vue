<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { FolderTree, Tag } from '@lucide/vue';
import { onBeforeUnmount, ref } from 'vue';
import CategoryController from '@/actions/App/Http/Controllers/Backend/CategoryController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { create, index } from '@/routes/categories';

type ParentOption = {
    id: number;
    name: string;
};

defineProps<{
    parents: ParentOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Categories',
                href: index(),
            },
            {
                title: 'Add category',
                href: create(),
            },
        ],
    },
});

const iconPreview = ref<string | null>(null);

function onIconChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (iconPreview.value) {
        URL.revokeObjectURL(iconPreview.value);
    }

    iconPreview.value = file ? URL.createObjectURL(file) : null;
}

onBeforeUnmount(() => {
    if (iconPreview.value) {
        URL.revokeObjectURL(iconPreview.value);
    }
});
</script>

<template>
    <Head title="Add category" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add category"
            description="Create a new product category"
        />

        <Form
            v-bind="CategoryController.store.form()"
            class="flex flex-col gap-6"
            v-slot="{ errors, processing }"
        >
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <Tag
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Identity</CardTitle>
                    </div>
                    <CardDescription>
                        The name and icon shown throughout the storefront.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            name="name"
                            required
                            autofocus
                            placeholder="Category name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="mt-4 grid gap-2">
                        <Label for="icon">Icon</Label>
                        <div class="flex items-center gap-3">
                            <img
                                v-if="iconPreview"
                                :src="iconPreview"
                                alt="Icon preview"
                                class="size-16 shrink-0 rounded-md border border-input object-cover"
                            />
                            <div
                                v-else
                                class="flex size-16 shrink-0 items-center justify-center rounded-md border border-dashed border-input text-xs text-muted-foreground"
                            >
                                No icon
                            </div>
                            <Input
                                id="icon"
                                name="icon"
                                type="file"
                                accept="image/*"
                                @change="onIconChange"
                            />
                        </div>
                        <InputError :message="errors.icon" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <FolderTree
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Organization</CardTitle>
                    </div>
                    <CardDescription>
                        Where it sits in the catalog and how it's ordered.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="parent_id">Parent category</Label>
                            <Select name="parent_id">
                                <SelectTrigger id="parent_id" class="w-full">
                                    <SelectValue
                                        placeholder="None (top-level category)"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="parent in parents"
                                        :key="parent.id"
                                        :value="String(parent.id)"
                                    >
                                        {{ parent.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.parent_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="position">Position</Label>
                            <Input
                                id="position"
                                name="position"
                                type="number"
                                min="0"
                                default-value="0"
                            />
                            <p class="text-sm text-muted-foreground">
                                Lower numbers appear first.
                            </p>
                            <InputError :message="errors.position" />
                        </div>
                    </div>

                    <label
                        for="home_status"
                        class="mt-4 flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                    >
                        <Checkbox id="home_status" name="home_status" />
                        <span class="text-sm font-medium"
                            >Show on home page</span
                        >
                    </label>
                    <InputError :message="errors.home_status" class="mt-2" />
                </CardContent>
                <CardFooter class="gap-3 border-t pt-6">
                    <Button :disabled="processing">
                        <Spinner v-if="processing" />
                        Create category
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </CardFooter>
            </Card>
        </Form>
    </div>
</template>
