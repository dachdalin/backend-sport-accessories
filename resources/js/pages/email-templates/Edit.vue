<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, FileText, Mail, Rocket } from '@lucide/vue';
import EmailTemplateController from '@/actions/App/Http/Controllers/Backend/EmailTemplateController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
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
import { Spinner } from '@/components/ui/spinner';
import { edit, index } from '@/routes/email-templates';

type EmailTemplate = {
    id: number;
    name: string;
    subject: string;
    body: string;
    status: boolean;
};

const props = defineProps<{
    emailTemplate: EmailTemplate;
}>();

defineOptions({
    layout: (pageProps: { emailTemplate: EmailTemplate }) => ({
        breadcrumbs: [
            {
                title: 'Email templates',
                href: index(),
            },
            {
                title: 'Edit email template',
                href: edit(pageProps.emailTemplate),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit email template" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit email template"
            :description="`Update the details for ${props.emailTemplate.name}`"
        />

        <Form
            v-bind="EmailTemplateController.update.form(props.emailTemplate)"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Mail
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Template details</CardTitle>
                        </div>
                        <CardDescription>
                            An internal name to find this template, and the
                            subject line customers see.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                name="name"
                                required
                                autofocus
                                :default-value="props.emailTemplate.name"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="subject">Subject</Label>
                            <Input
                                id="subject"
                                name="subject"
                                required
                                :default-value="props.emailTemplate.subject"
                            />
                            <InputError :message="errors.subject" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <FileText
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Content</CardTitle>
                        </div>
                        <CardDescription>
                            What the customer reads in the email body.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <RichTextEditor
                            name="body"
                            required
                            :default-value="props.emailTemplate.body"
                            :error="errors.body"
                        />
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Eye
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Status</CardTitle>
                        </div>
                        <CardDescription>
                            Whether this template can be sent.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <label
                            for="status"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                :default-value="props.emailTemplate.status"
                            />
                            <span class="text-sm font-medium">Active</span>
                        </label>
                        <InputError :message="errors.status" />
                    </CardContent>
                </Card>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Rocket
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Publish</CardTitle>
                        </div>
                        <CardDescription>
                            Save your changes to this template.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save email template
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>
