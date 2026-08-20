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
import { edit, index } from '@/routes/withdrawal-methods';

type WithdrawalMethod = {
    id: number;
    method_name: string;
    method_fields: string;
    is_default: boolean;
    status: boolean;
};

const props = defineProps<{
    withdrawalMethod: WithdrawalMethod;
}>();

defineOptions({
    layout: (pageProps: { withdrawalMethod: WithdrawalMethod }) => ({
        breadcrumbs: [
            {
                title: 'Withdrawal methods',
                href: index(),
            },
            {
                title: 'Edit withdrawal method',
                href: edit(pageProps.withdrawalMethod),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit withdrawal method" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit withdrawal method"
            :description="`Update the details for ${props.withdrawalMethod.method_name}`"
        />

        <Form
            v-bind="
                WithdrawalMethodController.update.form(
                    props.withdrawalMethod,
                )
            "
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
                    :default-value="props.withdrawalMethod.method_name"
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
                    :default-value="props.withdrawalMethod.method_fields"
                />
                <InputError :message="errors.method_fields" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="is_default"
                    name="is_default"
                    :default-value="props.withdrawalMethod.is_default"
                />
                <Label for="is_default">Default method</Label>
                <InputError :message="errors.is_default" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.withdrawalMethod.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save withdrawal method</Button>
            </div>
        </Form>
    </div>
</template>
