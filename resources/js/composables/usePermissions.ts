// composables/usePermissions.js
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();

    const auth = computed(() => page.props.auth);

    const hasRole = (roleName) => {
        return auth.value?.roles?.includes(roleName) ?? false;
    };

    const hasPermission = (permissionName) => {
        return auth.value?.permissions?.includes(permissionName) ?? false;
    };

    const hasAnyPermission = (permissions) => {
        return permissions.some((permission) => hasPermission(permission));
    };

    const hasAllPermissions = (permissions) => {
        return permissions.every((permission) => hasPermission(permission));
    };

    const can = (permissionName) => {
        return auth.value?.permissions?.includes(permissionName) ?? false;
    };

    return {
        user: computed(() => auth.value?.user ?? null),
        roles: computed(() => auth.value?.roles ?? []),
        permissions: computed(() => auth.value?.permissions ?? []),
        can,
        hasRole,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        isAdmin: computed(() => hasRole('admin')),
    };
}
