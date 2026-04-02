<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import type { Unit } from '@/components/properties/UnitModal.vue';
import ReceiptForm from '@/components/receipts/ReceiptForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as receiptsIndex, store as receiptsStore } from '@/routes/receipts';
import type { Property } from '../properties/index.vue';
import type { Tenant } from '../tenants/index.vue';

defineProps<{
    tenants: Tenant[];
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
                title: 'Receipts',
                href: receiptsIndex(),
            },
            {
                title: 'New Receipt',
            },
        ],
    },
});

const form = useForm({
    tenant_id: '',
    property_id: '',
    unit_id: '',
    adults: 1,
    children: 0,
    babies: 0,
    pets: 0,
    receipt_duration: 1,
    receipt_cycle: 'monthly',
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
    discount_type: 'percentage' as const,
    discount_amount: 0,
    discount_percent: 0,
    tax: 0,
    deposit: 0,
    total: 0,
    down_payment: 0,
    remaining: 0,
    payment_status: 'paid' as const,
    payment_method: 'cash' as const,
    notes: '',
    reminder: '1-day-advance',
});

function submit() {
    form.post(receiptsStore.url());
}
</script>

<template>

    <Head title="New Receipt" />

    <Card>
        <CardHeader>
            <CardTitle>Create New Receipt</CardTitle>
        </CardHeader>
        <CardContent>
            <ReceiptForm v-model:form="form" :tenants="tenants" :properties="properties" :units="units"
                submit-label="Create Receipt" @submit="submit" />
        </CardContent>
    </Card>
</template>
