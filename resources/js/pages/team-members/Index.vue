<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import TeamMemberController from '@/actions/App/Http/Controllers/Backend/TeamMemberController';
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
import { create, edit, index } from '@/routes/team-members';

type TeamMember = {
    id: number;
    name: string;
    role: string;
    photo: string;
    photo_storage_type: string;
    photo_alt_text: string | null;
    sort_order: number;
    status: boolean;
};

defineProps<{
    teamMembers: TeamMember[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Team members',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Team members" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Team members"
                description="Manage the staff shown on the about page"
            />
            <Button as-child>
                <Link :href="create()">Add team member</Link>
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
                        <th class="p-3 font-medium">Photo</th>
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Role</th>
                        <th class="p-3 font-medium">Order</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="teamMember in teamMembers"
                        :key="teamMember.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${teamMember.photo}`"
                                :alt="
                                    teamMember.photo_alt_text ??
                                    teamMember.name
                                "
                                class="size-10 rounded-full object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">{{ teamMember.name }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ teamMember.role }}
                        </td>
                        <td class="p-3">{{ teamMember.sort_order }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    teamMember.status ? 'default' : 'secondary'
                                "
                            >
                                {{ teamMember.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(teamMember)">Edit</Link>
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
                                                TeamMemberController.destroy.form(
                                                    teamMember,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        teamMember.name
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

                    <tr v-if="teamMembers.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No team members yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
