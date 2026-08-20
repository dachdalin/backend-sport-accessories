<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import AnalyticScriptController from '@/actions/App/Http/Controllers/Backend/AnalyticScriptController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { create, index } from '@/routes/analytic-scripts';

interface TypeOption {
    value: string;
    label: string;
}

defineProps<{
    types: TypeOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Analytic scripts',
                href: index(),
            },
            {
                title: 'Add script',
                href: create(),
            },
        ],
    },
});

const textareaClass =
    'placeholder:text-muted-foreground dark:bg-input/30 border-input min-h-32 w-full rounded-md border bg-transparent px-3 py-2 font-mono text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]';
</script>

<template>
    <Head title="Add script" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add script"
            description="Add a new tracking or marketing pixel script"
        />

        <Form
            v-bind="AnalyticScriptController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    autofocus
                    placeholder="Google Analytics 4"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="type">Type</Label>
                <Select name="type" default-value="custom">
                    <SelectTrigger id="type" class="w-full">
                        <SelectValue placeholder="Select a type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in types"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.type" />
            </div>

            <div class="grid gap-2">
                <Label for="script_id">Script ID</Label>
                <Input
                    id="script_id"
                    name="script_id"
                    placeholder="G-XXXXXXXXXX"
                />
                <InputError :message="errors.script_id" />
            </div>

            <div class="grid gap-2">
                <Label for="script">Script</Label>
                <textarea
                    id="script"
                    name="script"
                    required
                    :class="textareaClass"
                    placeholder="<script>...</script>"
                />
                <InputError :message="errors.script" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create script</Button>
            </div>
        </Form>
    </div>
</template>
