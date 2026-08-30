<script setup lang="ts">
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { expandHex } from '@/lib/colors';

const props = withDefaults(
    defineProps<{
        id: string;
        name: string;
        label: string;
        placeholder?: string;
        error?: string;
    }>(),
    {
        placeholder: '#ffffff',
        error: undefined,
    },
);

const value = defineModel<string>({ default: '' });

/** Native color inputs require a valid `#rrggbb` value; fall back to a neutral swatch while the typed text isn't one yet. */
const swatchValue = computed(() => expandHex(value.value) ?? '#94a3b8');

function onSwatchInput(event: Event) {
    value.value = (event.target as HTMLInputElement).value;
}
</script>

<template>
    <div class="grid gap-2">
        <Label :for="props.id">{{ props.label }}</Label>
        <div class="flex items-center gap-2">
            <label
                :for="`${props.id}-swatch`"
                class="relative size-9 shrink-0 cursor-pointer overflow-hidden rounded-md border border-input shadow-xs"
                :style="{ backgroundColor: swatchValue }"
            >
                <span class="sr-only"
                    >Pick {{ props.label.toLowerCase() }}</span
                >
                <input
                    :id="`${props.id}-swatch`"
                    type="color"
                    tabindex="-1"
                    class="absolute inset-0 size-full cursor-pointer opacity-0"
                    :value="swatchValue"
                    @input="onSwatchInput"
                />
            </label>
            <Input
                :id="props.id"
                v-model="value"
                :name="props.name"
                :placeholder="props.placeholder"
                class="font-mono uppercase"
                maxlength="7"
                autocomplete="off"
            />
        </div>
        <InputError :message="props.error" />
    </div>
</template>
