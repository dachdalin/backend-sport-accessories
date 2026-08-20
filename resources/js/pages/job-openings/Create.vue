<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import JobOpeningController from '@/actions/App/Http/Controllers/Backend/JobOpeningController';
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
import { create, index } from '@/routes/job-openings';

interface EmploymentTypeOption {
    value: string;
    label: string;
}

defineProps<{
    employmentTypes: EmploymentTypeOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Job openings',
                href: index(),
            },
            {
                title: 'Add job opening',
                href: create(),
            },
        ],
    },
});

const textareaClass =
    'placeholder:text-muted-foreground dark:bg-input/30 border-input min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] md:text-sm';
</script>

<template>
    <Head title="Add job opening" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add job opening"
            description="Create a new careers page listing"
        />

        <Form
            v-bind="JobOpeningController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    required
                    autofocus
                    placeholder="Warehouse Associate"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="department">Department</Label>
                    <Input
                        id="department"
                        name="department"
                        placeholder="Warehouse"
                    />
                    <InputError :message="errors.department" />
                </div>

                <div class="grid gap-2">
                    <Label for="location">Location</Label>
                    <Input
                        id="location"
                        name="location"
                        placeholder="Manchester"
                    />
                    <InputError :message="errors.location" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="employment_type">Employment type</Label>
                <Select name="employment_type" default-value="full_time">
                    <SelectTrigger id="employment_type" class="w-full">
                        <SelectValue placeholder="Select a type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in employmentTypes"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.employment_type" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <textarea
                    id="description"
                    name="description"
                    required
                    :class="textareaClass"
                    placeholder="Describe the role, responsibilities, and requirements"
                />
                <InputError :message="errors.description" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create job opening</Button>
            </div>
        </Form>
    </div>
</template>
