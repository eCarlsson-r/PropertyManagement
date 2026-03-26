<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppContent from '@/components/AppContent.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as locationsIndex, update as locationsUpdate } from '@/routes/locations';
import LocationForm from '@/components/locations/LocationForm.vue';

interface Location {
    id: number;
    address: string;
    country: string;
    province: string;
    city: string;
    district: string;
    subdistrict: string;
    postal: string;
}

const props = defineProps<{
    location: Location;
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
            {
                title: 'Edit Location',
            },
        ],
    },
});

const form = useForm({
    id: props.location.id,
    address: props.location.address,
    country: props.location.country,
    province: props.location.province,
    city: props.location.city,
    district: props.location.district,
    subdistrict: props.location.subdistrict,
    postal: props.location.postal,
});

function submit() {
    form.put(locationsUpdate.url(props.location.id));
}
</script>

<template>

    <Head title="Edit Location" />

    <AppContent>
        <Card>
            <CardHeader>
                <CardTitle>Edit Location</CardTitle>
            </CardHeader>
            <CardContent>
                <LocationForm v-model:form="form" submit-label="Update" @submit="submit" />
            </CardContent>
        </Card>
    </AppContent>
</template>