<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import RoomForm from '@/components/rooms/RoomForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as roomsIndex, update as roomsUpdate } from '@/routes/rooms';
import type { Property } from '../properties/index.vue';
import type { Room } from '../rooms/index.vue';

const props = defineProps<{
    room: Room;
    properties: Property[];
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
            {
                title: 'Edit Room',
            },
        ],
    },
});

const form = useForm({
    id: props.room.id,
    name: props.room.name,
    property_id: props.room.property_id,
    area: props.room.area,
    daily_price: props.room.daily_price,
    weekly_price: props.room.weekly_price,
    monthly_price: props.room.monthly_price,
    annual_price: props.room.annual_price,
    notes: props.room.notes,
});

function submit() {
    form.put(roomsUpdate.url(props.room.id));
}
</script>

<template>

    <Head title="Edit Room" />

    <Card>
        <CardHeader>
            <CardTitle>Edit Room</CardTitle>
        </CardHeader>
        <CardContent>
            <RoomForm v-model:form="form" :properties="properties" submit-label="Update" @submit="submit" />
        </CardContent>
    </Card>
</template>