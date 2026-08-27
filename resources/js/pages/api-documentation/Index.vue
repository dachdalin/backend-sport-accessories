<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Check, Copy, Search, Send } from '@lucide/vue';
import { computed, reactive, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index as apiDocumentationIndex } from '@/routes/api-documentation';

interface Field {
    name: string;
    type: string;
    required: boolean;
    example: string | number | boolean | null;
    notes: string | null;
}

interface Endpoint {
    name: string;
    method: string;
    uri: string;
    params: string[];
    auth: 'none' | 'sanctum';
    throttle: string | null;
    group: string;
    summary: string | null;
    details: string | null;
    fields: Field[];
}

interface Group {
    label: string;
    endpoints: Endpoint[];
}

const props = defineProps<{
    groups: Group[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'API documentation',
                href: apiDocumentationIndex(),
            },
        ],
    },
});

const METHOD_STYLES: Record<string, string> = {
    GET: 'border-sky-600/30 bg-sky-600/10 text-sky-700 dark:text-sky-400',
    POST: 'border-emerald-600/30 bg-emerald-600/10 text-emerald-700 dark:text-emerald-400',
    PUT: 'border-amber-600/30 bg-amber-600/10 text-amber-700 dark:text-amber-400',
    PATCH: 'border-amber-600/30 bg-amber-600/10 text-amber-700 dark:text-amber-400',
    DELETE: 'border-destructive/30 bg-destructive/10 text-destructive',
};

function methodClass(method: string): string {
    return METHOD_STYLES[method] ?? 'border-border bg-muted text-foreground';
}

const allEndpoints = computed(() => props.groups.flatMap((group) => group.endpoints));

const search = ref('');

const filteredGroups = computed<Group[]>(() => {
    const term = search.value.trim().toLowerCase();

    if (!term) {
        return props.groups;
    }

    return props.groups
        .map((group) => ({
            label: group.label,
            endpoints: group.endpoints.filter(
                (endpoint) =>
                    endpoint.uri.toLowerCase().includes(term) ||
                    (endpoint.summary ?? '').toLowerCase().includes(term),
            ),
        }))
        .filter((group) => group.endpoints.length > 0);
});

const selectedName = ref<string | null>(allEndpoints.value[0]?.name ?? null);

const selected = computed<Endpoint | null>(
    () => allEndpoints.value.find((endpoint) => endpoint.name === selectedName.value) ?? null,
);

const pathParamValues = reactive<Record<string, string>>({});
const queryString = ref('');
const bodyText = ref('');
const bearerToken = ref('');
const sending = ref(false);
const requestError = ref<string | null>(null);
const response = ref<{ status: number; statusText: string; ms: number; body: string; ok: boolean } | null>(null);
const curlCopied = ref(false);

function defaultBody(fields: Field[]): string {
    const writable = fields.filter((field) => field.type !== 'file');

    if (writable.length === 0) {
        return '';
    }

    const draft: Record<string, unknown> = {};

    for (const field of writable) {
        if (field.example !== null) {
            draft[field.name] = field.example;
        } else if (field.type === 'integer' || field.type === 'number') {
            draft[field.name] = 0;
        } else if (field.type === 'boolean') {
            draft[field.name] = false;
        } else {
            draft[field.name] = '';
        }
    }

    return JSON.stringify(draft, null, 2);
}

watch(
    selected,
    (endpoint) => {
        for (const key of Object.keys(pathParamValues)) {
            delete pathParamValues[key];
        }

        endpoint?.params.forEach((param) => {
            pathParamValues[param] = '';
        });

        queryString.value = '';
        bodyText.value = endpoint ? defaultBody(endpoint.fields) : '';
        requestError.value = null;
        response.value = null;
    },
    { immediate: true },
);

function resolvedUrl(endpoint: Endpoint): string {
    let uri = endpoint.uri;

    for (const param of endpoint.params) {
        uri = uri.replace(`{${param}}`, encodeURIComponent(pathParamValues[param] || `{${param}}`));
    }

    const query = queryString.value.trim().replace(/^\?/, '');

    return window.location.origin + uri + (query ? `?${query}` : '');
}

function hasBody(endpoint: Endpoint): boolean {
    return endpoint.method !== 'GET' && endpoint.method !== 'HEAD' && bodyText.value.trim() !== '';
}

async function send() {
    const endpoint = selected.value;

    if (!endpoint) {
        return;
    }

    requestError.value = null;
    response.value = null;

    if (hasBody(endpoint)) {
        try {
            JSON.parse(bodyText.value);
        } catch {
            requestError.value = 'Body must be valid JSON.';

            return;
        }
    }

    sending.value = true;

    const headers: Record<string, string> = { Accept: 'application/json' };

    if (bearerToken.value.trim()) {
        headers.Authorization = `Bearer ${bearerToken.value.trim()}`;
    }

    if (hasBody(endpoint)) {
        headers['Content-Type'] = 'application/json';
    }

    const start = performance.now();

    try {
        const res = await fetch(resolvedUrl(endpoint), {
            method: endpoint.method,
            headers,
            body: hasBody(endpoint) ? bodyText.value : undefined,
        });
        const ms = Math.round(performance.now() - start);
        const text = await res.text();
        let pretty = text;

        try {
            pretty = JSON.stringify(JSON.parse(text), null, 2);
        } catch {
            // Not JSON — show the raw response body as-is.
        }

        response.value = { status: res.status, statusText: res.statusText, ms, body: pretty, ok: res.ok };
    } catch (e) {
        requestError.value =
            e instanceof Error ? e.message : 'The request failed. Check your connection and the URL.';
    } finally {
        sending.value = false;
    }
}

async function copyCurl() {
    const endpoint = selected.value;

    if (!endpoint) {
        return;
    }

    const lines = [`curl -X ${endpoint.method} '${resolvedUrl(endpoint)}'`, `-H 'Accept: application/json'`];

    if (bearerToken.value.trim()) {
        lines.push(`-H 'Authorization: Bearer ${bearerToken.value.trim()}'`);
    }

    if (hasBody(endpoint)) {
        lines.push(`-H 'Content-Type: application/json'`, `-d '${bodyText.value.replace(/'/g, "'\\''")}'`);
    }

    await navigator.clipboard.writeText(lines.join(' \\\n  '));
    toast.success('cURL command copied.');
    curlCopied.value = true;
    setTimeout(() => (curlCopied.value = false), 1500);
}
</script>

<template>
    <Head title="API documentation" />

    <div class="flex flex-col gap-6">
        <Heading
            title="API documentation"
            description="Reference and live testing console for the customer-facing API (api/v1)."
        />

        <div
            class="flex flex-col overflow-hidden rounded-xl border border-sidebar-border/70 md:h-[calc(100vh-14rem)] md:min-h-[32rem] md:flex-row dark:border-sidebar-border"
        >
            <aside
                class="flex w-full shrink-0 flex-col border-sidebar-border/70 md:h-full md:w-80 md:border-r dark:border-sidebar-border"
            >
                <div class="border-b border-sidebar-border/70 p-3 dark:border-sidebar-border">
                    <div class="relative">
                        <Search
                            class="absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Search endpoints…"
                            class="pl-8"
                        />
                    </div>
                </div>

                <nav class="flex-1 overflow-y-auto">
                    <div v-if="filteredGroups.length === 0" class="p-4 text-sm text-muted-foreground">
                        No endpoints match "{{ search }}".
                    </div>

                    <div v-for="group in filteredGroups" :key="group.label">
                        <p
                            class="sticky top-0 bg-background px-3 py-1.5 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                        >
                            {{ group.label }}
                        </p>
                        <button
                            v-for="endpoint in group.endpoints"
                            :key="endpoint.name"
                            type="button"
                            class="flex w-full flex-col gap-0.5 border-l-2 px-3 py-2 text-left transition-colors hover:bg-accent"
                            :class="
                                selectedName === endpoint.name
                                    ? 'border-primary bg-accent'
                                    : 'border-transparent'
                            "
                            @click="selectedName = endpoint.name"
                        >
                            <span class="flex items-center gap-2">
                                <span
                                    class="w-14 shrink-0 rounded border px-1.5 py-0.5 text-center font-mono text-[10px] font-semibold"
                                    :class="methodClass(endpoint.method)"
                                    >{{ endpoint.method }}</span
                                >
                                <span class="truncate font-mono text-xs">{{ endpoint.uri }}</span>
                            </span>
                            <span
                                v-if="endpoint.summary"
                                class="truncate pl-16 text-xs text-muted-foreground"
                                >{{ endpoint.summary }}</span
                            >
                        </button>
                    </div>
                </nav>
            </aside>

            <section v-if="selected" class="flex-1 overflow-y-auto p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="rounded border px-2 py-1 font-mono text-xs font-semibold"
                        :class="methodClass(selected.method)"
                        >{{ selected.method }}</span
                    >
                    <code class="font-mono text-sm">{{ selected.uri }}</code>
                    <Badge :variant="selected.auth === 'sanctum' ? 'default' : 'outline'">
                        {{ selected.auth === 'sanctum' ? 'Requires bearer token' : 'No authentication' }}
                    </Badge>
                    <Badge v-if="selected.throttle" variant="outline">Rate limited</Badge>
                </div>

                <p v-if="selected.summary" class="mt-3 text-sm">{{ selected.summary }}</p>
                <p v-if="selected.details" class="mt-2 text-sm whitespace-pre-line text-muted-foreground">
                    {{ selected.details }}
                </p>

                <div v-if="selected.fields.length > 0" class="mt-6">
                    <h3 class="text-sm font-medium">Request body</h3>
                    <div class="mt-2 overflow-x-auto rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-sidebar-border/70 text-xs text-muted-foreground dark:border-sidebar-border">
                                <tr>
                                    <th class="px-3 py-2 font-medium">Field</th>
                                    <th class="px-3 py-2 font-medium">Type</th>
                                    <th class="px-3 py-2 font-medium">Required</th>
                                    <th class="px-3 py-2 font-medium">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="field in selected.fields"
                                    :key="field.name"
                                    class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                                >
                                    <td class="px-3 py-2 font-mono text-xs">{{ field.name }}</td>
                                    <td class="px-3 py-2 text-muted-foreground">{{ field.type }}</td>
                                    <td class="px-3 py-2">{{ field.required ? 'Yes' : 'No' }}</td>
                                    <td class="px-3 py-2 text-muted-foreground">{{ field.notes ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex items-center justify-between border-b border-sidebar-border/70 px-4 py-2.5 dark:border-sidebar-border">
                        <h3 class="text-sm font-medium">Console</h3>
                        <Button variant="ghost" size="sm" @click="copyCurl">
                            <Check v-if="curlCopied" class="text-emerald-600" />
                            <Copy v-else />
                            Copy as cURL
                        </Button>
                    </div>

                    <div class="space-y-4 p-4">
                        <div v-if="selected.params.length > 0" class="grid gap-3 sm:grid-cols-2">
                            <div v-for="param in selected.params" :key="param" class="grid gap-1.5">
                                <Label :for="`param-${param}`" class="font-mono text-xs"
                                    >{{ '{' + param + '}' }}</Label
                                >
                                <Input :id="`param-${param}`" v-model="pathParamValues[param]" />
                            </div>
                        </div>

                        <div class="grid gap-1.5">
                            <Label for="query-string">Query string (optional)</Label>
                            <Input
                                id="query-string"
                                v-model="queryString"
                                placeholder="page=2&discounted=1"
                                class="font-mono text-xs"
                            />
                        </div>

                        <div class="grid gap-1.5">
                            <Label for="bearer-token">Bearer token (optional)</Label>
                            <Input
                                id="bearer-token"
                                v-model="bearerToken"
                                placeholder="Paste a customer API token to test authenticated endpoints"
                                class="font-mono text-xs"
                            />
                        </div>

                        <div v-if="selected.method !== 'GET' && selected.method !== 'HEAD'" class="grid gap-1.5">
                            <Label for="body">Body (JSON)</Label>
                            <Textarea
                                id="body"
                                v-model="bodyText"
                                rows="8"
                                class="min-h-32 bg-zinc-950 font-mono text-xs text-zinc-100 dark:bg-zinc-950"
                            />
                        </div>

                        <div class="flex items-center gap-3">
                            <Button type="button" :disabled="sending" @click="send">
                                <Send />
                                {{ sending ? 'Sending…' : 'Send request' }}
                            </Button>
                            <code class="truncate text-xs text-muted-foreground">{{ resolvedUrl(selected) }}</code>
                        </div>

                        <p v-if="requestError" class="text-sm text-destructive">{{ requestError }}</p>

                        <div v-if="response" class="overflow-hidden rounded-lg border border-zinc-800">
                            <div class="flex items-center gap-3 bg-zinc-900 px-3 py-2 text-xs text-zinc-300">
                                <span
                                    class="font-mono font-semibold"
                                    :class="response.ok ? 'text-emerald-400' : 'text-red-400'"
                                    >{{ response.status }} {{ response.statusText }}</span
                                >
                                <span>{{ response.ms }}ms</span>
                            </div>
                            <pre class="max-h-96 overflow-auto bg-zinc-950 p-3 font-mono text-xs text-zinc-100">{{
                                response.body || '(empty response body)'
                            }}</pre>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
