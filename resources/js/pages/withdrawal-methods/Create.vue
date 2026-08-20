<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import WithdrawalMethodController from '@/actions/App/Http/Controllers/Backend/WithdrawalMethodController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/withdrawal-methods';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Withdrawal methods',
                href: index(),
            },
            {
                title: 'Add withdrawal method',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add withdrawal method" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add withdrawal method"
            description="Create a new way for vendors to withdraw earnings"
        />

        <Form
            v-bind="WithdrawalMethodController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="method_name">Name</Label>
                <Input
                    id="method_name"
                    name="method_name"
                    required
                    autofocus
                    placeholder="Bank Transfer"
                />
                <InputError :message="errors.method_name" />
            </div>

            <div class="grid gap-2">
                <Label for="method_fields">Fields vendors must fill in</Label>
                <Textarea
                    id="method_fields"
                    name="method_fields"
                    required
                    rows="4"
                    placeholder="Account name, account number, bank name"
                />
                <InputError :message="errors.method_fields" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="is_default"
                    name="is_default"
                    :default-value="false"
                />
                <Label for="is_default">Default method</Label>
                <InputError :message="errors.is_default" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing"
                    >Create withdrawal method</Button
                >
            </div>
        </Form>
    </div>
</template>
