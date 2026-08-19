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
                    placeholder="Category name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="icon">Icon</Label>
                <Input id="icon" name="icon" type="file" accept="image/*" />
                <InputError :message="errors.icon" />
            </div>

            <div class="grid gap-2">
                <Label for="parent_id">Parent category</Label>
                <Select name="parent_id">
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
                    default-value="0"
                />
                <InputError :message="errors.position" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="home_status" name="home_status" />
                <Label for="home_status">Show on home page</Label>
                <InputError :message="errors.home_status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create category</Button>
            </div>
        </Form>
    </div>
</template>
