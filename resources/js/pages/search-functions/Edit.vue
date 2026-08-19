<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SearchFunctionController from '@/actions/App/Http/Controllers/Backend/SearchFunctionController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { edit, index } from '@/routes/search-functions';

interface VisibilityOption {
    value: string;
    label: string;
}

type SearchFunction = {
    id: number;
    key: string;
    url: string;
    visible_for: string;
};

const props = defineProps<{
    searchFunction: SearchFunction;
    visibilities: VisibilityOption[];
}>();

defineOptions({
    layout: (pageProps: { searchFunction: SearchFunction }) => ({
        breadcrumbs: [
            {
                title: 'Search functions',
                href: index(),
            },
            {
                title: 'Edit search function',
                href: edit(pageProps.searchFunction),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit search function" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit search function"
            :description="`Update the details for &quot;${searchFunction.key}&quot;`"
        />

        <Form
            v-bind="SearchFunctionController.update.form(searchFunction)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="key">Key</Label>
                <Input
                    id="key"
                    name="key"
                    required
                    autofocus
                    :default-value="searchFunction.key"
                />
                <InputError :message="errors.key" />
            </div>

            <div class="grid gap-2">
                <Label for="url">URL</Label>
                <Input
                    id="url"
                    name="url"
                    required
                    :default-value="searchFunction.url"
                />
                <InputError :message="errors.url" />
            </div>

            <div class="grid gap-2">
                <Label for="visible_for">Visible for</Label>
                <Select
                    name="visible_for"
                    :default-value="searchFunction.visible_for"
                >
                    <SelectTrigger id="visible_for" class="w-full">
                        <SelectValue placeholder="Select an audience" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in props.visibilities"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.visible_for" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save search function</Button>
            </div>
        </Form>
    </div>
</template>
