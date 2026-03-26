<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppContent from '@/components/AppContent.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as locationsIndex, store as locationsStore } from '@/routes/locations';
import LocationForm from '@/components/locations/LocationForm.vue';

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
                title: 'Add New Location',
            },
        ],
    },
});

const form = useForm({
    id: 0,
    address: '',
    country: '',
    province: '',
    city: '',
    district: '',
    subdistrict: '',
    postal: '',
});

function submit() {
    form.post(locationsStore.url());
}
</script>

<template>

    <Head title="Add New Location" />

    <AppContent>
        <Card>
            <CardHeader>
                <CardTitle>Add New Location</CardTitle>
            </CardHeader>
            <CardContent>
                <LocationForm v-model:form="form" submit-label="Add" @submit="submit" />
            </CardContent>
        </Card>
    </AppContent>
</template>
