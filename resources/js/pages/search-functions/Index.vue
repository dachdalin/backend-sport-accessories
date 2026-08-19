<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import SearchFunctionController from '@/actions/App/Http/Controllers/Backend/SearchFunctionController';
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
import { create, edit, index } from '@/routes/search-functions';

type SearchFunction = {
    id: number;
    key: string;
    url: string;
    visible_for: string;
};

defineProps<{
    searchFunctions: SearchFunction[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Search functions',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Search functions" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Search functions"
                description="Manage quick search shortcuts shown across the app"
            />
            <Button as-child>
                <Link :href="create()">Add search function</Link>
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
                        <th class="p-3 font-medium">Key</th>
                        <th class="p-3 font-medium">URL</th>
                        <th class="p-3 font-medium">Visible for</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="searchFunction in searchFunctions"
                        :key="searchFunction.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ searchFunction.key }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ searchFunction.url }}
                        </td>
                        <td class="p-3">
                            <Badge variant="secondary">
                                {{ searchFunction.visible_for }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(searchFunction)"
                                        >Edit</Link
                                    >
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                SearchFunctionController.destroy.form(
                                                    searchFunction,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        searchFunction.key
                                                    }}"?</DialogTitle
                                                >
                                            </DialogHeader>

                                            <DialogFooter class="mt-6 gap-2">
                                                <DialogClose as-child>
                                                    <Button
                                                        variant="secondary"
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

                    <tr v-if="searchFunctions.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No search functions yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
