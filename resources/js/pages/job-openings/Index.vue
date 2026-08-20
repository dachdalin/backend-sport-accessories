<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import JobOpeningController from '@/actions/App/Http/Controllers/Backend/JobOpeningController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/job-openings';

type JobOpening = {
    id: number;
    title: string;
    department: string | null;
    location: string | null;
    employment_type: string;
    description: string;
    status: boolean;
};

defineProps<{
    jobOpenings: JobOpening[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Job openings',
                href: index(),
            },
        ],
    },
});

const employmentTypeLabels: Record<string, string> = {
    full_time: 'Full-time',
    part_time: 'Part-time',
    contract: 'Contract',
    internship: 'Internship',
};
</script>

<template>
    <Head title="Job openings" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Job openings"
                description="Manage the careers page job listings"
            />
            <Button as-child>
                <Link :href="create()">Add job opening</Link>
            </Button>
        </div>

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Title</th>
                        <th class="p-3 font-medium">Department</th>
                        <th class="p-3 font-medium">Location</th>
                        <th class="p-3 font-medium">Type</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="jobOpening in jobOpenings"
                        :key="jobOpening.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ jobOpening.title }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ jobOpening.department ?? '—' }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ jobOpening.location ?? '—' }}
                        </td>
                        <td class="p-3">
                            {{
                                employmentTypeLabels[
                                    jobOpening.employment_type
                                ] ?? jobOpening.employment_type
                            }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    jobOpening.status ? 'default' : 'secondary'
                                "
                            >
                                {{ jobOpening.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(jobOpening)">Edit</Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                JobOpeningController.destroy.form(
                                                    jobOpening,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        jobOpening.title
                                                    }}"?</DialogTitle
                                                >
                                            </DialogHeader>

                                            <DialogFooter class="mt-6 gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary"
                                                        >Cancel</Button
                                                    >
                                                </DialogClose>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="jobOpenings.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No job openings yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
