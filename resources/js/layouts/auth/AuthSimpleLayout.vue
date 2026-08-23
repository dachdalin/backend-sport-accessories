<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <Head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin=""
        />
        <link
            href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500&family=Teko:wght@500;600;700&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="auth-shell flex min-h-svh flex-col items-center justify-center px-6 py-16"
    >
        <div class="flex w-full max-w-sm flex-col items-center">
            <div class="auth-shell__cord" aria-hidden="true" />

            <div class="auth-shell__tag w-full px-7 pt-9 pb-6 sm:px-9">
                <Link
                    :href="home()"
                    class="mb-6 flex items-center justify-center gap-2.5"
                >
                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--whistle)]"
                    >
                        <AppLogoIcon
                            class="size-4 fill-current text-[var(--ink)]"
                        />
                    </span>
                    <span class="auth-shell__brand">{{ name }}</span>
                </Link>

                <div v-if="title || description" class="mb-6 text-center">
                    <h1 v-if="title" class="auth-shell__title">
                        {{ title }}
                    </h1>
                    <p
                        v-if="description"
                        class="mt-1.5 text-sm text-[var(--ink-muted)]"
                    >
                        {{ description }}
                    </p>
                </div>

                <slot />

                <div class="auth-shell__perforation" aria-hidden="true" />
                <p class="auth-shell__code">
                    SKU&nbsp;·&nbsp;000-000&nbsp;&nbsp;SIZE&nbsp;·&nbsp;ALL&nbsp;&nbsp;STATUS&nbsp;·&nbsp;ACTIVE
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.auth-shell {
    --court-bg: #201503;
    --court-line: #46300f;
    --tag-cream: #f4ead4;
    --tag-cream-soft: #ece0c4;
    --ink: #2a1d0d;
    --ink-muted: #7a6a4f;
    --whistle: #ff8904;
    --lace: #c1443a;

    background:
        radial-gradient(
            ellipse 900px 480px at 50% -8%,
            rgba(255, 137, 4, 0.16),
            transparent 60%
        ),
        var(--court-bg);

    /* Re-theme form primitives (Input, Button, Checkbox, Label) locally,
       independent of the app-wide light/dark preference, so the sign-in
       tag keeps one fixed identity every time staff check in. Re-declaring
       `color` here (not just the --foreground token) matters: `color` is
       inherited by value, so without this the page's already-resolved
       body text color (light or dark) would leak into the tag untouched. */
    color: var(--ink);
    --background: var(--tag-cream);
    --foreground: var(--ink);
    --card: var(--tag-cream);
    --card-foreground: var(--ink);
    --muted-foreground: var(--ink-muted);
    --primary: var(--whistle);
    --primary-foreground: var(--ink);
    --secondary: var(--tag-cream-soft);
    --secondary-foreground: var(--ink);
    --border: #d9c69c;
    --input: #d9c69c;
    --ring: var(--whistle);
    --destructive: var(--lace);
    --destructive-foreground: var(--tag-cream);
}

.auth-shell__cord {
    width: 2px;
    height: 34px;
    background: var(--court-line);
}

.auth-shell__tag {
    position: relative;
    background: var(--tag-cream);
    border-radius: 4px 4px 16px 16px;
    box-shadow:
        0 30px 70px -24px rgba(0, 0, 0, 0.6),
        0 1px 0 rgba(255, 255, 255, 0.35) inset;
}

.auth-shell__tag::before {
    content: '';
    position: absolute;
    top: -13px;
    left: 50%;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: var(--court-bg);
    border: 3px solid var(--court-line);
    transform: translateX(-50%);
}

.auth-shell__brand {
    font-family: 'Teko', var(--font-sans);
    font-size: 1.5rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    color: var(--ink);
    line-height: 1;
}

.auth-shell__title {
    font-family: 'Teko', var(--font-sans);
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    color: var(--ink);
    line-height: 1.1;
}

.auth-shell__perforation {
    margin: 1.75rem 0 0.875rem;
    border-top: 1.5px dashed #d9c69c;
}

.auth-shell__code {
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 0.6875rem;
    letter-spacing: 0.06em;
    text-align: center;
    color: var(--ink-muted);
    text-transform: uppercase;
}

@media (prefers-reduced-motion: no-preference) {
    .auth-shell__cord {
        transform-origin: top center;
        animation: cord-drop 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .auth-shell__tag {
        animation: tag-settle 0.6s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
}

@keyframes cord-drop {
    from {
        transform: scaleY(0);
        opacity: 0;
    }
}

@keyframes tag-settle {
    from {
        transform: translateY(-14px) rotate(-1.5deg);
        opacity: 0;
    }
}
</style>
