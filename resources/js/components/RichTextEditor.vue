<script setup lang="ts">
import {
    BoldIcon,
    Heading2Icon,
    Heading3Icon,
    ItalicIcon,
    Link2Icon,
    ListIcon,
    ListOrderedIcon,
    QuoteIcon,
    Redo2Icon,
    StrikethroughIcon,
    Undo2Icon,
} from '@lucide/vue';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { ref, useId, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

/**
 * Global rich text field. Tiptap owns the contenteditable area; a hidden,
 * named `<textarea>` mirrors its HTML so the field still serializes through
 * Inertia's `<Form>` like any other input.
 */
const props = withDefaults(
    defineProps<{
        name: string;
        id?: string;
        label?: string;
        defaultValue?: string;
        placeholder?: string;
        required?: boolean;
        disabled?: boolean;
        error?: string;
    }>(),
    {
        id: undefined,
        label: undefined,
        defaultValue: '',
        placeholder: undefined,
        required: false,
        disabled: false,
        error: undefined,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', html: string): void;
}>();

const fieldId = props.id ?? `editor-${useId()}`;
const html = ref(props.defaultValue);

const editor = useEditor({
    content: props.defaultValue,
    editable: !props.disabled,
    extensions: [
        StarterKit,
        Link.configure({
            openOnClick: false,
            autolink: true,
            defaultProtocol: 'https',
        }),
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
    ],
    editorProps: {
        attributes: {
            class: 'tiptap-content min-h-40 px-3 py-2 text-base outline-none md:text-sm',
        },
    },
    onUpdate: ({ editor: instance }) => {
        html.value = instance.getHTML();
        emit('update:modelValue', html.value);
    },
});

watch(
    () => props.disabled,
    (disabled) => editor.value?.setEditable(!disabled),
);

function toggleLink(): void {
    const previous = editor.value?.getAttributes('link').href as
        string | undefined;
    const url = window.prompt('Link URL', previous ?? 'https://');

    if (url === null) {
        return;
    }

    if (url === '') {
        editor.value?.chain().focus().unsetLink().run();

        return;
    }

    editor.value?.chain().focus().setLink({ href: url }).run();
}
</script>

<template>
    <div class="grid gap-2">
        <Label v-if="label" :for="fieldId">{{ label }}</Label>

        <div
            :class="
                cn(
                    'rounded-md border border-input shadow-xs transition-colors',
                    'focus-within:border-ring focus-within:ring-[3px] focus-within:ring-ring/50',
                    disabled && 'cursor-not-allowed opacity-50',
                )
            "
        >
            <div
                class="flex flex-wrap items-center gap-0.5 border-b border-input p-1"
            >
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="editor?.isActive('bold') || undefined"
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Bold"
                    @click="editor?.chain().focus().toggleBold().run()"
                >
                    <BoldIcon />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="editor?.isActive('italic') || undefined"
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Italic"
                    @click="editor?.chain().focus().toggleItalic().run()"
                >
                    <ItalicIcon />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="editor?.isActive('strike') || undefined"
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Strikethrough"
                    @click="editor?.chain().focus().toggleStrike().run()"
                >
                    <StrikethroughIcon />
                </Button>

                <div class="mx-1 h-5 w-px bg-border" />

                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="
                        editor?.isActive('heading', { level: 2 }) || undefined
                    "
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Heading 2"
                    @click="
                        editor
                            ?.chain()
                            .focus()
                            .toggleHeading({ level: 2 })
                            .run()
                    "
                >
                    <Heading2Icon />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="
                        editor?.isActive('heading', { level: 3 }) || undefined
                    "
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Heading 3"
                    @click="
                        editor
                            ?.chain()
                            .focus()
                            .toggleHeading({ level: 3 })
                            .run()
                    "
                >
                    <Heading3Icon />
                </Button>

                <div class="mx-1 h-5 w-px bg-border" />

                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="editor?.isActive('bulletList') || undefined"
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Bullet list"
                    @click="editor?.chain().focus().toggleBulletList().run()"
                >
                    <ListIcon />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="editor?.isActive('orderedList') || undefined"
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Numbered list"
                    @click="editor?.chain().focus().toggleOrderedList().run()"
                >
                    <ListOrderedIcon />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="editor?.isActive('blockquote') || undefined"
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Quote"
                    @click="editor?.chain().focus().toggleBlockquote().run()"
                >
                    <QuoteIcon />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled"
                    :data-active="editor?.isActive('link') || undefined"
                    class="data-[active]:bg-accent data-[active]:text-accent-foreground"
                    aria-label="Link"
                    @click="toggleLink"
                >
                    <Link2Icon />
                </Button>

                <div class="mx-1 h-5 w-px bg-border" />

                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled || !editor?.can().undo()"
                    aria-label="Undo"
                    @click="editor?.chain().focus().undo().run()"
                >
                    <Undo2Icon />
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon-sm"
                    :disabled="disabled || !editor?.can().redo()"
                    aria-label="Redo"
                    @click="editor?.chain().focus().redo().run()"
                >
                    <Redo2Icon />
                </Button>
            </div>

            <EditorContent :editor="editor" />
        </div>

        <textarea
            :id="fieldId"
            :name="name"
            :value="html"
            :required="required"
            tabindex="-1"
            class="sr-only"
            aria-hidden="true"
        />

        <InputError :message="error" />
    </div>
</template>

<style scoped>
:deep(.tiptap-content p) {
    margin: 0.5em 0;
}

:deep(.tiptap-content p:first-child) {
    margin-top: 0;
}

:deep(.tiptap-content p:last-child) {
    margin-bottom: 0;
}

:deep(.tiptap-content h2) {
    margin: 0.8em 0 0.4em;
    font-size: 1.25em;
    font-weight: 600;
}

:deep(.tiptap-content h3) {
    margin: 0.8em 0 0.4em;
    font-size: 1.1em;
    font-weight: 600;
}

:deep(.tiptap-content ul),
:deep(.tiptap-content ol) {
    margin: 0.5em 0;
    padding-left: 1.5em;
}

:deep(.tiptap-content ul) {
    list-style: disc;
}

:deep(.tiptap-content ol) {
    list-style: decimal;
}

:deep(.tiptap-content blockquote) {
    margin: 0.5em 0;
    border-left: 3px solid var(--color-border);
    padding-left: 1em;
    color: var(--color-muted-foreground);
}

:deep(.tiptap-content a) {
    color: var(--color-primary);
    text-decoration: underline;
    text-underline-offset: 2px;
}

:deep(.tiptap-content p.is-editor-empty:first-child::before) {
    float: left;
    height: 0;
    color: var(--color-muted-foreground);
    content: attr(data-placeholder);
    pointer-events: none;
}
</style>
