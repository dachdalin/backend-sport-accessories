<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ReturnPolicyController from '@/actions/App/Http/Controllers/Backend/ReturnPolicyController';
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
import { create, edit, index } from '@/routes/return-policies';

type ReturnPolicy = {
    id: number;
    title: string;
    description: string;
    days_allowed: number;
    status: boolean;
};

defineProps<{
    returnPolicies: ReturnPolicy[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Return policies',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Return policies" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Return policies"
                description="Manage the return policies shown to customers"
            />
            <Button as-child>
                <Link :href="create()">Add return policy</Link>
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
                        <th class="p-3 font-medium">Description</th>
                        <th class="p-3 font-medium">Days allowed</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="returnPolicy in returnPolicies"
                        :key="returnPolicy.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ returnPolicy.title }}
                        </td>
                        <td class="max-w-md truncate p-3 text-muted-foreground">
                            {{ returnPolicy.description }}
                        </td>
                        <td class="p-3">
                            {{ returnPolicy.days_allowed }} days
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    returnPolicy.status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    returnPolicy.status ? 'Active' : 'Inactive'
                                }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(returnPolicy)">Edit</Link>
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
                                                ReturnPolicyController.destroy.form(
                                                    returnPolicy,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        returnPolicy.title
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

                    <tr v-if="returnPolicies.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No return policies yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
