<script setup lang="ts">
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

export type DashboardFilter = 'today' | 'this_week' | 'this_month' | 'this_year' | 'custom';

const props = defineProps<{
    modelValue: DashboardFilter;
    from: string | null;
    to: string | null;
}>();

const emit = defineEmits<{
    apply: [filter: DashboardFilter, from: string | null, to: string | null];
}>();

const options: { value: DashboardFilter; label: string }[] = [
    { value: 'today', label: 'Today' },
    { value: 'this_week', label: 'This week' },
    { value: 'this_month', label: 'This month' },
    { value: 'this_year', label: 'This year' },
    { value: 'custom', label: 'Custom' },
];

const today = new Date().toISOString().slice(0, 10);
const customFrom = ref(props.from ?? today);
const customTo = ref(props.to ?? today);

watch(
    () => [props.from, props.to],
    ([from, to]) => {
        customFrom.value = from ?? today;
        customTo.value = to ?? today;
    },
);

function select(filter: DashboardFilter) {
    if (filter === 'custom') {
        emit('apply', filter, customFrom.value, customTo.value);

        return;
    }

    emit('apply', filter, null, null);
}

function applyCustomRange() {
    emit('apply', 'custom', customFrom.value, customTo.value);
}
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <div
            class="inline-flex items-center gap-1 rounded-lg border border-sidebar-border/70 bg-card p-1 dark:border-sidebar-border"
        >
            <Button
                v-for="option in options"
                :key="option.value"
                type="button"
                size="sm"
                :variant="modelValue === option.value ? 'default' : 'ghost'"
                :class="cn('h-8 px-3 text-sm', modelValue !== option.value && 'text-muted-foreground')"
                @click="select(option.value)"
            >
                {{ option.label }}
            </Button>
        </div>

        <div v-if="modelValue === 'custom'" class="flex items-center gap-2">
            <Input
                v-model="customFrom"
                type="date"
                :max="customTo"
                class="h-8 w-36"
                aria-label="Start date"
            />
            <span class="text-sm text-muted-foreground">to</span>
            <Input
                v-model="customTo"
                type="date"
                :min="customFrom"
                :max="today"
                class="h-8 w-36"
                aria-label="End date"
            />
            <Button type="button" size="sm" class="h-8" @click="applyCustomRange">
                Apply
            </Button>
        </div>
    </div>
</template>
