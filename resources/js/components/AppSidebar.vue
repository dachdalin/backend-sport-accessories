<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Coins,
    FolderGit2,
    FolderTree,
    GalleryHorizontal,
    HelpCircle,
    KeyRound,
    LayoutGrid,
    MessageCircleQuestion,
    MessageSquareQuote,
    Palette,
    Ruler,
    Search,
    Share2,
    Tag,
    Tags,
    Truck,
    Wallet,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as bannersIndex } from '@/routes/banners';
import { index as faqsIndex } from '@/routes/faqs';
import { index as brandsIndex } from '@/routes/brands';
import { index as categoriesIndex } from '@/routes/categories';
import { index as colorsIndex } from '@/routes/colors';
import { index as credentialsIndex } from '@/routes/credentials';
import { index as currenciesIndex } from '@/routes/currencies';
import { index as helpTopicsIndex } from '@/routes/help-topics';
import { index as offlinePaymentMethodsIndex } from '@/routes/offline-payment-methods';
import { index as searchFunctionsIndex } from '@/routes/search-functions';
import { index as shippingMethodsIndex } from '@/routes/shipping-methods';
import { index as sizesIndex } from '@/routes/sizes';
import { index as socialMediasIndex } from '@/routes/social-medias';
import { index as tagsIndex } from '@/routes/tags';
import { index as testimonialsIndex } from '@/routes/testimonials';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Colors',
        href: colorsIndex(),
        icon: Palette,
    },
    {
        title: 'Brands',
        href: brandsIndex(),
        icon: Tag,
    },
    {
        title: 'Sizes',
        href: sizesIndex(),
        icon: Ruler,
    },
    {
        title: 'Tags',
        href: tagsIndex(),
        icon: Tags,
    },
    {
        title: 'Currencies',
        href: currenciesIndex(),
        icon: Coins,
    },
    {
        title: 'Categories',
        href: categoriesIndex(),
        icon: FolderTree,
    },
    {
        title: 'Help topics',
        href: helpTopicsIndex(),
        icon: HelpCircle,
    },
    {
        title: 'Shipping methods',
        href: shippingMethodsIndex(),
        icon: Truck,
    },
    {
        title: 'Search functions',
        href: searchFunctionsIndex(),
        icon: Search,
    },
    {
        title: 'Offline payment methods',
        href: offlinePaymentMethodsIndex(),
        icon: Wallet,
    },
    {
        title: 'Social media',
        href: socialMediasIndex(),
        icon: Share2,
    },
    {
        title: 'Credentials',
        href: credentialsIndex(),
        icon: KeyRound,
    },
    {
        title: 'Banners',
        href: bannersIndex(),
        icon: GalleryHorizontal,
    },
    {
        title: 'Testimonials',
        href: testimonialsIndex(),
        icon: MessageSquareQuote,
    },
    {
        title: 'FAQs',
        href: faqsIndex(),
        icon: MessageCircleQuestion,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
