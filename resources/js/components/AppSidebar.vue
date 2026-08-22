<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Award,
    BadgePercent,
    BarChart3,
    Briefcase,
    Coins,
    Factory,
    FileText,
    Flame,
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
    MapPin,
    MapPinned,
    MessageCircleQuestion,
    MessageSquareQuote,
    Newspaper,
    Palette,
    Percent,
    Receipt,
    Ruler,
    RefreshCcw,
    RotateCcw,
    ScrollText,
    Search,
    Settings,
    Share2,
    ShieldCheck,
    ShoppingBag,
    SlidersHorizontal,
    Sparkles,
    Star,
    Store,
    Tag,
    Tags,
    Ticket,
    TrendingUp,
    Truck,
    UserCog,
    UserRound,
    Users,
    Wallet,
    Warehouse,
    Zap,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
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
import { index as blogCategoriesIndex } from '@/routes/blog-categories';
import { index as blogsIndex } from '@/routes/blogs';
import { index as brandsIndex } from '@/routes/brands';
import { edit as businessSettingsEdit } from '@/routes/business-settings';
import { index as categoriesIndex } from '@/routes/categories';
import { index as colorsIndex } from '@/routes/colors';
import { index as contactsIndex } from '@/routes/contacts';
import { index as couponsIndex } from '@/routes/coupons';
import { index as credentialsIndex } from '@/routes/credentials';
import { index as currenciesIndex } from '@/routes/currencies';
import { index as customersIndex } from '@/routes/customers';
import { index as dealOfTheDaysIndex } from '@/routes/deal-of-the-days';
import { index as deliveryZonesIndex } from '@/routes/delivery-zones';
import { index as emailTemplatesIndex } from '@/routes/email-templates';
import { index as faqsIndex } from '@/routes/faqs';
import { index as featureDealsIndex } from '@/routes/feature-deals';
import { index as flashDealsIndex } from '@/routes/flash-deals';
import { index as giftCardsIndex } from '@/routes/gift-cards';
import { index as helpTopicsIndex } from '@/routes/help-topics';
import { index as jobOpeningsIndex } from '@/routes/job-openings';
import { index as loyaltyTiersIndex } from '@/routes/loyalty-tiers';
import { index as materialsIndex } from '@/routes/materials';
import { index as mostDemandedsIndex } from '@/routes/most-demandeds';
import { index as newsletterSubscribersIndex } from '@/routes/newsletter-subscribers';
import { index as offlinePaymentMethodsIndex } from '@/routes/offline-payment-methods';
import { index as ordersIndex } from '@/routes/orders';
import { index as pagesIndex } from '@/routes/pages';
import { index as productsIndex } from '@/routes/products';
import { index as refundRequestsIndex } from '@/routes/refund-requests';
import { index as returnPoliciesIndex } from '@/routes/return-policies';
import { index as reviewsIndex } from '@/routes/reviews';
import { index as rolesIndex } from '@/routes/roles';
import { index as searchFunctionsIndex } from '@/routes/search-functions';
import { index as shippingAddressesIndex } from '@/routes/shipping-addresses';
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
import { index as usersIndex } from '@/routes/users';
import { index as warehousesIndex } from '@/routes/warehouses';
import { index as wishlistsIndex } from '@/routes/wishlists';
import type { NavGroup } from '@/types';

const navGroups: NavGroup[] = [
    {
        label: 'Overview',
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
            },
            {
                title: 'Business settings',
                href: businessSettingsEdit(),
                icon: Settings,
            },
        ],
    },
    {
        label: 'Catalog',
        items: [
            {
                title: 'Products',
                href: productsIndex(),
                icon: ShoppingBag,
            },
            {
                title: 'Categories',
                href: categoriesIndex(),
                icon: FolderTree,
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
                title: 'Brands',
                href: brandsIndex(),
                icon: Tag,
            },
            {
                title: 'Tags',
                href: tagsIndex(),
                icon: Tags,
            },
        ],
    },
    {
        label: 'Merchandising',
        items: [
            {
                title: 'Flash deals',
                href: flashDealsIndex(),
                icon: Zap,
            },
            {
                title: 'Feature deals',
                href: featureDealsIndex(),
                icon: BadgePercent,
            },
            {
                title: 'Deal of the day',
                href: dealOfTheDaysIndex(),
                icon: Flame,
            },
            {
                title: 'Most demanded',
                href: mostDemandedsIndex(),
                icon: TrendingUp,
            },
            {
                title: 'Stock clearance',
                href: stockClearanceSetupsIndex(),
                icon: Sparkles,
            },
            {
                title: 'Coupons',
                href: couponsIndex(),
                icon: Ticket,
            },
            {
                title: 'Gift cards',
                href: giftCardsIndex(),
                icon: Gift,
            },
            {
                title: 'Banners',
                href: bannersIndex(),
                icon: GalleryHorizontal,
            },
        ],
    },
    {
        label: 'Sales',
        items: [
            {
                title: 'Orders',
                href: ordersIndex(),
                icon: Receipt,
            },
            {
                title: 'Refund requests',
                href: refundRequestsIndex(),
                icon: RefreshCcw,
            },
            {
                title: 'Return policies',
                href: returnPoliciesIndex(),
                icon: RotateCcw,
            },
        ],
    },
    {
        label: 'Customers',
        items: [
            {
                title: 'Customers',
                href: customersIndex(),
                icon: UserRound,
            },
            {
                title: 'Wishlists',
                href: wishlistsIndex(),
                icon: Heart,
            },
            {
                title: 'Reviews',
                href: reviewsIndex(),
                icon: Star,
            },
            {
                title: 'Testimonials',
                href: testimonialsIndex(),
                icon: MessageSquareQuote,
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
                title: 'Newsletter subscribers',
                href: newsletterSubscribersIndex(),
                icon: Mail,
            },
        ],
    },
    {
        label: 'Content',
        items: [
            {
                title: 'Pages',
                href: pagesIndex(),
                icon: ScrollText,
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
                title: 'FAQs',
                href: faqsIndex(),
                icon: MessageCircleQuestion,
            },
            {
                title: 'Help topics',
                href: helpTopicsIndex(),
                icon: HelpCircle,
            },
            {
                title: 'Team members',
                href: teamMembersIndex(),
                icon: Users,
            },
            {
                title: 'Store locations',
                href: storeLocationsIndex(),
                icon: Store,
            },
            {
                title: 'Job openings',
                href: jobOpeningsIndex(),
                icon: Briefcase,
            },
            {
                title: 'Loyalty tiers',
                href: loyaltyTiersIndex(),
                icon: Award,
            },
        ],
    },
    {
        label: 'Operations',
        items: [
            {
                title: 'Currencies',
                href: currenciesIndex(),
                icon: Coins,
            },
            {
                title: 'Tax rates',
                href: taxRatesIndex(),
                icon: Percent,
            },
            {
                title: 'Shipping methods',
                href: shippingMethodsIndex(),
                icon: Truck,
            },
            {
                title: 'Shipping addresses',
                href: shippingAddressesIndex(),
                icon: MapPin,
            },
            {
                title: 'Delivery zones',
                href: deliveryZonesIndex(),
                icon: MapPinned,
            },
            {
                title: 'Offline payment methods',
                href: offlinePaymentMethodsIndex(),
                icon: Wallet,
            },
            {
                title: 'Suppliers',
                href: suppliersIndex(),
                icon: Factory,
            },
            {
                title: 'Warehouses',
                href: warehousesIndex(),
                icon: Warehouse,
            },
        ],
    },
    {
        label: 'System',
        items: [
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
                title: 'Email templates',
                href: emailTemplatesIndex(),
                icon: MailOpen,
            },
            {
                title: 'Analytic scripts',
                href: analyticScriptsIndex(),
                icon: BarChart3,
            },
            {
                title: 'Search functions',
                href: searchFunctionsIndex(),
                icon: Search,
            },
        ],
    },
    {
        label: 'Administrator',
        items: [
            {
                title: 'Users',
                href: usersIndex(),
                icon: UserCog,
            },
            {
                title: 'Roles',
                href: rolesIndex(),
                icon: ShieldCheck,
            },
        ],
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
            <NavMain :groups="navGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
