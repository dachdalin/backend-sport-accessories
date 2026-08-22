<script setup lang="ts">
import { Check, Languages } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useLocale } from '@/composables/useLocale';
import type { Locale } from '@/types/ui';

const { locale, setLocale } = useLocale();

const options: Array<{ value: Locale; name: string; abbr: string; khmerFont?: boolean }> = [
    { value: 'en', name: 'English', abbr: 'EN' },
    { value: 'km', name: 'ខ្មែរ', abbr: 'KM', khmerFont: true },
];

const current = computed(
    () => options.find((option) => option.value === locale.value) ?? options[0],
);
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="group h-9 w-9 cursor-pointer"
                :aria-label="`Language: ${current.name}`"
            >
                <Languages class="size-5 opacity-80 group-hover:opacity-100" />
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" :side-offset="8" class="w-44">
            <DropdownMenuItem
                v-for="option in options"
                :key="option.value"
                class="cursor-pointer justify-between"
                @select="setLocale(option.value)"
            >
                <span :class="option.khmerFont ? 'font-khmer' : undefined">
                    {{ option.name }}
                </span>
                <Check v-if="option.value === locale" class="size-4 text-sidebar-primary" />
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
