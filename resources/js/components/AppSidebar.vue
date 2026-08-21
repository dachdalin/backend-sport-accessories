<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Award,
    BarChart3,
    BookOpen,
    Briefcase,
    Coins,
    Factory,
    FileText,
    FolderGit2,
    FolderTree,
    GalleryHorizontal,
    Gift,
    Heart,
    HelpCircle,
    Inbox,
    KeyRound,
    Layers,
    LayoutGrid,
    LifeBuoy,
    Mail,
    MailOpen,
    MapPinned,
    MessageCircleQuestion,
    MessageSquareQuote,
    Newspaper,
    Palette,
    Percent,
    Receipt,
    Ruler,
    RotateCcw,
    ScrollText,
    Search,
    Share2,
    ShoppingBag,
    SlidersHorizontal,
    Sparkles,
    Star,
    Store,
    Tag,
    Tags,
    Ticket,
    Truck,
    UserRound,
    Users,
    Wallet,
    Warehouse,
    Zap,
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
import { index as analyticScriptsIndex } from '@/routes/analytic-scripts';
import { index as attributesIndex } from '@/routes/attributes';
import { index as bannersIndex } from '@/routes/banners';
import { index as faqsIndex } from '@/routes/faqs';
import { index as blogCategoriesIndex } from '@/routes/blog-categories';
import { index as blogsIndex } from '@/routes/blogs';
import { index as flashDealsIndex } from '@/routes/flash-deals';
import { index as brandsIndex } from '@/routes/brands';
import { index as categoriesIndex } from '@/routes/categories';
import { index as colorsIndex } from '@/routes/colors';
import { index as contactsIndex } from '@/routes/contacts';
import { index as couponsIndex } from '@/routes/coupons';
import { index as credentialsIndex } from '@/routes/credentials';
import { index as currenciesIndex } from '@/routes/currencies';
import { index as customersIndex } from '@/routes/customers';
import { index as deliveryZonesIndex } from '@/routes/delivery-zones';
import { index as emailTemplatesIndex } from '@/routes/email-templates';
import { index as giftCardsIndex } from '@/routes/gift-cards';
import { index as helpTopicsIndex } from '@/routes/help-topics';
import { index as jobOpeningsIndex } from '@/routes/job-openings';
import { index as loyaltyTiersIndex } from '@/routes/loyalty-tiers';
import { index as materialsIndex } from '@/routes/materials';
import { index as newsletterSubscribersIndex } from '@/routes/newsletter-subscribers';
import { index as offlinePaymentMethodsIndex } from '@/routes/offline-payment-methods';
import { index as ordersIndex } from '@/routes/orders';
import { index as pagesIndex } from '@/routes/pages';
import { index as productsIndex } from '@/routes/products';
import { index as returnPoliciesIndex } from '@/routes/return-policies';
import { index as reviewsIndex } from '@/routes/reviews';
import { index as searchFunctionsIndex } from '@/routes/search-functions';
import { index as shippingMethodsIndex } from '@/routes/shipping-methods';
import { index as sizesIndex } from '@/routes/sizes';
import { index as socialMediasIndex } from '@/routes/social-medias';
import { index as stockClearanceSetupsIndex } from '@/routes/stock-clearance-setups';
import { index as storeLocationsIndex } from '@/routes/store-locations';
import { index as suppliersIndex } from '@/routes/suppliers';
import { index as supportTicketsIndex } from '@/routes/support-tickets';
import { index as tagsIndex } from '@/routes/tags';
import { index as taxRatesIndex } from '@/routes/tax-rates';
import { index as teamMembersIndex } from '@/routes/team-members';
import { index as testimonialsIndex } from '@/routes/testimonials';
import { index as warehousesIndex } from '@/routes/warehouses';
import { index as wishlistsIndex } from '@/routes/wishlists';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Products',
        href: productsIndex(),
        icon: ShoppingBag,
    },
    {
        title: 'Orders',
        href: ordersIndex(),
        icon: Receipt,
    },
    {
        title: 'Flash deals',
        href: flashDealsIndex(),
        icon: Zap,
    },
    {
        title: 'Reviews',
        href: reviewsIndex(),
        icon: Star,
    },
    {
        title: 'Wishlists',
        href: wishlistsIndex(),
        icon: Heart,
    },
    {
        title: 'Customers',
        href: customersIndex(),
        icon: UserRound,
    },
    {
        title: 'Contacts',
        href: contactsIndex(),
        icon: Inbox,
    },
    {
        title: 'Support tickets',
        href: supportTicketsIndex(),
        icon: LifeBuoy,
    },
    {
        title: 'Attributes',
        href: attributesIndex(),
        icon: SlidersHorizontal,
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
        title: 'Materials',
        href: materialsIndex(),
        icon: Layers,
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
        title: 'Blogs',
        href: blogsIndex(),
        icon: FileText,
    },
    {
        title: 'Blog categories',
        href: blogCategoriesIndex(),
        icon: Newspaper,
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
    {
        title: 'Coupons',
        href: couponsIndex(),
        icon: Ticket,
    },
    {
        title: 'Newsletter subscribers',
        href: newsletterSubscribersIndex(),
        icon: Mail,
    },
    {
        title: 'Email templates',
        href: emailTemplatesIndex(),
        icon: MailOpen,
    },
    {
        title: 'Return policies',
        href: returnPoliciesIndex(),
        icon: RotateCcw,
    },
    {
        title: 'Pages',
        href: pagesIndex(),
        icon: ScrollText,
    },
    {
        title: 'Store locations',
        href: storeLocationsIndex(),
        icon: Store,
    },
    {
        title: 'Team members',
        href: teamMembersIndex(),
        icon: Users,
    },
    {
        title: 'Loyalty tiers',
        href: loyaltyTiersIndex(),
        icon: Award,
    },
    {
        title: 'Tax rates',
        href: taxRatesIndex(),
        icon: Percent,
    },
    {
        title: 'Warehouses',
        href: warehousesIndex(),
        icon: Warehouse,
    },
    {
        title: 'Suppliers',
        href: suppliersIndex(),
        icon: Factory,
    },
    {
        title: 'Gift cards',
        href: giftCardsIndex(),
        icon: Gift,
    },
    {
        title: 'Job openings',
        href: jobOpeningsIndex(),
        icon: Briefcase,
    },
    {
        title: 'Analytic scripts',
        href: analyticScriptsIndex(),
        icon: BarChart3,
    },
    {
        title: 'Delivery zones',
        href: deliveryZonesIndex(),
        icon: MapPinned,
    },
    {
        title: 'Stock clearance',
        href: stockClearanceSetupsIndex(),
        icon: Sparkles,
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
