<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import SoftCredentialController from '@/actions/App/Http/Controllers/Backend/SoftCredentialController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as credentialsIndex } from '@/routes/credentials';

interface Credential {
    id: number;
    key: string;
    is_configured: boolean;
}

defineProps<{
    credentials: Credential[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Credentials',
                href: credentialsIndex(),
            },
        ],
    },
});

const createOpen = ref(false);
const editingCredential = ref<Credential | null>(null);

function openEdit(credential: Credential) {
    editingCredential.value = credential;
}
</script>

<template>
    <Head title="Credentials" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Credentials"
                description="Secrets used by third-party integrations. Values are encrypted at rest and never shown once saved."
            />

            <Dialog v-model:open="createOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus />
                        Add credential
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="SoftCredentialController.store.form()"
                        reset-on-success
                        @success="createOpen = false"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <DialogHeader>
                            <DialogTitle>Add credential</DialogTitle>
                            <DialogDescription>
                                Create a new integration secret.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="create-key">Key</Label>
                            <Input
                                id="create-key"
                                name="key"
                                placeholder="STRIPE_SECRET_KEY"
                                required
                            />
                            <InputError :message="errors.key" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="create-value">Value</Label>
                            <Input
                                id="create-value"
                                name="value"
                                type="password"
                                autocomplete="off"
                                required
                            />
                            <InputError :message="errors.value" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="processing">
                                Save
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>

        <div
            class="relative overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-4 py-3 font-medium">Key</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="credential in credentials"
                        :key="credential.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3 font-mono text-xs">
                            {{ credential.key }}
                        </td>
                        <td class="px-4 py-3">
                            <Badge
                                :variant="
                                    credential.is_configured
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    credential.is_configured
                                        ? 'Configured'
                                        : 'Not set'
                                }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="openEdit(credential)"
                                >
                                    Edit
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                        >
                                            <Trash2 />
                                            <span class="sr-only"
                                                >Delete</span
                                            >
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                SoftCredentialController.destroy.form(
                                                    { credential: credential.id },
                                                )
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        credential.key
                                                    }}"?</DialogTitle
                                                >
                                                <DialogDescription>
                                                    Any integration relying on
                                                    this credential will stop
                                                    working. This cannot be
                                                    undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button
                                                        variant="secondary"
                                                    >
                                                        Cancel
                                                    </Button>
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
                    <tr v-if="credentials.length === 0">
                        <td
                            colspan="3"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No credentials yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Dialog
            :open="editingCredential !== null"
            @update:open="(open) => !open && (editingCredential = null)"
        >
            <DialogContent v-if="editingCredential">
                <Form
                    v-bind="
                        SoftCredentialController.update.form({
                            credential: editingCredential.id,
                        })
                    "
                    @success="editingCredential = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit credential</DialogTitle>
                        <DialogDescription>
                            Leave the value blank to keep the current secret.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-key">Key</Label>
                        <Input
                            id="edit-key"
                            name="key"
                            :default-value="editingCredential.key"
                            required
                        />
                        <InputError :message="errors.key" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-value">New value</Label>
                        <Input
                            id="edit-value"
                            name="value"
                            type="password"
                            autocomplete="off"
                            placeholder="Leave blank to keep current value"
                        />
                        <InputError :message="errors.value" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing">
                            Save
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>
