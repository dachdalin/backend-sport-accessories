<script setup lang="ts">
import { ImageUpIcon, Trash2Icon, TriangleAlertIcon } from '@lucide/vue';
import { computed, onBeforeUnmount, ref, useId, watch } from 'vue';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';

/**
 * Global drag-and-drop image upload field. Renders a real, named
 * `<input type="file">` so it serializes through Inertia's `<Form>` like
 * any other field — drag/drop and click-to-browse are layered on top of it.
 */
const props = withDefaults(
    defineProps<{
        /** Field name submitted with the form, e.g. "image" or "images[]". */
        name: string;
        id?: string;
        label?: string;
        /** Helper text shown under the dropzone. Defaults to accept + size limits. */
        hint?: string;
        accept?: string;
        multiple?: boolean;
        maxFiles?: number;
        maxSizeMb?: number;
        disabled?: boolean;
        /** Server-side validation message, e.g. `errors.image`. */
        error?: string;
        /** True while the surrounding form is submitting (Inertia's `processing`). */
        processing?: boolean;
        /** Already-saved image URL(s), shown until replaced by a new selection. */
        initialPreviews?: string[];
    }>(),
    {
        accept: 'image/*',
        multiple: false,
        maxFiles: undefined,
        maxSizeMb: 5,
        disabled: false,
        error: undefined,
        processing: false,
        initialPreviews: () => [],
        id: undefined,
        label: undefined,
        hint: undefined,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', files: File[]): void;
}>();

const fieldId = props.id ?? `dropzone-${useId()}`;
const inputEl = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const dragDepth = ref(0);
const localError = ref<string | null>(null);

type Entry = { file: File; previewUrl: string };
const entries = ref<Entry[]>([]);

const effectiveMaxFiles = computed(
    () => props.maxFiles ?? (props.multiple ? Infinity : 1),
);
const message = computed(() => localError.value ?? props.error ?? null);
const showInitialPreviews = computed(
    () => entries.value.length === 0 && props.initialPreviews.length > 0,
);
const defaultHint = computed(() => {
    const kinds =
        props.accept === 'image/*' ? 'PNG, JPG or WEBP' : props.accept;

    return `${kinds}, up to ${props.maxSizeMb}MB each`;
});

function matchesAccept(file: File): boolean {
    return props.accept
        .split(',')
        .map((token) => token.trim())
        .some((token) => {
            if (!token) {
                return true;
            }

            if (token.endsWith('/*')) {
                return file.type.startsWith(token.slice(0, -1));
            }

            if (token.startsWith('.')) {
                return file.name.toLowerCase().endsWith(token.toLowerCase());
            }

            return file.type === token;
        });
}

function revoke(entry: Entry): void {
    URL.revokeObjectURL(entry.previewUrl);
}

function syncNativeInput(): void {
    if (!inputEl.value) {
        return;
    }

    const transfer = new DataTransfer();
    entries.value.forEach(({ file }) => transfer.items.add(file));
    inputEl.value.files = transfer.files;
}

function addFiles(incoming: File[]): void {
    if (props.disabled || props.processing) {
        return;
    }

    localError.value = null;

    const accepted: File[] = [];

    for (const file of incoming) {
        if (!matchesAccept(file)) {
            localError.value = `"${file.name}" isn't an accepted image type.`;
            continue;
        }

        if (file.size > props.maxSizeMb * 1024 * 1024) {
            localError.value = `"${file.name}" is over the ${props.maxSizeMb}MB limit.`;
            continue;
        }

        accepted.push(file);
    }

    if (accepted.length === 0) {
        return;
    }

    const merged = props.multiple
        ? [...entries.value.map((entry) => entry.file), ...accepted]
        : accepted.slice(-1);

    if (merged.length > effectiveMaxFiles.value) {
        localError.value = `You can only add up to ${effectiveMaxFiles.value} image${effectiveMaxFiles.value === 1 ? '' : 's'}.`;
    }

    const kept = merged.slice(0, effectiveMaxFiles.value);

    entries.value.forEach(revoke);
    entries.value = kept.map((file) => ({
        file,
        previewUrl: URL.createObjectURL(file),
    }));

    syncNativeInput();
    emit('update:modelValue', kept);
}

function removeAt(index: number): void {
    revoke(entries.value[index]);
    entries.value.splice(index, 1);
    syncNativeInput();
    emit(
        'update:modelValue',
        entries.value.map((entry) => entry.file),
    );
}

function onDrop(event: DragEvent): void {
    isDragging.value = false;
    dragDepth.value = 0;
    addFiles([...(event.dataTransfer?.files ?? [])]);
}

function onDragEnter(): void {
    if (props.disabled || props.processing) {
        return;
    }

    dragDepth.value += 1;
    isDragging.value = true;
}

function onDragLeave(): void {
    dragDepth.value = Math.max(0, dragDepth.value - 1);
    isDragging.value = dragDepth.value > 0;
}

function onInputChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    addFiles([...(input.files ?? [])]);
}

function openBrowser(): void {
    if (props.disabled || props.processing) {
        return;
    }

    inputEl.value?.click();
}

function onKeydown(event: KeyboardEvent): void {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openBrowser();
    }
}

watch(
    () => props.initialPreviews,
    () => {
        // A fresh set of saved images (e.g. navigating to a different record)
        // means any in-progress local selection is stale.
        entries.value.forEach(revoke);
        entries.value = [];
    },
);

onBeforeUnmount(() => {
    entries.value.forEach(revoke);
});
</script>

<template>
    <div class="grid gap-2">
        <Label v-if="label" :for="fieldId">{{ label }}</Label>

        <div
            :id="fieldId"
            role="button"
            tabindex="0"
            :aria-disabled="disabled || processing"
            :data-dragging="isDragging || undefined"
            :data-processing="processing || undefined"
            :class="
                cn(
                    'group relative flex min-h-40 w-full flex-col items-center justify-center gap-2 overflow-hidden rounded-lg border-2 border-dashed border-input bg-transparent p-4 text-center transition-colors',
                    'hover:border-ring/60',
                    'focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none',
                    isDragging && 'border-primary bg-primary/5',
                    (disabled || processing) && 'cursor-not-allowed opacity-60',
                    !disabled && !processing && 'cursor-pointer',
                )
            "
            @click="openBrowser"
            @keydown="onKeydown"
            @dragover.prevent
            @dragenter.prevent="onDragEnter"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
        >
            <span class="dropzone-bracket dropzone-bracket--tl" />
            <span class="dropzone-bracket dropzone-bracket--tr" />
            <span class="dropzone-bracket dropzone-bracket--bl" />
            <span class="dropzone-bracket dropzone-bracket--br" />

            <input
                :id="`${fieldId}-input`"
                ref="inputEl"
                :name="name"
                type="file"
                :accept="accept"
                :multiple="multiple"
                :disabled="disabled"
                class="sr-only"
                tabindex="-1"
                @click.stop
                @change="onInputChange"
            />

            <template v-if="entries.length === 0 && !showInitialPreviews">
                <ImageUpIcon class="size-8 text-muted-foreground" />
                <p class="text-sm font-medium">
                    Drag images here or
                    <span class="text-primary underline underline-offset-2"
                        >browse</span
                    >
                </p>
            </template>

            <div
                v-else-if="!multiple"
                class="group/thumb relative aspect-video w-full max-w-sm overflow-hidden rounded-md border border-input"
            >
                <img
                    :src="entries[0]?.previewUrl ?? initialPreviews[0]"
                    :alt="entries[0]?.file.name ?? 'Current image'"
                    class="size-full object-cover"
                />
                <button
                    v-if="entries[0]"
                    type="button"
                    class="absolute top-2 right-2 flex size-7 items-center justify-center rounded-full bg-background/90 text-destructive shadow-sm transition-colors hover:bg-destructive hover:text-destructive-foreground"
                    :aria-label="`Remove ${entries[0].file.name}`"
                    @click.stop="removeAt(0)"
                >
                    <Trash2Icon class="size-3.5" />
                </button>
                <div
                    class="absolute inset-x-0 bottom-0 bg-background/80 py-1 text-xs text-muted-foreground opacity-0 transition-opacity group-hover/thumb:opacity-100"
                >
                    Drop or browse to replace
                </div>
            </div>

            <div v-else class="grid w-full grid-cols-3 gap-2 sm:grid-cols-4">
                <img
                    v-for="url in showInitialPreviews ? initialPreviews : []"
                    :key="url"
                    :src="url"
                    alt="Current image"
                    class="aspect-square w-full rounded-md border border-input object-cover"
                />

                <div
                    v-for="(entry, index) in entries"
                    :key="entry.previewUrl"
                    class="relative aspect-square w-full overflow-hidden rounded-md border border-input"
                >
                    <img
                        :src="entry.previewUrl"
                        :alt="entry.file.name"
                        class="size-full object-cover"
                    />
                    <button
                        type="button"
                        class="absolute top-1 right-1 flex size-6 items-center justify-center rounded-full bg-background/90 text-destructive shadow-sm transition-colors hover:bg-destructive hover:text-destructive-foreground"
                        :aria-label="`Remove ${entry.file.name}`"
                        @click.stop="removeAt(index)"
                    >
                        <Trash2Icon class="size-3.5" />
                    </button>
                </div>

                <div
                    v-if="entries.length < effectiveMaxFiles"
                    class="flex aspect-square w-full items-center justify-center rounded-md border border-dashed border-input text-muted-foreground"
                >
                    <ImageUpIcon class="size-5" />
                </div>
            </div>

            <div
                v-if="processing"
                class="dropzone-sweep pointer-events-none absolute inset-0 flex items-center justify-center gap-2 bg-background/70 text-sm font-medium"
            >
                <Spinner class="size-4" />
                Uploading…
            </div>
        </div>

        <p
            v-if="message"
            class="flex items-center gap-1 text-sm text-destructive"
        >
            <TriangleAlertIcon class="size-3.5 shrink-0" />
            {{ message }}
        </p>
        <p v-else class="text-xs text-muted-foreground">
            {{ hint ?? defaultHint }}
        </p>
    </div>
</template>

<style scoped>
.dropzone-bracket {
    position: absolute;
    width: 0.9rem;
    height: 0.9rem;
    border-color: var(--color-primary);
    opacity: 0;
    transform: scale(0.6);
    transition:
        opacity 0.15s ease,
        transform 0.15s ease;
}

[data-dragging] .dropzone-bracket {
    opacity: 1;
    transform: scale(1);
}

.dropzone-bracket--tl {
    top: 0.4rem;
    left: 0.4rem;
    border-top: 2px solid;
    border-left: 2px solid;
    border-top-left-radius: 0.25rem;
}

.dropzone-bracket--tr {
    top: 0.4rem;
    right: 0.4rem;
    border-top: 2px solid;
    border-right: 2px solid;
    border-top-right-radius: 0.25rem;
}

.dropzone-bracket--bl {
    bottom: 0.4rem;
    left: 0.4rem;
    border-bottom: 2px solid;
    border-left: 2px solid;
    border-bottom-left-radius: 0.25rem;
}

.dropzone-bracket--br {
    bottom: 0.4rem;
    right: 0.4rem;
    border-bottom: 2px solid;
    border-right: 2px solid;
    border-bottom-right-radius: 0.25rem;
}

.dropzone-sweep::before {
    content: '';
    position: absolute;
    inset: -50% -50%;
    background: linear-gradient(
        115deg,
        transparent 40%,
        color-mix(in oklab, var(--color-primary) 18%, transparent) 50%,
        transparent 60%
    );
    animation: dropzone-sweep 1.6s ease-in-out infinite;
}

@keyframes dropzone-sweep {
    0% {
        transform: translateX(-35%);
    }
    100% {
        transform: translateX(35%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .dropzone-bracket {
        transition: opacity 0.15s ease;
        transform: none;
    }

    .dropzone-sweep::before {
        animation: none;
    }
}
</style>
