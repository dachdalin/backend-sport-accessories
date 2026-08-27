<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Award,
    BadgePercent,
    BarChart3,
    Briefcase,
    Code2,
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
    MessageSquare,
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
import { computed } from 'vue';
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
import { usePermissions } from '@/composables/usePermissions';
import { dashboard } from '@/routes';
import { index as analyticScriptsIndex } from '@/routes/analytic-scripts';
import { index as apiDocumentationIndex } from '@/routes/api-documentation';
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
import { index as messagesIndex } from '@/routes/messages';
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

const { can } = usePermissions();

const navGroups: NavGroup[] = [
    {
        label: 'Overview',
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
                permission: 'view dashboard',
            },
            {
                title: 'Messages',
                href: messagesIndex(),
                icon: MessageSquare,
                permission: 'view messages',
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
                permission: 'view users',
            },
            {
                title: 'Roles',
                href: rolesIndex(),
                icon: ShieldCheck,
                permission: 'view roles',
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
                permission: 'view products',
            },
            {
                title: 'Categories',
                href: categoriesIndex(),
                icon: FolderTree,
                permission: 'view categories',
            },
            {
                title: 'Attributes',
                href: attributesIndex(),
                icon: SlidersHorizontal,
                permission: 'view attributes',
            },
            {
                title: 'Colors',
                href: colorsIndex(),
                icon: Palette,
                permission: 'view colors',
            },
            {
                title: 'Sizes',
                href: sizesIndex(),
                icon: Ruler,
                permission: 'view sizes',
            },
            {
                title: 'Materials',
                href: materialsIndex(),
                icon: Layers,
                permission: 'view materials',
            },
            {
                title: 'Brands',
                href: brandsIndex(),
                icon: Tag,
                permission: 'view brands',
            },
            {
                title: 'Tags',
                href: tagsIndex(),
                icon: Tags,
                permission: 'view tags',
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
                permission: 'view flash deals',
            },
            {
                title: 'Feature deals',
                href: featureDealsIndex(),
                icon: BadgePercent,
                permission: 'view feature deals',
            },
            {
                title: 'Deal of the day',
                href: dealOfTheDaysIndex(),
                icon: Flame,
                permission: 'view deal of the days',
            },
            {
                title: 'Most demanded',
                href: mostDemandedsIndex(),
                icon: TrendingUp,
                permission: 'view most demandeds',
            },
            {
                title: 'Stock clearance',
                href: stockClearanceSetupsIndex(),
                icon: Sparkles,
                permission: 'view stock clearance setups',
            },
            {
                title: 'Coupons',
                href: couponsIndex(),
                icon: Ticket,
                permission: 'view coupons',
            },
            {
                title: 'Gift cards',
                href: giftCardsIndex(),
                icon: Gift,
                permission: 'view gift cards',
            },
            {
                title: 'Banners',
                href: bannersIndex(),
                icon: GalleryHorizontal,
                permission: 'view banners',
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
                permission: 'view orders',
            },
            {
                title: 'Refund requests',
                href: refundRequestsIndex(),
                icon: RefreshCcw,
                permission: 'view refund requests',
            },
            {
                title: 'Return policies',
                href: returnPoliciesIndex(),
                icon: RotateCcw,
                permission: 'view return policies',
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
                permission: 'view customers',
            },
            {
                title: 'Wishlists',
                href: wishlistsIndex(),
                icon: Heart,
                permission: 'view wishlists',
            },
            {
                title: 'Reviews',
                href: reviewsIndex(),
                icon: Star,
                permission: 'view reviews',
            },
            {
                title: 'Testimonials',
                href: testimonialsIndex(),
                icon: MessageSquareQuote,
                permission: 'view testimonials',
            },
            {
                title: 'Contacts',
                href: contactsIndex(),
                icon: Inbox,
                permission: 'view contacts',
            },
            {
                title: 'Support tickets',
                href: supportTicketsIndex(),
                icon: LifeBuoy,
                permission: 'view support tickets',
            },
            {
                title: 'Newsletter subscribers',
                href: newsletterSubscribersIndex(),
                icon: Mail,
                permission: 'view newsletter subscribers',
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
                permission: 'view pages',
            },
            {
                title: 'Blogs',
                href: blogsIndex(),
                icon: FileText,
                permission: 'view blogs',
            },
            {
                title: 'Blog categories',
                href: blogCategoriesIndex(),
                icon: Newspaper,
                permission: 'view blog categories',
            },
            {
                title: 'FAQs',
                href: faqsIndex(),
                icon: MessageCircleQuestion,
                permission: 'view faqs',
            },
            {
                title: 'Help topics',
                href: helpTopicsIndex(),
                icon: HelpCircle,
                permission: 'view help topics',
            },
            {
                title: 'Team members',
                href: teamMembersIndex(),
                icon: Users,
                permission: 'view team members',
            },
            {
                title: 'Store locations',
                href: storeLocationsIndex(),
                icon: Store,
                permission: 'view store locations',
            },
            {
                title: 'Job openings',
                href: jobOpeningsIndex(),
                icon: Briefcase,
                permission: 'view job openings',
            },
            {
                title: 'Loyalty tiers',
                href: loyaltyTiersIndex(),
                icon: Award,
                permission: 'view loyalty tiers',
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
                permission: 'view currencies',
            },
            {
                title: 'Tax rates',
                href: taxRatesIndex(),
                icon: Percent,
                permission: 'view tax rates',
            },
            {
                title: 'Shipping methods',
                href: shippingMethodsIndex(),
                icon: Truck,
                permission: 'view shipping methods',
            },
            {
                title: 'Shipping addresses',
                href: shippingAddressesIndex(),
                icon: MapPin,
                permission: 'view shipping addresses',
            },
            {
                title: 'Delivery zones',
                href: deliveryZonesIndex(),
                icon: MapPinned,
                permission: 'view delivery zones',
            },
            {
                title: 'Offline payment methods',
                href: offlinePaymentMethodsIndex(),
                icon: Wallet,
                permission: 'view offline payment methods',
            },
            {
                title: 'Suppliers',
                href: suppliersIndex(),
                icon: Factory,
                permission: 'view suppliers',
            },
            {
                title: 'Warehouses',
                href: warehousesIndex(),
                icon: Warehouse,
                permission: 'view warehouses',
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
                permission: 'view social medias',
            },
            {
                title: 'Credentials',
                href: credentialsIndex(),
                icon: KeyRound,
                permission: 'view credentials',
            },
            {
                title: 'Email templates',
                href: emailTemplatesIndex(),
                icon: MailOpen,
                permission: 'view email templates',
            },
            {
                title: 'Analytic scripts',
                href: analyticScriptsIndex(),
                icon: BarChart3,
                permission: 'view analytic scripts',
            },
            {
                title: 'Search functions',
                href: searchFunctionsIndex(),
                icon: Search,
                permission: 'view search functions',
            },
            {
                title: 'Business settings',
                href: businessSettingsEdit(),
                icon: Settings,
                permission: 'view business settings',
            },
            {
                title: 'API documentation',
                href: apiDocumentationIndex(),
                icon: Code2,
                permission: 'view api documentation',
            },
        ],
    },
];

const visibleNavGroups = computed<NavGroup[]>(() =>
    navGroups
        .map((group) => ({
            ...group,
            items: group.items.filter(
                (item) => !item.permission || can(item.permission),
            ),
        }))
        .filter((group) => group.items.length > 0),
);
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
            <NavMain :groups="visibleNavGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
