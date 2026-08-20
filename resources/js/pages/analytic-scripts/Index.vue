<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import AnalyticScriptController from '@/actions/App/Http/Controllers/Backend/AnalyticScriptController';
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
import { create, edit, index } from '@/routes/analytic-scripts';

type AnalyticScript = {
    id: number;
    name: string;
    type: string;
    script_id: string | null;
    script: string;
    status: boolean;
};

defineProps<{
    analyticScripts: AnalyticScript[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Analytic scripts',
                href: index(),
            },
        ],
    },
});

const typeLabels: Record<string, string> = {
    google_analytics: 'Google Analytics',
    google_tag_manager: 'Google Tag Manager',
    facebook_pixel: 'Facebook Pixel',
    custom: 'Custom',
};
</script>

<template>
    <Head title="Analytic scripts" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Analytic scripts"
                description="Manage tracking and marketing pixel scripts"
            />
            <Button as-child>
                <Link :href="create()">Add script</Link>
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
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Type</th>
                        <th class="p-3 font-medium">Script ID</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="analyticScript in analyticScripts"
                        :key="analyticScript.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ analyticScript.name }}
                        </td>
                        <td class="p-3">
                            {{
                                typeLabels[analyticScript.type] ??
                                analyticScript.type
                            }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ analyticScript.script_id ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    analyticScript.status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    analyticScript.status
                                        ? 'Active'
                                        : 'Inactive'
                                }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(analyticScript)"
                                        >Edit</Link
                                    >
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
                                                AnalyticScriptController.destroy.form(
                                                    analyticScript,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        analyticScript.name
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

                    <tr v-if="analyticScripts.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No analytic scripts yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
