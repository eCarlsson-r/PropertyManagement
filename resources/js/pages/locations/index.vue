<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppContent from '@/components/AppContent.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import { index as locationsIndex, create as locationsCreate, edit as locationsEdit, destroy as locationsDestroy } from '@/routes/locations';

interface Location {
    id: number;
    address: string;
    city: string;
    country: string;
    district: string;
    postal: string;
    province: string;
    subdistrict: string;
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
    locations: PaginatedData<Location>;
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
                title: 'Locations',
                href: locationsIndex(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(locationsIndex.url(), { search: value || undefined }, {
        preserveState: true,
        replace: true,
    });
});

function deleteLocation(location: Location) {
    if (confirm(`Are you sure you want to delete "${location.address}"?`)) {
        router.delete(locationsDestroy.url(location.id));
    }
}
</script>

<template>

    <Head title="Locations List" />

    <AppContent>
        <Card>
            <CardHeader>
                <CardTitle>Locations List</CardTitle>
                <CardAction>
                    <div class="flex items-center gap-2">
                        <Input v-model="search" type="search" placeholder="Search locations..." class="w-64" />
                        <Button as-child>
                            <Link :href="locationsCreate.url()">
                                + New Location
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
                                <th class="px-4 py-3 text-left font-medium">Address</th>
                                <th class="px-4 py-3 text-left font-medium">Province</th>
                                <th class="px-4 py-3 text-left font-medium">City</th>
                                <th class="px-4 py-3 text-left font-medium">District</th>
                                <th class="px-4 py-3 text-left font-medium">Subdistrict</th>
                                <th class="px-4 py-3 text-left font-medium">Postal</th>
                                <th class="px-4 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="locations.data.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-muted-foreground">
                                    No locations found.
                                </td>
                            </tr>
                            <tr v-for="location in locations.data" :key="location.id"
                                class="border-b transition-colors hover:bg-muted/50">
                                <td class="px-4 py-3">{{ location.address }}</td>
                                <td class="px-4 py-3">{{ location.province }}</td>
                                <td class="px-4 py-3">{{ location.city }}</td>
                                <td class="px-4 py-3">{{ location.district }}</td>
                                <td class="px-4 py-3">{{ location.subdistrict }}</td>
                                <td class="px-4 py-3">{{ location.postal }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="sm" as-child>
                                            <Link :href="locationsEdit.url(location.id)">
                                                Edit
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="sm"
                                            class="text-destructive hover:text-destructive"
                                            @click="deleteLocation(location)">
                                            Delete
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="locations.last_page > 1" class="mt-4 flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Showing page {{ locations.current_page }} of {{ locations.last_page }}
                        ({{ locations.total }} total)
                    </p>
                    <div class="flex items-center gap-1">
                        <template v-for="link in locations.links" :key="link.label">
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
