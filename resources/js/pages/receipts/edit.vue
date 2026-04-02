<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import type { Unit } from '@/components/properties/UnitModal.vue';
import ReceiptForm from '@/components/receipts/ReceiptForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as receiptsIndex, update as receiptsUpdate } from '@/routes/receipts';
import type { Property } from '../properties/index.vue';
import type { Tenant } from '../tenants/index.vue';
import type { Receipt } from './index.vue';

const props = defineProps<{
    receipt: Receipt;
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
                title: 'Edit Receipt',
            },
        ],
    },
});

const form = useForm({
    id: props.receipt.id,
    tenant_id: props.receipt.tenant_id.toString(),
    property_id: props.receipt.unit.property_id?.toString() ?? '',
    unit_id: props.receipt.unit_id.toString(),
    adults: props.receipt.adults,
    children: props.receipt.children,
    babies: props.receipt.babies,
    pets: props.receipt.pets,
    receipt_duration: (props.receipt as any).receipt_duration || 1,
    receipt_cycle: props.receipt.receipt_cycle,
    start_date: props.receipt.start_date,
    end_date: props.receipt.end_date,
    discount_type: props.receipt.discount_type as 'percentage' | 'amount' ?? 'percentage',
    discount_amount: props.receipt.discount_amount,
    discount_percent: props.receipt.discount_percent,
    tax: props.receipt.tax,
    deposit: props.receipt.deposit,
    total: props.receipt.total,
    down_payment: props.receipt.down_payment,
    remaining: props.receipt.remaining,
    payment_status: props.receipt.fully_paid ? 'paid' as const : 'unpaid' as const,
    payment_method: props.receipt.payment_method,
    notes: props.receipt.notes ?? '',
    reminder: props.receipt.reminder ?? '1-day-advance',
});

function submit() {
    form.put(receiptsUpdate.url(props.receipt.id));
}
</script>

<template>

    <Head title="Edit Receipt" />

    <Card>
        <CardHeader>
            <CardTitle>Edit Receipt</CardTitle>
        </CardHeader>
        <CardContent>
            <ReceiptForm v-model:form="form" :tenants="tenants" :properties="properties" :units="units"
                submit-label="Update Receipt" @submit="submit" />
        </CardContent>
    </Card>
</template>
