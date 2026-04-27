<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes';
import { index as tenantsIndex, create as tenantsCreate, edit as tenantsEdit, destroy as tenantsDestroy } from '@/routes/tenants';
import type { Property } from '../properties/index.vue';
import type { Room } from '../rooms/index.vue';

export interface Tenant {
    id: number;
    name: string;
    country_code: string;
    mobile: string;
    email?: string;
    unit_id: number;
    cycle: string;
    birth_date?: string;
    gender?: string;
    marital_status?: string;
    address?: string;
    account_bank?: string;
    account_number?: string;
    account_owner?: string;
    currency: string;
    notes?: string;
    emergency_contact_name: string;
    emergency_contact_relationship: string;
    emergency_contact_country_code: string;
    emergency_contact_mobile: string;
    deposit?: number;
    deposit_bank?: string;
    deposit_number?: string;
    unit: {
        property: Property;
        room: Room;
    }
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

    <Card>
        <CardHeader>
            <CardTitle>Tenants List</CardTitle>
            <CardAction>
                <div class="flex items-center gap-2">
                    <Select v-model="option">
                        <SelectTrigger id="option">
                            <SelectValue placeholder="Select Option" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="least-days">Least Remaining Days</SelectItem>
                            <SelectItem value="most-days">Most Remaining Days</SelectItem>
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
            <div class="overflow-x-auto rounded-md">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Property</TableHead>
                            <TableHead>Room</TableHead>
                            <TableHead>Billing Cycle</TableHead>
                            <TableHead class="text-center">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="tenants.data.length === 0">
                            <TableCell colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                                No tenants found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="tenant in tenants.data" :key="tenant.id">
                            <TableCell>{{ tenant.name }}</TableCell>
                            <TableCell>{{ tenant.unit.property?.name }}</TableCell>
                            <TableCell>{{ tenant.unit.room?.name }}</TableCell>
                            <TableCell>{{ tenant.cycle }}</TableCell>
                            <TableCell class="flex items-center justify-center gap-1">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="tenantsEdit.url(tenant.id)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button variant="destructive" size="sm" @click="deleteTenant(tenant)">
                                    Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
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
</template>
