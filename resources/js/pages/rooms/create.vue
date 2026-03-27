<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import RoomForm from '@/components/rooms/RoomForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as roomsIndex, store as roomsStore } from '@/routes/rooms';
import type { Property } from '../properties/index.vue';

defineProps<{
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
                title: 'Add New Room',
            },
        ],
    },
});

const form = useForm({
    name: '',
    property_id: 0,
    area: 0,
    daily_price: 0,
    weekly_price: 0,
    monthly_price: 0,
    annual_price: 0,
    notes: '',
});

function submit() {
    form.post(roomsStore.url());
}
</script>

<template>

    <Head title="Add New Room" />

    <Card>
        <CardHeader>
            <CardTitle>Add New Room</CardTitle>
        </CardHeader>
        <CardContent>
            <RoomForm v-model:form="form" :properties="properties" submit-label="Add" @submit="submit" />
        </CardContent>
    </Card>
</template>
