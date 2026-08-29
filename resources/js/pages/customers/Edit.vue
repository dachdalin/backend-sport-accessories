<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    Fingerprint,
    Mail,
    MapPin,
    Phone,
    Rocket,
    ShieldCheck,
    UserRound,
    Wallet,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import CustomerController from '@/actions/App/Http/Controllers/Backend/CustomerController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useInitials } from '@/composables/useInitials';
import { edit, index } from '@/routes/customers';

type Provider = 'manual' | 'google' | 'telegram';

type Customer = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    address: string | null;
    balance: string;
    status: boolean;
    provider: Provider;
    provider_id: string | null;
};

const props = defineProps<{
    customer: Customer;
}>();

defineOptions({
    layout: (pageProps: { customer: Customer }) => ({
        breadcrumbs: [
            {
                title: 'Customers',
                href: index(),
            },
            {
                title: 'Edit customer',
                href: edit(pageProps.customer),
            },
        ],
    }),
});

const { getInitials } = useInitials();

const providerLabel: Record<Provider, string> = {
    manual: 'Manual',
    google: 'Google',
    telegram: 'Telegram',
};

const balance = ref(props.customer.balance);
const balanceNumber = computed(() => parseFloat(balance.value) || 0);
const formattedBalance = computed(() =>
    balanceNumber.value.toLocaleString(undefined, {
        style: 'currency',
        currency: 'USD',
    }),
);
</script>

<template>
    <Head title="Edit customer" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit customer"
            :description="`Update the details for ${customer.name}`"
        />

        <div
            class="flex flex-wrap items-center gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border"
        >
            <Avatar class="size-12 rounded-full">
                <AvatarFallback
                    class="rounded-full bg-[#ff8904]/15 text-base font-semibold text-[#ff8904]"
                >
                    {{ getInitials(customer.name) }}
                </AvatarFallback>
            </Avatar>
            <div class="min-w-0">
                <p class="truncate font-medium">{{ customer.name }}</p>
                <p class="truncate text-sm text-muted-foreground">
                    {{ customer.email }}
                </p>
            </div>
            <Badge
                :variant="customer.provider === 'manual' ? 'outline' : 'secondary'"
                class="ml-auto"
            >
                <Fingerprint class="size-3" aria-hidden="true" />
                {{ providerLabel[customer.provider] }}
            </Badge>
        </div>

        <Form
            v-bind="CustomerController.update.form(customer)"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <UserRound
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Profile</CardTitle>
                        </div>
                        <CardDescription>
                            Who this customer is and how to reach them.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                name="name"
                                required
                                autofocus
                                :default-value="customer.name"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="email" class="flex items-center gap-1.5">
                                    <Mail
                                        class="size-3.5 text-muted-foreground"
                                        aria-hidden="true"
                                    />
                                    Email
                                </Label>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                    :default-value="customer.email"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="phone" class="flex items-center gap-1.5">
                                    <Phone
                                        class="size-3.5 text-muted-foreground"
                                        aria-hidden="true"
                                    />
                                    Phone
                                </Label>
                                <Input
                                    id="phone"
                                    name="phone"
                                    :default-value="customer.phone ?? ''"
                                    placeholder="+1 555 000 0000"
                                />
                                <InputError :message="errors.phone" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="address" class="flex items-center gap-1.5">
                                <MapPin
                                    class="size-3.5 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                Address
                            </Label>
                            <Input
                                id="address"
                                name="address"
                                :default-value="customer.address ?? ''"
                                placeholder="123 Main Street, London"
                            />
                            <InputError :message="errors.address" />
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="customer.provider !== 'manual'">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Fingerprint
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Sign-in</CardTitle>
                        </div>
                        <CardDescription>
                            Read-only. Set automatically the first time this
                            customer signed in.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <dl class="grid grid-cols-3 gap-y-3 text-sm">
                            <dt class="text-muted-foreground">Provider</dt>
                            <dd class="col-span-2 font-medium">
                                {{ providerLabel[customer.provider] }}
                            </dd>

                            <dt class="text-muted-foreground">Provider ID</dt>
                            <dd class="col-span-2 truncate font-mono text-xs">
                                {{ customer.provider_id }}
                            </dd>
                        </dl>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Wallet
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Wallet</CardTitle>
                        </div>
                        <CardDescription>
                            Store credit available to this account.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div
                            class="flex items-center gap-3 rounded-lg border border-[#ff8904]/25 bg-[#ff8904]/5 p-3"
                        >
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#ff8904]/15 text-[#ff8904]"
                            >
                                <Wallet class="size-4.5" aria-hidden="true" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-muted-foreground">
                                    Balance
                                </p>
                                <p
                                    class="truncate text-xl font-semibold tabular-nums"
                                >
                                    {{ formattedBalance }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="balance">Amount</Label>
                            <Input
                                id="balance"
                                name="balance"
                                type="number"
                                step="0.01"
                                min="0"
                                v-model="balance"
                            />
                            <InputError :message="errors.balance" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <ShieldCheck
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Access</CardTitle>
                        </div>
                        <CardDescription>
                            Whether this customer can sign in and order.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <label
                            for="status"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                :default-value="customer.status"
                            />
                            <span class="text-sm font-medium">Active</span>
                        </label>
                        <InputError :message="errors.status" class="mt-2" />
                    </CardContent>
                </Card>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Rocket
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Save</CardTitle>
                        </div>
                        <CardDescription>
                            Save your changes to this customer.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save customer
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>
