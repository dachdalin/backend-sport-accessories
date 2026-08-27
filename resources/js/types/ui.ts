export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';

export type AppVariant = 'header' | 'sidebar';

export type FlashToast = {
    type: 'success' | 'info' | 'warning' | 'error';
    message: string;
};

export type Locale = 'en' | 'km';

export type NotificationType = 'order' | 'review' | 'ticket' | 'contact';

export type NotificationItem = {
    id?: string;
    type: NotificationType;
    title: string;
    subtitle: string;
    timestamp: string;
    href: string;
};

export type NotificationSummary = {
    items: NotificationItem[];
    total: number;
};
