<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes';
import { index as roomsIndex, create as roomsCreate, edit as roomsEdit, destroy as roomsDestroy } from '@/routes/rooms';

export interface Room {
    id: number;
    property_id: number;
    name: string;
    area: number;
    daily_price: number;
    weekly_price: number;
    monthly_price: number;
    annual_price: number;
    notes?: string;
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
    rooms: PaginatedData<Room>;
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
                title: 'Rooms',
                href: roomsIndex(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(roomsIndex.url(), { search: value || undefined }, {
        preserveState: true,
        replace: true,
    });
});

function deleteRoom(room: Room) {
    if (confirm(`Are you sure you want to delete "${room.name}"?`)) {
        router.delete(roomsDestroy.url(room.id));
    }
}
</script>

<template>

    <Head title="Rooms List" />

    <Card>
        <CardHeader>
            <CardTitle>Rooms List</CardTitle>
            <CardAction>
                <div class="flex items-center gap-2">
                    <Input v-model="search" type="search" placeholder="Search rooms..." class="w-64" />
                    <Button as-child>
                        <Link :href="roomsCreate.url()">
                            + New Room
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
                            <TableHead>Area</TableHead>
                            <TableHead>Daily Price</TableHead>
                            <TableHead>Weekly Price</TableHead>
                            <TableHead>Monthly Price</TableHead>
                            <TableHead>Annual Price</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="rooms.data.length === 0">
                            <TableCell colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                No rooms found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="room in rooms.data" :key="room.id">
                            <TableCell>{{ room.name }}</TableCell>
                            <TableCell>{{ room.area }}</TableCell>
                            <TableCell>{{ room.daily_price.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }) }}</TableCell>
                            <TableCell>{{ room.weekly_price.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }) }}</TableCell>
                            <TableCell>{{ room.monthly_price.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }) }}</TableCell>
                            <TableCell>{{ room.annual_price.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }) }}</TableCell>
                            <TableCell class="flex items-center justify-center gap-1">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="roomsEdit.url(room.id)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button variant="destructive" size="sm" @click="deleteRoom(room)">
                                    Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div v-if="rooms.last_page > 1" class="mt-4 flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing page {{ rooms.current_page }} of {{ rooms.last_page }}
                    ({{ rooms.total }} total)
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in rooms.links" :key="link.label">
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
