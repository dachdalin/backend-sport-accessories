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
import { edit, index } from '@/routes/job-openings';

interface EmploymentTypeOption {
    value: string;
    label: string;
}

type JobOpening = {
    id: number;
    title: string;
    department: string | null;
    location: string | null;
    employment_type: string;
    description: string;
    status: boolean;
};

const props = defineProps<{
    jobOpening: JobOpening;
    employmentTypes: EmploymentTypeOption[];
}>();

defineOptions({
    layout: (pageProps: { jobOpening: JobOpening }) => ({
        breadcrumbs: [
            {
                title: 'Job openings',
                href: index(),
            },
            {
                title: 'Edit job opening',
                href: edit(pageProps.jobOpening),
            },
        ],
    }),
});

const textareaClass =
    'placeholder:text-muted-foreground dark:bg-input/30 border-input min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] md:text-sm';
</script>

<template>
    <Head title="Edit job opening" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit job opening"
            :description="`Update the details for ${props.jobOpening.title}`"
        />

        <Form
            v-bind="JobOpeningController.update.form(props.jobOpening)"
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
                    :default-value="props.jobOpening.title"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="department">Department</Label>
                    <Input
                        id="department"
                        name="department"
                        :default-value="props.jobOpening.department ?? ''"
                        placeholder="Warehouse"
                    />
                    <InputError :message="errors.department" />
                </div>

                <div class="grid gap-2">
                    <Label for="location">Location</Label>
                    <Input
                        id="location"
                        name="location"
                        :default-value="props.jobOpening.location ?? ''"
                        placeholder="Manchester"
                    />
                    <InputError :message="errors.location" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="employment_type">Employment type</Label>
                <Select
                    name="employment_type"
                    :default-value="props.jobOpening.employment_type"
                >
                    <SelectTrigger id="employment_type" class="w-full">
                        <SelectValue placeholder="Select a type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in props.employmentTypes"
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
                    :default-value="props.jobOpening.description"
                />
                <InputError :message="errors.description" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.jobOpening.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save job opening</Button>
            </div>
        </Form>
    </div>
</template>
