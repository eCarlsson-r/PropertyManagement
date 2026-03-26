<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppContent from '@/components/AppContent.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { dashboard } from '@/routes';
import { index as tenantsIndex, create as tenantsCreate, edit as tenantsEdit, destroy as tenantsDestroy } from '@/routes/tenants';
import type { Property } from '../properties/index.vue';
import type { Room } from '../rooms/index.vue';

export interface Tenant {
    id: number;
    name: string;
    email: string;
    phone: string;
    mobile: string;
    unit_id: number;
    cycle: string;
    deposit: number;
    notes: string;
    unit: {
        property?: Property;
        room?: Room;
    };
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

const props = defineProps<{
    tenants: PaginatedData<Tenant>;
    filters: {
        search?: string;
        option?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Tenants',
                href: tenantsIndex(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');
const option = ref(props.filters.option ?? 'az');

watch(search, (value) => {
    router.get(tenantsIndex.url(), { search: value || undefined }, {
        preserveState: true,
        replace: true,
    });
});

function deleteTenant(tenant: Tenant) {
    if (confirm(`Are you sure you want to delete "${tenant.name}"?`)) {
        router.delete(tenantsDestroy.url(tenant.id));
    }
}
</script>

<template>

    <Head title="Tenants List" />

    <AppContent>
        <Card>
            <CardHeader>
                <CardTitle>Tenants List</CardTitle>
                <CardAction>
                    <div class="flex items-center gap-2">
                        <Select v-model="option">
                            <SelectTrigger id="option">
                                <SelectValue placeholder="Pilih Opsi" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="least-days">Sisa Hari Terkecil</SelectItem>
                                <SelectItem value="most-days">Sisa Hari Terbesar</SelectItem>
                                <SelectItem value="az">A - Z</SelectItem>
                                <SelectItem value="za">Z - A</SelectItem>
                                <SelectItem value="az-unit">A - Z Unit</SelectItem>
                                <SelectItem value="za-unit">Z - A Unit</SelectItem>
                            </SelectContent>
                        </Select>
                        <Input v-model="search" type="search" placeholder="Search tenants..." class="w-64" />
                        <Button as-child>
                            <Link :href="tenantsCreate.url()">
                                + New Tenant
                            </Link>
                        </Button>
                        <Button variant="outline" @click="router.reload()">
                            ↻ Refresh
                        </Button>
                    </div>
                </CardAction>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-3 text-left font-medium">Name</th>
                                <th class="px-4 py-3 text-left font-medium">Property</th>
                                <th class="px-4 py-3 text-left font-medium">Room</th>
                                <th class="px-4 py-3 text-left font-medium">Billing Cycle</th>
                                <th class="px-4 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="tenants.data.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-muted-foreground">
                                    No tenants found.
                                </td>
                            </tr>
                            <tr v-for="tenant in tenants.data" :key="tenant.id"
                                class="border-b transition-colors hover:bg-muted/50">
                                <td class="px-4 py-3">{{ tenant.name }}</td>
                                <td class="px-4 py-3">{{ tenant.unit.property?.name }}</td>
                                <td class="px-4 py-3">{{ tenant.unit.room?.name }}</td>
                                <td class="px-4 py-3">{{ tenant.cycle }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="sm" as-child>
                                            <Link :href="tenantsEdit.url(tenant.id)">
                                                Edit
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="sm"
                                            class="text-destructive hover:text-destructive"
                                            @click="deleteTenant(tenant)">
                                            Delete
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="tenants.last_page > 1" class="mt-4 flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Showing page {{ tenants.current_page }} of {{ tenants.last_page }}
                        ({{ tenants.total }} total)
                    </p>
                    <div class="flex items-center gap-1">
                        <template v-for="link in tenants.links" :key="link.label">
                            <Button v-if="link.url" variant="outline" size="sm"
                                :class="{ 'bg-primary text-primary-foreground': link.active }" as-child>
                                <Link :href="link.url">{{ link.label }}</Link>
                            </Button>
                            <Button v-else variant="outline" size="sm" disabled>{{ link.label }}</Button>
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>
    </AppContent>
</template>
