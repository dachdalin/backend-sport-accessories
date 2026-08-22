<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Lock, Pencil, Plus, ShieldCheck, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import RoleController from '@/actions/App/Http/Controllers/Backend/RoleController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { index as rolesIndex } from '@/routes/roles';

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
    permissions_count: number;
}

const props = defineProps<{
    roles: Role[];
    permissions: Permission[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Roles',
                href: rolesIndex(),
            },
        ],
    },
});

const PROTECTED_ROLE = 'admin';

const createOpen = ref(false);
const editingRole = ref<Role | null>(null);

function openEdit(role: Role) {
    editingRole.value = role;
}

function hasPermission(role: Role, permissionId: number) {
    return role.permissions.some((permission) => permission.id === permissionId);
}
</script>

<template>
    <Head title="Roles" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Roles"
                description="Control what admins can do by grouping permissions into roles"
            />

            <Dialog v-model:open="createOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus />
                        Add role
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-lg">
                    <Form
                        v-bind="RoleController.store.form()"
                        reset-on-success
                        @success="createOpen = false"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <DialogHeader>
                            <DialogTitle>Add role</DialogTitle>
                            <DialogDescription>
                                Name the role, then choose what it can do.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="create-name">Name</Label>
                            <Input
                                id="create-name"
                                name="name"
                                placeholder="Editor"
                                required
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Permissions</Label>
                            <div
                                v-if="props.permissions.length > 0"
                                class="grid max-h-56 grid-cols-1 gap-2 overflow-y-auto rounded-lg border p-3 sm:grid-cols-2"
                            >
                                <div
                                    v-for="permission in props.permissions"
                                    :key="permission.id"
                                    class="flex items-center gap-2"
                                >
                                    <Checkbox
                                        :id="`create-permission-${permission.id}`"
                                        name="permissions[]"
                                        :value="permission.id"
                                    />
                                    <Label
                                        :for="`create-permission-${permission.id}`"
                                        class="font-normal"
                                    >
                                        {{ permission.name }}
                                    </Label>
                                </div>
                            </div>
                            <p
                                v-else
                                class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
                            >
                                No permissions defined yet. This role can still be created and assigned to admins.
                            </p>
                            <InputError :message="errors.permissions" />
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
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Permissions</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="role in roles"
                        :key="role.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 font-medium">
                                <ShieldCheck class="size-4 text-muted-foreground" />
                                {{ role.name }}
                                <Lock
                                    v-if="role.name === PROTECTED_ROLE"
                                    class="size-3.5 text-muted-foreground"
                                />
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <Badge v-if="role.permissions_count > 0" variant="secondary">
                                {{ role.permissions_count }}
                                {{ role.permissions_count === 1 ? 'permission' : 'permissions' }}
                            </Badge>
                            <span v-else class="text-muted-foreground">No permissions</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(role)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog v-if="role.name !== PROTECTED_ROLE">
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                        >
                                            <Trash2 />
                                            <span class="sr-only">Delete</span>
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                RoleController.destroy.form({
                                                    role: role.id,
                                                })
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete
                                                    "{{ role.name }}"?</DialogTitle
                                                >
                                                <DialogDescription>
                                                    Admins with this role lose its permissions immediately. This cannot be undone.
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
                                <Button
                                    v-else
                                    variant="outline"
                                    size="icon-sm"
                                    disabled
                                    title="The admin role is protected and cannot be deleted."
                                >
                                    <Lock />
                                    <span class="sr-only">Protected</span>
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="roles.length === 0">
                        <td
                            colspan="3"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No roles yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Dialog
            :open="editingRole !== null"
            @update:open="(open) => !open && (editingRole = null)"
        >
            <DialogContent v-if="editingRole" class="sm:max-w-lg">
                <Form
                    v-bind="
                        RoleController.update.form({
                            role: editingRole.id,
                        })
                    "
                    @success="editingRole = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit role</DialogTitle>
                        <DialogDescription>
                            Update the role's name and permissions.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-name">Name</Label>
                        <Input
                            id="edit-name"
                            name="name"
                            :default-value="editingRole.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Permissions</Label>
                        <div
                            v-if="props.permissions.length > 0"
                            class="grid max-h-56 grid-cols-1 gap-2 overflow-y-auto rounded-lg border p-3 sm:grid-cols-2"
                        >
                            <div
                                v-for="permission in props.permissions"
                                :key="permission.id"
                                class="flex items-center gap-2"
                            >
                                <Checkbox
                                    :id="`edit-permission-${permission.id}`"
                                    name="permissions[]"
                                    :value="permission.id"
                                    :default-value="hasPermission(editingRole, permission.id)"
                                />
                                <Label
                                    :for="`edit-permission-${permission.id}`"
                                    class="font-normal"
                                >
                                    {{ permission.name }}
                                </Label>
                            </div>
                        </div>
                        <p
                            v-else
                            class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
                        >
                            No permissions defined yet. This role can still be created and assigned to admins.
                        </p>
                        <InputError :message="errors.permissions" />
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
