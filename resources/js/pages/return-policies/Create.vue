<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ReturnPolicyController from '@/actions/App/Http/Controllers/Backend/ReturnPolicyController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/return-policies';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Return policies',
                href: index(),
            },
            {
                title: 'Add return policy',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add return policy" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add return policy"
            description="Create a new return policy"
        />

        <Form
            v-bind="ReturnPolicyController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    required
                    autofocus
                    placeholder="Standard Returns"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    name="description"
                    required
                    placeholder="Explain the return policy"
                />
                <InputError :message="errors.description" />
            </div>

            <div class="grid gap-2">
                <Label for="days_allowed">Days allowed</Label>
                <Input
                    id="days_allowed"
                    name="days_allowed"
                    type="number"
                    min="0"
                    max="365"
                    default-value="30"
                />
                <InputError :message="errors.days_allowed" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create return policy</Button>
            </div>
        </Form>
    </div>
</template>
