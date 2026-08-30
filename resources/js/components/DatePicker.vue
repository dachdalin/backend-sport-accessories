<script setup lang="ts">
import { Calendar as CalendarIcon, X } from '@lucide/vue';
import { computed, ref, useId, watch } from 'vue';
import Calendar from '@/components/Calendar.vue';
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { formatDisplayDate, todayISO } from '@/lib/dates';
import { cn } from '@/lib/utils';

/**
 * Global date field. A styled trigger button + calendar popover own the
 * picking UI; a hidden native input mirrors the value so it still
 * serializes through Inertia's `<Form>` like any other named field.
 */
const props = withDefaults(
    defineProps<{
        /** Field name submitted with the form, e.g. "published_at". */
        name: string;
        id?: string;
        /** Initial value as `YYYY-MM-DD`, for uncontrolled (Inertia `<Form>`) usage. */
        defaultValue?: string;
        /** Controlled value as `YYYY-MM-DD`, for `v-model` usage. */
        modelValue?: string;
        placeholder?: string;
        required?: boolean;
        disabled?: boolean;
        /** Earliest selectable date, `YYYY-MM-DD`. */
        min?: string;
        /** Latest selectable date, `YYYY-MM-DD`. */
        max?: string;
        class?: string;
    }>(),
    {
        id: undefined,
        defaultValue: '',
        modelValue: undefined,
        placeholder: 'Pick a date',
        required: false,
        disabled: false,
        min: undefined,
        max: undefined,
        class: undefined,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const fieldId = props.id ?? `date-picker-${useId()}`;
const open = ref(false);
const innerValue = ref(props.modelValue ?? props.defaultValue);

watch(
    () => props.modelValue,
    (value) => {
        if (value !== undefined) {
            innerValue.value = value;
        }
    },
);

const displayLabel = computed(() => formatDisplayDate(innerValue.value));
const canClear = computed(
    () => !props.required && !props.disabled && innerValue.value !== '',
);

function selectDate(value: string): void {
    innerValue.value = value;
    emit('update:modelValue', value);
    open.value = false;
}

function selectToday(): void {
    selectDate(todayISO());
}

function clear(): void {
    innerValue.value = '';
    emit('update:modelValue', '');
    open.value = false;
}
</script>

<template>
    <div class="relative">
        <input
            :id="fieldId"
            type="hidden"
            :name="name"
            :value="innerValue"
            :required="required"
        />

        <Popover v-model:open="open">
            <PopoverTrigger as-child>
                <button
                    type="button"
                    :disabled="disabled"
                    :class="
                        cn(
                            'flex h-9 w-full items-center gap-2 rounded-md border border-input bg-transparent px-3 py-1 text-left text-base shadow-xs transition-[color,box-shadow] outline-none dark:bg-input/30',
                            'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                            'aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40',
                            'disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                            canClear && 'pr-8',
                            props.class,
                        )
                    "
                >
                    <CalendarIcon
                        class="size-4 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <span
                        :class="
                            cn(
                                'flex-1 truncate',
                                !displayLabel && 'text-muted-foreground',
                            )
                        "
                    >
                        {{ displayLabel ?? placeholder }}
                    </span>
                </button>
            </PopoverTrigger>

            <PopoverContent class="w-auto p-3" align="start">
                <Calendar
                    :model-value="innerValue"
                    :min="min"
                    :max="max"
                    @update:model-value="selectDate"
                />
                <div
                    class="mt-3 flex items-center justify-between border-t pt-3"
                >
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="selectToday"
                    >
                        Today
                    </Button>
                    <Button
                        v-if="!required && innerValue !== ''"
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="clear"
                    >
                        Clear
                    </Button>
                </div>
            </PopoverContent>
        </Popover>

        <button
            v-if="canClear"
            type="button"
            class="absolute top-1/2 right-2 -translate-y-1/2 rounded-sm p-0.5 text-muted-foreground transition-colors hover:text-foreground"
            aria-label="Clear date"
            @click="clear"
        >
            <X class="size-3.5" />
        </button>
    </div>
</template>
