<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppContent from '@/components/AppContent.vue';
import PropertyForm from '@/components/properties/PropertyForm.vue';
import type { Location } from '@/components/properties/PropertyForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as propertiesIndex, store as propertiesStore } from '@/routes/properties';

defineProps<{
    locations: Location[];
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
            {
                title: 'Add New Property',
            },
        ],
    },
});

const form = useForm({
    name: '',
    currency: '',
    mobile: '',
    location_id: '',
    notes: '',
});

function submit() {
    form.post(propertiesStore.url());
}
</script>

<template>

    <Head title="Add New Property" />

    <AppContent>
        <Card>
            <CardHeader>
                <CardTitle>Add New Property</CardTitle>
            </CardHeader>
            <CardContent>
                <PropertyForm v-model:form="form" :locations="locations" submit-label="Add" @submit="submit" />
            </CardContent>
        </Card>
    </AppContent>
</template>
