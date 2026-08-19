<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import CategoryController from '@/actions/App/Http/Controllers/Backend/CategoryController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
import { edit, index } from '@/routes/categories';

type Category = {
    id: number;
    name: string;
    icon: string;
    parent_id: number | null;
    position: number;
    home_status: boolean;
};

type ParentOption = {
    id: number;
    name: string;
};

const props = defineProps<{
    category: Category;
    parents: ParentOption[];
}>();

defineOptions({
    layout: (pageProps: { category: Category }) => ({
        breadcrumbs: [
            {
                title: 'Categories',
                href: index(),
            },
            {
                title: 'Edit category',
                href: edit(pageProps.category),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit category" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit category"
            :description="`Update the details for ${category.name}`"
        />

        <Form
            v-bind="CategoryController.update.form(category)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    autofocus
                    :default-value="category.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <img
                    :src="`/storage/${category.icon}`"
                    :alt="category.name"
                    class="size-16 rounded object-cover"
                />
                <Label for="icon">Replace icon</Label>
                <Input id="icon" name="icon" type="file" accept="image/*" />
                <InputError :message="errors.icon" />
            </div>

            <div class="grid gap-2">
                <Label for="parent_id">Parent category</Label>
                <Select
                    name="parent_id"
                    :default-value="
                        category.parent_id
                            ? String(category.parent_id)
                            : undefined
                    "
                >
                    <SelectTrigger id="parent_id" class="w-full">
                        <SelectValue placeholder="None (top-level category)" />
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
                    :default-value="category.position"
                />
                <InputError :message="errors.position" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="home_status"
                    name="home_status"
                    :default-value="category.home_status"
                />
                <Label for="home_status">Show on home page</Label>
                <InputError :message="errors.home_status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save category</Button>
            </div>
        </Form>
    </div>
</template>
