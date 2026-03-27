<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import type { Location } from '@/components/properties/LocationModal.vue';
import PropertyForm from '@/components/properties/PropertyForm.vue';
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
    location: {
        address: '',
        country: '',
        province: '',
        city: '',
        district: '',
        subdistrict: '',
        postal: '',
    },
    notes: '',
    owner_name: '',
    owner_country_code: '',
    owner_mobile: '',
    manager_name: '',
    manager_country_code: '',
    manager_mobile: '',
});

function submit() {
    form.post(propertiesStore.url());
}
</script>

<template>

    <Head title="Add New Property" />

    <Card>
        <CardHeader>
            <CardTitle>Add New Property</CardTitle>
        </CardHeader>
        <CardContent>
            <PropertyForm v-model:form="form" :locations="locations" submit-label="Add" @submit="submit" />
        </CardContent>
    </Card>
</template>
