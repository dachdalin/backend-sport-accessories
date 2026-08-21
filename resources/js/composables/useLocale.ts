import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Locale } from '@/types/ui';

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

export function useLocale() {
    const page = usePage();

    const locale = computed<Locale>(() => page.props.locale ?? 'en');

    function setLocale(value: Locale) {
        if (value === locale.value) {
            return;
        }

        setCookie('locale', value);

        // Reload so the server re-renders with the new locale (updates
        // <html lang> and anything translated server-side).
        window.location.reload();
    }

    return { locale, setLocale };
}
