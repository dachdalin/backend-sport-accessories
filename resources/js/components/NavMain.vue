<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavGroup } from '@/types';

defineProps<{
    groups: NavGroup[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup
        v-for="(group, index) in groups"
        :key="group.label"
        class="px-2 py-0"
        :class="index > 0 ? 'mt-3 border-t border-sidebar-border pt-3' : ''"
    >
        <SidebarGroupLabel
            class="text-[11px] font-semibold tracking-[0.08em] text-sidebar-foreground/45 uppercase"
        >
            {{ group.label }}
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in group.items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="hover:bg-sidebar-accent hover:text-sidebar-accent-foreground data-[active=true]:bg-[#FF8904] data-[active=true]:font-semibold data-[active=true]:text-[#2D1E06] data-[active=true]:shadow-[0_1px_2px_rgba(0,0,0,0.25)] data-[active=true]:hover:bg-[#FF8904] data-[active=true]:hover:text-[#2D1E06] [&>svg]:text-sidebar-foreground/50 data-[active=true]:[&>svg]:text-[#2D1E06]"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
