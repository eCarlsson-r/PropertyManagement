<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import type { Unit } from '@/components/properties/UnitModal.vue';
import TenantForm from '@/components/tenants/TenantForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as tenantsIndex, store as tenantsStore } from '@/routes/tenants';
import type { Property } from '../properties/index.vue';

defineProps<{
    properties: Property[];
    units: Unit[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Tenants',
                href: tenantsIndex(),
            },
            {
                title: 'Add New Tenant',
            },
        ],
    },
});

const form = useForm({
    name: '',
    unit_id: 0,
    unit: {
        property_id: 0,
        room_id: 0
    },
    country_code: 'ID',
    mobile: '',
    email: '',
    cycle: 'monthly',
    birth_date: '',
    gender: '',
    marital_status: '',
    address: '',
    account_bank: '',
    account_number: '',
    account_owner: '',
    currency: 'IDR',
    notes: '',
    emergency_contact_name: '',
    emergency_contact_relationship: '',
    emergency_contact_country_code: 'ID',
    emergency_contact_mobile: '',
    deposit: 0,
    deposit_bank: '',
    deposit_number: '',
});

function submit() {
    form.post(tenantsStore.url());
}
</script>

<template>

    <Head title="Add New Tenant" />

    <Card>
        <CardHeader>
            <CardTitle>Add New Tenant</CardTitle>
        </CardHeader>
        <CardContent>
            <TenantForm v-model:form="form" :properties="properties" :units="units" submit-label="Add"
                @submit="submit" />
        </CardContent>
    </Card>
</template>
