<script setup lang="ts">
import { Star } from '@lucide/vue';
import { ref, useId } from 'vue';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';

/**
 * Native radio-group star picker. Five real `<input type="radio">`
 * elements (visually hidden, one per star) carry the field's name/value
 * pairing, so it serializes through Inertia's `<Form>` and gets the
 * browser's built-in radiogroup keyboard behavior for free — the hover
 * state is the only bit of local reactivity layered on top.
 */
const props = withDefaults(
    defineProps<{
        name: string;
        id?: string;
        label?: string;
        defaultValue?: number;
        max?: number;
        error?: string;
    }>(),
    {
        id: undefined,
        label: undefined,
        defaultValue: 5,
        max: 5,
        error: undefined,
    },
);

const fieldId = props.id ?? `rating-${useId()}`;
const checked = ref(props.defaultValue);
const hovered = ref<number | null>(null);

function onChange(event: Event): void {
    checked.value = Number((event.target as HTMLInputElement).value);
}
</script>

<template>
    <div class="grid gap-2">
        <Label v-if="label" :id="fieldId">{{ label }}</Label>

        <div
            class="flex w-fit items-center gap-3 rounded-lg border border-input px-3 py-2"
            @mouseleave="hovered = null"
        >
            <div
                class="flex gap-0.5"
                role="radiogroup"
                :aria-label="label ? undefined : 'Rating'"
                :aria-labelledby="label ? fieldId : undefined"
            >
                <label
                    v-for="star in max"
                    :key="star"
                    class="cursor-pointer rounded-sm p-0.5 transition-transform hover:scale-110 has-focus-visible:ring-[3px] has-focus-visible:ring-ring/50 has-focus-visible:outline-none"
                    :aria-label="`${star} out of ${max} stars`"
                    @mouseenter="hovered = star"
                >
                    <input
                        type="radio"
                        :name="name"
                        :value="star"
                        :checked="star === defaultValue"
                        class="sr-only"
                        @change="onChange"
                    />
                    <Star
                        class="size-6 transition-colors"
                        :class="
                            star <= (hovered ?? checked)
                                ? 'fill-[#FF8904] text-[#FF8904]'
                                : 'text-muted-foreground/30'
                        "
                    />
                </label>
            </div>
            <span
                class="text-sm font-medium text-muted-foreground tabular-nums"
            >
                {{ hovered ?? checked }}/{{ max }}
            </span>
        </div>

        <InputError :message="error" />
    </div>
</template>
