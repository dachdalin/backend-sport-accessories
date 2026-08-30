<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import TeamMemberController from '@/actions/App/Http/Controllers/Backend/TeamMemberController';
import Heading from '@/components/Heading.vue';
import ImageDropzone from '@/components/ImageDropzone.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/team-members';

type TeamMember = {
    id: number;
    name: string;
    role: string;
    bio: string | null;
    photo: string;
    photo_alt_text: string | null;
    sort_order: number;
    status: boolean;
};

const props = defineProps<{
    teamMember: TeamMember;
}>();

defineOptions({
    layout: (pageProps: { teamMember: TeamMember }) => ({
        breadcrumbs: [
            {
                title: 'Team members',
                href: index(),
            },
            {
                title: 'Edit team member',
                href: edit(pageProps.teamMember),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit team member" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit team member"
            :description="`Update the details for ${props.teamMember.name}`"
        />

        <Form
            v-bind="TeamMemberController.update.form(props.teamMember)"
            class="max-w-2xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        name="name"
                        required
                        autofocus
                        :default-value="props.teamMember.name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="role">Role</Label>
                    <Input
                        id="role"
                        name="role"
                        required
                        :default-value="props.teamMember.role"
                    />
                    <InputError :message="errors.role" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="bio">Bio</Label>
                <Textarea
                    id="bio"
                    name="bio"
                    rows="4"
                    :default-value="props.teamMember.bio ?? ''"
                    placeholder="Short biography"
                />
                <InputError :message="errors.bio" />
            </div>

            <ImageDropzone
                name="photo"
                label="Replace photo"
                hint="PNG, JPG or WEBP, up to 2MB. Leave empty to keep the current photo."
                :max-size-mb="2"
                :error="errors.photo"
                :processing="processing"
                :initial-previews="[`/storage/${props.teamMember.photo}`]"
            />

            <div class="grid gap-2">
                <Label for="photo_alt_text">Photo alt text</Label>
                <Input
                    id="photo_alt_text"
                    name="photo_alt_text"
                    :default-value="props.teamMember.photo_alt_text ?? ''"
                    placeholder="Describes the photo"
                />
                <InputError :message="errors.photo_alt_text" />
            </div>

            <div class="grid gap-2">
                <Label for="sort_order">Sort order</Label>
                <Input
                    id="sort_order"
                    name="sort_order"
                    type="number"
                    min="0"
                    :default-value="props.teamMember.sort_order"
                />
                <InputError :message="errors.sort_order" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.teamMember.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <Button class="w-full sm:w-auto" :disabled="processing">
                    <Spinner v-if="processing" />
                    Save team member
                </Button>
                <Button variant="outline" as-child class="w-full sm:w-auto">
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
