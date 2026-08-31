<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import { dashboard, login } from '@/routes';

const props = defineProps<{
    status: number;
}>();

type Tone = 'amber' | 'red' | 'dim';

type Action = 'home' | 'login' | 'retry' | 'retry-home';

type ErrorMeta = {
    call: string;
    tone: Tone;
    headline: string;
    body: string;
    action: Action;
};

const META: Record<number, ErrorMeta> = {
    403: {
        call: 'RED CARD',
        tone: 'red',
        headline: 'SENT OFF',
        body: "Your account doesn't carry clearance for this page. Ask an admin to update your permissions if you need back in.",
        action: 'home',
    },
    404: {
        call: 'OFFSIDE FLAG',
        tone: 'amber',
        headline: 'OFFSIDE',
        body: "This page isn't on the pitch. Check the address, or it may have moved since the last kick-off.",
        action: 'home',
    },
    419: {
        call: 'TIMEOUT',
        tone: 'amber',
        headline: 'STOPPAGE TIME',
        body: 'Your session ran out the clock. Sign in again to get back on the pitch.',
        action: 'login',
    },
    429: {
        call: 'FALSE START',
        tone: 'amber',
        headline: 'FALSE START',
        body: 'Too many attempts, too fast. Wait a moment, then try again.',
        action: 'retry',
    },
    500: {
        call: 'SYSTEM FAULT',
        tone: 'dim',
        headline: 'MATCH SUSPENDED',
        body: "Something failed on our side of the pitch. We've logged it — try again shortly.",
        action: 'retry-home',
    },
    503: {
        call: 'MAINTENANCE BREAK',
        tone: 'dim',
        headline: 'HALFTIME',
        body: "We're doing scheduled maintenance. Kick-off resumes shortly.",
        action: 'retry',
    },
};

const FALLBACK: ErrorMeta = {
    call: 'REVIEW',
    tone: 'dim',
    headline: 'PLAY STOPPED',
    body: 'Something interrupted the match. Try again, or head back to the dashboard.',
    action: 'retry-home',
};

const TONE_COLOR: Record<Tone, { chip: string; border: string; text: string; glow: string }> = {
    amber: { chip: 'rgba(255,137,4,0.14)', border: 'rgba(255,137,4,0.45)', text: '#ffb347', glow: '#ff8904' },
    red: { chip: 'rgba(224,57,62,0.14)', border: 'rgba(224,57,62,0.5)', text: '#ff9b9e', glow: '#e0393e' },
    dim: { chip: 'rgba(237,224,200,0.08)', border: 'rgba(237,224,200,0.22)', text: '#c9bfa8', glow: '#a8916a' },
};

const page = usePage();
const isAuthed = computed(() => Boolean(page.props.auth.user));

const meta = computed(() => META[props.status] ?? FALLBACK);
const tone = computed(() => TONE_COLOR[meta.value.tone]);

const homeHref = computed(() => (isAuthed.value ? dashboard() : login()));
const homeLabel = computed(() => (isAuthed.value ? 'Back to dashboard' : 'Back to sign in'));

const reload = () => router.reload();
</script>

<template>
    <Head :title="`${status} — ${meta.headline}`" />

    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#140d02] px-6 py-16 text-[#ede0c8]">
        <div class="pointer-events-none absolute -top-40 -left-32 h-96 w-96 rounded-full bg-[#ff8904] opacity-[0.12] blur-[110px]" aria-hidden="true" />
        <div class="pointer-events-none absolute -top-32 -right-24 h-80 w-80 rounded-full bg-[#ff8904] opacity-[0.08] blur-[100px]" aria-hidden="true" />

        <div class="flex w-full max-w-md flex-col items-center gap-8">
            <Link :href="homeHref" class="flex items-center gap-2 opacity-80 transition-opacity hover:opacity-100">
                <AppLogo />
            </Link>

            <div
                class="relative w-full overflow-hidden rounded-lg border bg-gradient-to-b from-[#241704] to-[#1a1102] p-8 text-center shadow-[0_0_60px_-20px_var(--glow)]"
                :style="{ borderColor: tone.border, '--glow': tone.glow }"
            >
                <div class="scanlines pointer-events-none absolute inset-0" aria-hidden="true" />

                <span
                    class="relative inline-flex items-center gap-1.5 rounded-full border px-3 py-1 font-mono text-[11px] font-medium tracking-[0.15em] uppercase"
                    :style="{ backgroundColor: tone.chip, borderColor: tone.border, color: tone.text }"
                >
                    <span class="size-1.5 rounded-full" :style="{ backgroundColor: tone.text }" />
                    {{ meta.call }}
                </span>

                <p
                    class="digits relative mt-4 leading-none font-[Teko] font-semibold tabular-nums"
                    :style="{ color: tone.glow, textShadow: `0 0 32px ${tone.glow}66` }"
                >
                    {{ status }}
                </p>

                <h1 class="relative mt-2 font-[Teko] text-4xl font-semibold tracking-[0.06em] text-[#ede0c8] uppercase sm:text-5xl">
                    {{ meta.headline }}
                </h1>

                <p class="relative mx-auto mt-4 max-w-sm text-sm leading-relaxed text-[#ede0c8]/70">
                    {{ meta.body }}
                </p>

                <div class="relative mt-7 flex flex-wrap items-center justify-center gap-3">
                    <button
                        v-if="meta.action === 'retry' || meta.action === 'retry-home'"
                        type="button"
                        class="rounded-md bg-[#ff8904] px-5 py-2 text-sm font-semibold text-[#2d1e06] transition-colors hover:bg-[#ff9c2b]"
                        @click="reload"
                    >
                        Try again
                    </button>

                    <Link
                        v-if="meta.action === 'home' || meta.action === 'retry-home'"
                        :href="homeHref"
                        :class="[
                            'rounded-md px-5 py-2 text-sm font-semibold transition-colors',
                            meta.action === 'retry-home'
                                ? 'border border-[#ede0c8]/20 text-[#ede0c8] hover:bg-[#ede0c8]/5'
                                : 'bg-[#ff8904] text-[#2d1e06] hover:bg-[#ff9c2b]',
                        ]"
                    >
                        {{ homeLabel }}
                    </Link>

                    <Link
                        v-if="meta.action === 'login'"
                        :href="login()"
                        class="rounded-md bg-[#ff8904] px-5 py-2 text-sm font-semibold text-[#2d1e06] transition-colors hover:bg-[#ff9c2b]"
                    >
                        Sign in again
                    </Link>
                </div>
            </div>

            <div class="w-full">
                <div class="border-t border-[#ede0c8]/15" />
                <div class="pitch-ticks mt-0 h-1.5 w-full" />
                <p class="mt-3 text-center font-mono text-[11px] tracking-[0.15em] text-[#ede0c8]/40 uppercase">
                    {{ page.props.name }} · status {{ status }}
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.digits {
    font-size: 5.5rem;
}

@media (min-width: 640px) {
    .digits {
        font-size: 7rem;
    }
}

.scanlines {
    background-image: repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.35) 0px, rgba(0, 0, 0, 0.35) 1px, transparent 1px, transparent 3px);
    opacity: 0.4;
    mix-blend-mode: multiply;
}

.pitch-ticks {
    background-image: repeating-linear-gradient(to right, rgba(237, 224, 200, 0.35) 0, rgba(237, 224, 200, 0.35) 2px, transparent 2px, transparent 28px);
}

@media (prefers-reduced-motion: no-preference) {
    .digits {
        animation: led-on 0.7s steps(6, end);
    }
}

@keyframes led-on {
    0% {
        opacity: 0.2;
    }
    30% {
        opacity: 0.85;
    }
    45% {
        opacity: 0.3;
    }
    60% {
        opacity: 1;
    }
    100% {
        opacity: 1;
    }
}
</style>
