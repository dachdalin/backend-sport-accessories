<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
// computed (not a plain const) because this layout persists across Inertia
// visits — a plain destructure would freeze the logo/name at first mount.
const name = computed(() => page.props.name);
const logoUrl = computed(() => page.props.logoUrl);
</script>

<template>
    <div
        v-if="logoUrl"
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-white ring-1 ring-sidebar-border"
    >
        <img :src="logoUrl" :alt="`${name} logo`" class="size-full rounded-md object-contain p-0.5" />
    </div>
    <div
        v-else
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{
            name
        }}</span>
    </div>
</template>
