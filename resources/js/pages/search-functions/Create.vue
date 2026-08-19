<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SearchFunctionController from '@/actions/App/Http/Controllers/Backend/SearchFunctionController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { create, index } from '@/routes/search-functions';

interface VisibilityOption {
    value: string;
    label: string;
}

defineProps<{
    visibilities: VisibilityOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Search functions',
                href: index(),
            },
            {
                title: 'Add search function',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add search function" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add search function"
            description="Create a quick search shortcut"
        />

        <Form
            v-bind="SearchFunctionController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="key">Key</Label>
                <Input
                    id="key"
                    name="key"
                    required
                    autofocus
                    placeholder="New arrivals"
                />
                <InputError :message="errors.key" />
            </div>

            <div class="grid gap-2">
                <Label for="url">URL</Label>
                <Input
                    id="url"
                    name="url"
                    required
                    placeholder="/products?sort=new"
                />
                <InputError :message="errors.url" />
            </div>

            <div class="grid gap-2">
                <Label for="visible_for">Visible for</Label>
                <Select name="visible_for" default-value="admin">
                    <SelectTrigger id="visible_for" class="w-full">
                        <SelectValue placeholder="Select an audience" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in visibilities"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.visible_for" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create search function</Button>
            </div>
        </Form>
    </div>
</template>
