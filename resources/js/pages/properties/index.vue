<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
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
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-muted/10">
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Location</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="properties.data.length === 0">
                            <td colspan="3" class="px-4 py-8 text-center text-muted-foreground">
                                No properties found.
                            </td>
                        </tr>
                        <tr v-for="property in properties.data" :key="property.id"
                            class="hover:bg-muted/10">
                            <td class="px-4 py-3">{{ property.name }}</td>
                            <td class="px-4 py-3">{{ property.location?.address }}, {{ property.location?.city }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="propertiesEdit.url(property.id)">
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive"
                                        @click="deleteProperty(property)">
                                        Delete
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
