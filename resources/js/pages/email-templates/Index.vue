<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye } from '@lucide/vue';
import EmailTemplateController from '@/actions/App/Http/Controllers/Backend/EmailTemplateController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/email-templates';

type EmailTemplate = {
    id: number;
    name: string;
    subject: string;
    body: string;
    status: boolean;
};

defineProps<{
    emailTemplates: EmailTemplate[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Email templates',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Email templates" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Email templates"
                description="Manage the transactional emails sent from your store"
            />
            <Button as-child>
                <Link :href="create()">Add email template</Link>
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
                        <th class="p-3 font-medium">Subject</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="emailTemplate in emailTemplates"
                        :key="emailTemplate.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="max-w-xs p-3 font-medium">
                            {{ emailTemplate.name }}
                        </td>
                        <td
                            class="max-w-md truncate p-3 text-muted-foreground"
                            :title="emailTemplate.subject"
                        >
                            {{ emailTemplate.subject }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    emailTemplate.status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    emailTemplate.status
                                        ? 'Active'
                                        : 'Inactive'
                                }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            aria-label="Preview"
                                        >
                                            <Eye />
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-lg">
                                        <DialogHeader>
                                            <DialogTitle>{{
                                                emailTemplate.subject
                                            }}</DialogTitle>
                                            <DialogDescription>{{
                                                emailTemplate.name
                                            }}</DialogDescription>
                                        </DialogHeader>

                                        <div
                                            class="max-h-96 overflow-y-auto rounded-lg border border-input bg-muted/30 p-4 text-sm [&_a]:text-primary [&_a]:underline [&_a]:underline-offset-2 [&_h2]:mt-3 [&_h2]:text-base [&_h2]:font-semibold [&_h2]:first:mt-0 [&_h3]:mt-3 [&_h3]:font-semibold [&_h3]:first:mt-0 [&_li]:ml-1 [&_ol]:my-2 [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:my-2 [&_p]:first:mt-0 [&_p]:last:mb-0 [&_ul]:my-2 [&_ul]:list-disc [&_ul]:pl-5"
                                            v-html="emailTemplate.body"
                                        />

                                        <DialogFooter>
                                            <DialogClose as-child>
                                                <Button variant="secondary"
                                                    >Close</Button
                                                >
                                            </DialogClose>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>

                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(emailTemplate)"
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
                                                EmailTemplateController.destroy.form(
                                                    emailTemplate,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        emailTemplate.name
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

                    <tr v-if="emailTemplates.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No email templates yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
