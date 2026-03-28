<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';
import type { Unit } from '@/components/properties/UnitModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import NumberSlider from '@/components/ui/NumberSlider.vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { index as receiptsIndex } from '@/routes/receipts';
import type { Property } from '../../pages/properties/index.vue';
import type { Tenant } from '../../pages/tenants/index.vue';
import OccupantCounter from './OccupantCounter.vue';

export interface ReceiptFormData {
    id?: number;
    tenant_id: number | string;
    property_id: number | string;
    unit_id: number | string;
    adults: number;
    children: number;
    babies: number;
    pets: number;
    receipt_duration: number;
    receipt_cycle: string;
    start_date: string;
    end_date: string;
    discount_type: 'percentage' | 'amount';
    discount_amount: number;
    discount_percent: number;
    tax: number;
    deposit: number;
    total: number;
    down_payment?: number;
    remaining?: number;
    payment_status: 'paid' | 'unpaid';
    payment_method: 'cash' | 'transfer';
    notes: string;
    reminder: string;
}

const form = defineModel<InertiaForm<ReceiptFormData>>('form', { required: true });

defineProps<{
    tenants: Tenant[];
    properties: Property[];
    units: Unit[];
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();

const calculateEndDate = () => {
    if (!form.value.start_date) {
        return;
    }

    const date = new Date(form.value.start_date);
    const duration = form.value.receipt_duration || 1;

    switch (form.value.receipt_cycle) {
        case 'daily':
            date.setDate(date.getDate() + duration);
            break;
        case 'weekly':
            date.setDate(date.getDate() + (duration * 7));
            break;
        case 'monthly':
            date.setMonth(date.getMonth() + duration);
            break;
        case 'annual':
            date.setFullYear(date.getFullYear() + duration);
            break;
    }

    form.value.end_date = date.toISOString().split('T')[0];
};

watch(
    [() => form.value.start_date, () => form.value.receipt_duration, () => form.value.receipt_cycle],
    () => {
        calculateEndDate();
    }
);

watch(
    [() => form.value.total, () => form.value.down_payment],
    () => {
        form.value.remaining = (form.value.total || 0) - (form.value.down_payment || 0);
    }
);

onMounted(() => {
    if (!form.value.end_date) {
        calculateEndDate();
    }
});

</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-6">
                <!-- Lease Details -->
                <div class="space-y-4">
                    <h5 class="text-lg font-semibold">Lease Details</h5>
                    <div class="grid gap-2">
                        <Label for="tenant_id">Tenant</Label>
                        <Select v-model="form.tenant_id">
                            <SelectTrigger id="tenant_id" class="w-full">
                                <SelectValue placeholder="Select Tenant Name" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="tenant in tenants" :key="tenant.id" :value="tenant.id.toString()">
                                    {{ tenant.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="property_id">Property</Label>
                            <Select v-model="form.property_id">
                                <SelectTrigger id="property_id" class="w-full">
                                    <SelectValue placeholder="Select Property" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="property in properties" :key="property.id"
                                        :value="property.id.toString()">
                                        {{ property.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label for="unit_id">Unit/Room No.</Label>
                            <Select v-model="form.unit_id">
                                <SelectTrigger id="unit_id" class="w-full">
                                    <SelectValue placeholder="Unit/Room No." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="unit in units" :key="unit.id"
                                        :value="unit.id?.toString() || '0'">
                                        {{ unit.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </div>

                <!-- Number of Occupants -->
                <div class="space-y-4">
                    <h5 class="text-lg font-semibold">Number of Occupants</h5>
                    <div class="grid grid-cols-2 gap-4">
                        <OccupantCounter label="Adults" v-model="form.adults" :min="1" />
                        <OccupantCounter label="Children" v-model="form.children" />
                        <OccupantCounter label="Babies" v-model="form.babies" />
                        <OccupantCounter label="Pets" v-model="form.pets" />
                    </div>
                </div>
                <!-- Due Date Details -->
                <div class="space-y-4">
                    <h5 class="text-lg font-semibold">Due Date Details</h5>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid gap-2">
                            <Label for="receipt-cycle">Lease Cycle</Label>
                            <Select v-model="form.receipt_cycle">
                                <SelectTrigger id="receipt-cycle" class="w-full">
                                    <SelectValue placeholder="Select Cycle" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="daily">Daily</SelectItem>
                                    <SelectItem value="weekly">Weekly</SelectItem>
                                    <SelectItem value="monthly">Monthly</SelectItem>
                                    <SelectItem value="annual">Annual</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div v-if="form.receipt_cycle !== 'daily'" class="grid gap-2">
                            <Label for="receipt-duration">Lease Duration</Label>
                            <NumberSlider class="w-full" v-model="form.receipt_duration" :min="1" :max="31" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="start-date">Start Date</Label>
                            <Input type="date" id="start-date" v-model="form.start_date" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="end-date">End Date</Label>
                            <Input type="date" id="end-date" v-model="form.end_date" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Costs & Status -->
                <div class="space-y-4">
                    <h5 class="text-lg font-semibold">Costs & Status</h5>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="discount-type">Discount Type</Label>
                            <Select v-model="form.discount_type">
                                <SelectTrigger id="discount-type" class="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="percentage">Percentage (%)</SelectItem>
                                    <SelectItem value="amount">Amount (Rp)</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label v-if="form.discount_type === 'percentage'">Discount (%)</Label>
                            <Label v-else>Discount (Rp)</Label>
                            <Input v-if="form.discount_type === 'percentage'" type="number"
                                v-model="form.discount_percent" />
                            <Input v-else type="number" v-model="form.discount_amount" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="tax">Tax (Rp)</Label>
                            <Input type="number" id="tax" v-model="form.tax" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="total">Total Payment (Rp)</Label>
                            <Input type="number" id="total" v-model="form.total" />
                        </div>
                    </div>

                    <div v-if="form.payment_status == 'unpaid'" class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="down_payment">Down Payment (Rp)</Label>
                            <Input type="number" id="down_payment" v-model="form.down_payment" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="remaining">Remaining Balance (Rp)</Label>
                            <Input type="number" id="remaining" v-model="form.remaining" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="payment-status">Payment Status</Label>
                            <Select v-model="form.payment_status">
                                <SelectTrigger id="payment-status" class="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="paid">Paid</SelectItem>
                                    <SelectItem value="unpaid">Unpaid</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label for="deposit">Deposit (Rp)</Label>
                            <Input type="number" id="deposit" v-model="form.deposit" />
                        </div>
                    </div>
                </div>

                <!-- Notes & Reminder -->
                <div class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="notes">Notes</Label>
                        <Textarea id="notes" v-model="form.notes" placeholder="Add special notes..." />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="reminder">Reminder (Due Date)</Label>
                            <Select v-model="form.reminder">
                                <SelectTrigger id="reminder" class="w-full">
                                    <SelectValue placeholder="Select reminder time" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="1-day-advance">1 day before</SelectItem>
                                    <SelectItem value="2-day-advance">2 days before</SelectItem>
                                    <SelectItem value="3-day-advance">3 days before</SelectItem>
                                    <SelectItem value="7-day-advance">7 days before</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label for="payment_method">Payment Method</Label>
                            <Select v-model="form.payment_method">
                                <SelectTrigger id="payment_method" class="w-full">
                                    <SelectValue placeholder="Select payment method" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="cash">Cash</SelectItem>
                                    <SelectItem value="transfer">QRIS / Transfer</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t">
            <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
            <Button variant="outline" as-child>
                <Link :href="receiptsIndex.url()">Cancel</Link>
            </Button>
        </div>
    </form>
</template>