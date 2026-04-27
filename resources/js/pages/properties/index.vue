<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes';
import { index as propertiesIndex, create as propertiesCreate, edit as propertiesEdit, destroy as propertiesDestroy } from '@/routes/properties';

export interface Property {
    id: number;
    name: string;
    currency: string;
    mobile: string;
    location_id: number;
    notes: string;
    location?: {
        address: string;
        city: string;
        country: string;
        district: string;
        id: number;
        postal: string;
        province: string;
        subdistrict: string;
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
    properties: PaginatedData<Property>;
    filters: {
        search?: string;
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
                title: 'Properties',
                href: propertiesIndex(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(propertiesIndex.url(), { search: value || undefined }, {
        preserveState: true,
        replace: true,
    });
});

function deleteProperty(property: Property) {
    if (confirm(`Are you sure you want to delete "${property.name}"?`)) {
        router.delete(propertiesDestroy.url(property.id));
    }
}
</script>

<template>

    <Head title="Properties List" />

    <Card>
        <CardHeader>
            <CardTitle>Properties List</CardTitle>
            <CardAction>
                <div class="flex items-center gap-2">
                    <Input v-model="search" type="search" placeholder="Search properties..." class="w-64" />
                    <Button as-child>
                        <Link :href="propertiesCreate.url()">
                            + New Property
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
                            <TableHead>Location</TableHead>
                            <TableHead class="text-center">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="properties.data.length === 0">
                            <TableCell colspan="3" class="px-4 py-8 text-center text-muted-foreground">
                                No properties found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="property in properties.data" :key="property.id">
                            <TableCell>{{ property.name }}</TableCell>
                            <TableCell>{{ property.location?.address }}, {{ property.location?.city }}</TableCell>
                            <TableCell class="flex items-center justify-center gap-1">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="propertiesEdit.url(property.id)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button variant="destructive" size="sm" @click="deleteProperty(property)">
                                    Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div v-if="properties.last_page > 1" class="mt-4 flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing page {{ properties.current_page }} of {{ properties.last_page }}
                    ({{ properties.total }} total)
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in properties.links" :key="link.label">
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
