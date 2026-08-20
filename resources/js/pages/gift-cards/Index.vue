<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import GiftCardController from '@/actions/App/Http/Controllers/Backend/GiftCardController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/gift-cards';

type GiftCard = {
    id: number;
    code: string;
    initial_balance: string;
    balance: string;
    expires_at: string | null;
    status: boolean;
};

defineProps<{
    giftCards: GiftCard[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Gift cards',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Gift cards" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Gift cards"
                description="Manage store gift cards and their balances"
            />
            <Button as-child>
                <Link :href="create()">Add gift card</Link>
            </Button>
        </div>

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Code</th>
                        <th class="p-3 font-medium">Initial balance</th>
                        <th class="p-3 font-medium">Remaining balance</th>
                        <th class="p-3 font-medium">Expires</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="giftCard in giftCards"
                        :key="giftCard.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ giftCard.code }}</td>
                        <td class="p-3 text-muted-foreground">
                            ${{ Number(giftCard.initial_balance).toFixed(2) }}
                        </td>
                        <td class="p-3">
                            ${{ Number(giftCard.balance).toFixed(2) }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ giftCard.expires_at ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    giftCard.status ? 'default' : 'secondary'
                                "
                            >
                                {{ giftCard.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(giftCard)">Edit</Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                GiftCardController.destroy.form(
                                                    giftCard,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        giftCard.code
                                                    }}"?</DialogTitle
                                                >
                                            </DialogHeader>

                                            <DialogFooter class="mt-6 gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary"
                                                        >Cancel</Button
                                                    >
                                                </DialogClose>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="giftCards.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No gift cards yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
