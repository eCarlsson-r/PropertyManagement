<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import type { Unit } from '@/components/properties/UnitModal.vue';
import TenantForm from '@/components/tenants/TenantForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as tenantsIndex, update as tenantsUpdate } from '@/routes/tenants';
import type { Property } from '../properties/index.vue';
import type { Tenant } from '../tenants/index.vue';

const props = defineProps<{
    tenant: Tenant;
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
                title: 'Edit Tenant',
            },
        ],
    },
});

const form = useForm({
    id: props.tenant.id,
    name: props.tenant.name,
    unit_id: props.tenant.unit_id,
    unit: {
        property_id: props.tenant.unit.property.id,
        room_id: props.tenant.unit.room.id
    },
    country_code: props.tenant.country_code,
    mobile: props.tenant.mobile,
    email: props.tenant.email,
    cycle: props.tenant.cycle,
    birth_date: props.tenant.birth_date,
    gender: props.tenant.gender,
    marital_status: props.tenant.marital_status,
    address: props.tenant.address,
    account_bank: props.tenant.account_bank,
    account_number: props.tenant.account_number,
    account_owner: props.tenant.account_owner,
    currency: props.tenant.currency,
    notes: props.tenant.notes,
    emergency_contact_name: props.tenant.emergency_contact_name,
    emergency_contact_relationship: props.tenant.emergency_contact_relationship,
    emergency_contact_country_code: props.tenant.emergency_contact_country_code,
    emergency_contact_mobile: props.tenant.emergency_contact_mobile,
    deposit: props.tenant.deposit,
    deposit_bank: props.tenant.deposit_bank,
    deposit_number: props.tenant.deposit_number,
});

function submit() {
    form.put(tenantsUpdate.url(props.tenant.id));
}
</script>

<template>

    <Head title="Edit Tenant" />

    <Card>
        <CardHeader>
            <CardTitle>Edit Tenant</CardTitle>
        </CardHeader>
        <CardContent>
            <TenantForm v-model:form="form" :properties="properties" :units="units" submit-label="Update"
                @submit="submit" />
        </CardContent>
    </Card>
</template>