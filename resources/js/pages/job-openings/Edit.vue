<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    BriefcaseIcon,
    Building2Icon,
    ClockIcon,
    EyeIcon,
    FileTextIcon,
    MapPinIcon,
    SendIcon,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import JobOpeningController from '@/actions/App/Http/Controllers/Backend/JobOpeningController';
import Heading from '@/components/Heading.vue';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
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

const title = ref(props.jobOpening.title);
const department = ref(props.jobOpening.department ?? '');
const location = ref(props.jobOpening.location ?? '');
const employmentType = ref(props.jobOpening.employment_type);
const description = ref(props.jobOpening.description);
const status = ref(props.jobOpening.status);

const employmentTypeLabel = computed(
    () =>
        props.employmentTypes.find(
            (option) => option.value === employmentType.value,
        )?.label ?? employmentType.value,
);

const metaLine = computed(() =>
    [department.value, location.value].filter(Boolean).join(' · '),
);
</script>

<template>
    <Head title="Edit job opening" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit job opening"
            :description="`Update the details for ${jobOpening.title}`"
        />

        <Form
            v-bind="JobOpeningController.update.form(jobOpening)"
            class="grid gap-6 xl:grid-cols-3 xl:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 xl:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <BriefcaseIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Role</CardTitle>
                        </div>
                        <CardDescription
                            >What the role is called, where it sits, and how
                            it's worked.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="title">Title</Label>
                            <Input
                                id="title"
                                v-model="title"
                                name="title"
                                required
                                autofocus
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="department">Department</Label>
                                <Input
                                    id="department"
                                    v-model="department"
                                    name="department"
                                    placeholder="Warehouse"
                                />
                                <InputError :message="errors.department" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="location">Location</Label>
                                <Input
                                    id="location"
                                    v-model="location"
                                    name="location"
                                    placeholder="Manchester"
                                />
                                <InputError :message="errors.location" />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:max-w-64">
                            <Label for="employment_type">Employment type</Label>
                            <Select
                                :model-value="employmentType"
                                name="employment_type"
                                @update:model-value="
                                    (value) => (employmentType = String(value))
                                "
                            >
                                <SelectTrigger
                                    id="employment_type"
                                    class="w-full"
                                >
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

                        <label
                            for="status"
                            class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                v-model="status"
                            />
                            <span class="grid gap-0.5">
                                <span class="text-sm font-medium">Active</span>
                                <span class="text-xs text-muted-foreground"
                                    >Listed on the careers page while this stays
                                    checked.</span
                                >
                            </span>
                        </label>
                        <InputError :message="errors.status" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <FileTextIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Description</CardTitle>
                        </div>
                        <CardDescription
                            >The responsibilities and requirements candidates
                            will read.</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="description"
                                name="description"
                                required
                                rows="8"
                            />
                            <InputError :message="errors.description" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card class="xl:sticky xl:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <EyeIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Listing preview</CardTitle>
                        </div>
                        <CardDescription
                            >How this shows up in the careers
                            list.</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            class="flex flex-col gap-2 rounded-lg border border-input p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <p
                                    class="font-semibold"
                                    :class="{
                                        'text-muted-foreground italic': !title,
                                    }"
                                >
                                    {{ title || 'Job title' }}
                                </p>
                                <Badge
                                    :variant="status ? 'default' : 'secondary'"
                                    class="shrink-0"
                                >
                                    {{ status ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>

                            <div
                                class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground"
                            >
                                <span
                                    v-if="metaLine"
                                    class="inline-flex items-center gap-1"
                                >
                                    <Building2Icon
                                        class="size-3.5 shrink-0"
                                        aria-hidden="true"
                                    />
                                    {{ metaLine }}
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1"
                                >
                                    <MapPinIcon
                                        class="size-3.5 shrink-0"
                                        aria-hidden="true"
                                    />
                                    Department and location not set
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <ClockIcon
                                        class="size-3.5 shrink-0"
                                        aria-hidden="true"
                                    />
                                    {{ employmentTypeLabel }}
                                </span>
                            </div>

                            <p
                                class="line-clamp-3 text-sm text-muted-foreground"
                            >
                                {{
                                    description ||
                                    'The description you write appears here.'
                                }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardFooter class="flex-col gap-3 pt-6 sm:flex-row">
                        <Button class="w-full sm:w-auto" :disabled="processing">
                            <Spinner v-if="processing" />
                            <SendIcon
                                v-else
                                class="size-4"
                                aria-hidden="true"
                            />
                            Save job opening
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
