<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { parseISODate, toISODate } from '@/lib/dates';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        /** Selected date as `YYYY-MM-DD`, or empty/undefined for none. */
        modelValue?: string;
        /** Earliest selectable date, `YYYY-MM-DD`. */
        min?: string;
        /** Latest selectable date, `YYYY-MM-DD`. */
        max?: string;
    }>(),
    {
        modelValue: '',
        min: undefined,
        max: undefined,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const weekdayLabels = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
const today = new Date();
const todayIso = toISODate(today);

function startOfMonth(date: Date): Date {
    return new Date(date.getFullYear(), date.getMonth(), 1);
}

function addMonths(date: Date, amount: number): Date {
    return new Date(date.getFullYear(), date.getMonth() + amount, 1);
}

const viewMonth = ref(startOfMonth(parseISODate(props.modelValue) ?? today));

function goToPreviousMonth(): void {
    viewMonth.value = addMonths(viewMonth.value, -1);
}

function goToNextMonth(): void {
    viewMonth.value = addMonths(viewMonth.value, 1);
}

const monthLabel = computed(() =>
    viewMonth.value.toLocaleDateString(undefined, {
        month: 'long',
        year: 'numeric',
    }),
);

type Cell = { iso: string; day: number; outside: boolean };

const cells = computed<Cell[]>(() => {
    const year = viewMonth.value.getFullYear();
    const month = viewMonth.value.getMonth();
    const firstWeekday = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPreviousMonth = new Date(year, month, 0).getDate();

    const result: Cell[] = [];

    for (let i = firstWeekday - 1; i >= 0; i--) {
        const date = new Date(year, month - 1, daysInPreviousMonth - i);
        result.push({
            iso: toISODate(date),
            day: date.getDate(),
            outside: true,
        });
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        result.push({ iso: toISODate(date), day, outside: false });
    }

    let trailing = 1;

    while (result.length < 42) {
        const date = new Date(year, month + 1, trailing++);
        result.push({
            iso: toISODate(date),
            day: date.getDate(),
            outside: true,
        });
    }

    return result;
});

function isDisabled(iso: string): boolean {
    return (
        (props.min !== undefined && iso < props.min) ||
        (props.max !== undefined && iso > props.max)
    );
}

function selectCell(cell: Cell): void {
    if (isDisabled(cell.iso)) {
        return;
    }

    emit('update:modelValue', cell.iso);
}
</script>

<template>
    <div class="w-64">
        <div class="flex items-center justify-between pb-3">
            <Button
                type="button"
                variant="outline"
                size="icon-sm"
                aria-label="Previous month"
                @click="goToPreviousMonth"
            >
                <ChevronLeft />
            </Button>
            <span class="text-sm font-medium">{{ monthLabel }}</span>
            <Button
                type="button"
                variant="outline"
                size="icon-sm"
                aria-label="Next month"
                @click="goToNextMonth"
            >
                <ChevronRight />
            </Button>
        </div>

        <div class="grid grid-cols-7 gap-1">
            <span
                v-for="label in weekdayLabels"
                :key="label"
                class="flex h-8 items-center justify-center text-xs font-medium text-muted-foreground"
            >
                {{ label }}
            </span>
        </div>

        <div class="grid grid-cols-7 gap-1">
            <button
                v-for="cell in cells"
                :key="cell.iso"
                type="button"
                :disabled="isDisabled(cell.iso)"
                :aria-pressed="cell.iso === modelValue"
                :class="
                    cn(
                        'flex size-8 items-center justify-center rounded-md text-sm transition-colors outline-none',
                        'focus-visible:ring-[3px] focus-visible:ring-ring/50',
                        'disabled:pointer-events-none disabled:opacity-30',
                        cell.outside && 'text-muted-foreground/50',
                        cell.iso === modelValue
                            ? 'bg-primary font-semibold text-primary-foreground hover:bg-primary'
                            : 'hover:bg-accent hover:text-accent-foreground',
                        cell.iso === todayIso &&
                            cell.iso !== modelValue &&
                            'font-semibold text-foreground ring-1 ring-border ring-inset',
                    )
                "
                @click="selectCell(cell)"
            >
                {{ cell.day }}
            </button>
        </div>
    </div>
</template>
