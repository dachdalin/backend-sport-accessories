import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

/**
 * A CSRF/session mismatch (419) has no X-Inertia header, so Inertia treats it as a raw
 * HTTP exception and, left unhandled, swaps the whole app for Laravel's blank error page.
 * Reload instead so the user lands back on a working page (or the login screen).
 */
export function initializeSessionExpiryHandling(): void {
    router.on('httpException', (event) => {
        const response = (event as CustomEvent).detail?.response;

        if (response?.status !== 419) {
            return;
        }

        event.preventDefault();

        toast.error('Your session expired. Reloading the page…');

        window.setTimeout(() => window.location.reload(), 600);
    });
}
