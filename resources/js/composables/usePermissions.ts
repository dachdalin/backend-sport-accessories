import { usePage } from '@inertiajs/vue3';
import type { ComputedRef, DeepReadonly } from 'vue';
import { computed, readonly } from 'vue';

export type UsePermissionsReturn = {
    permissions: DeepReadonly<ComputedRef<string[]>>;
    can: (permission: string) => boolean;
};

const page = usePage();
const permissionsReactive = computed(() => page.props.auth.permissions);

export function usePermissions(): UsePermissionsReturn {
    function can(permission: string): boolean {
        return permissionsReactive.value.includes(permission);
    }

    return {
        permissions: readonly(permissionsReactive),
        can,
    };
}
