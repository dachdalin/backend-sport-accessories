<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import OfflinePaymentMethodController from '@/actions/App/Http/Controllers/Backend/OfflinePaymentMethodController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/offline-payment-methods';

type OfflinePaymentMethod = {
    id: number;
    method_name: string;
    method_fields: string;
    method_informations: string;
    status: boolean;
};

defineProps<{
    offlinePaymentMethod: OfflinePaymentMethod;
}>();

defineOptions({
    layout: (pageProps: { offlinePaymentMethod: OfflinePaymentMethod }) => ({
        breadcrumbs: [
            {
                title: 'Offline payment methods',
                href: index(),
            },
            {
                title: 'Edit payment method',
                href: edit(pageProps.offlinePaymentMethod),
            },
        ],
    }),
});

const textareaClass =
    'placeholder:text-muted-foreground dark:bg-input/30 border-input min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] md:text-sm';
</script>

<template>
    <Head title="Edit payment method" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit payment method"
            :description="`Update the details for ${offlinePaymentMethod.method_name}`"
        />

        <Form
            v-bind="
                OfflinePaymentMethodController.update.form(
                    offlinePaymentMethod,
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
                    :default-value="offlinePaymentMethod.method_name"
                />
                <InputError :message="errors.method_name" />
            </div>

            <div class="grid gap-2">
                <Label for="method_fields"
                    >Fields customers must fill in</Label
                >
                <textarea
                    id="method_fields"
                    name="method_fields"
                    required
                    :class="textareaClass"
                    >{{ offlinePaymentMethod.method_fields }}</textarea
                >
                <InputError :message="errors.method_fields" />
            </div>

            <div class="grid gap-2">
                <Label for="method_informations"
                    >Instructions shown to customers</Label
                >
                <textarea
                    id="method_informations"
                    name="method_informations"
                    required
                    :class="textareaClass"
                    >{{
                        offlinePaymentMethod.method_informations
                    }}</textarea
                >
                <InputError :message="errors.method_informations" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="offlinePaymentMethod.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save payment method</Button>
            </div>
        </Form>
    </div>
</template>
