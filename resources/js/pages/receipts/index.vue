<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes';
import { index as receiptsIndex, create as receiptsCreate, edit as receiptsEdit, destroy as receiptsDestroy } from '@/routes/receipts';
import type { Property } from '../properties/index.vue';
import type { Room } from '../rooms/index.vue';
import type { Tenant } from '../tenants/index.vue';

export interface Receipt {
    id: number;
    tenant_id: number;
    tenant?: Tenant;
    unit_id: number;
    unit: {
        property_id: number;
        room_id: number;
        property?: Property;
        room?: Room;
    };
    receipt_cycle: string;
    start_date: string;
    end_date: string;
    discount_type: string;
    discount_amount: number;
    discount_percent: number;
    tax: number;
    deposit: number;
    total: number;
    down_payment: number;
    remaining: number;
    fully_paid: boolean;
    payment_method: "cash" | "transfer";
    notes: string;
    adults: number;
    children: number;
    babies: number;
    pets: number;
    reminder: string;
    created_at: string;
    updated_at: string
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

const props = defineProps<{
    receipts: PaginatedData<Receipt>;
    filters: {
        dates?: string;
        date?: string;
    };
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
        ],
    },
});

const dates = ref(props.filters.dates ?? 'transaction-date');
const date = ref(props.filters.date ?? new Date().toISOString().split('T')[0]);


watch(dates, (value) => {
    router.get(receiptsIndex.url(), { dates: value || undefined, date: date.value || undefined }, {
        preserveState: true,
        replace: true,
    });
});

watch(date, (value) => {
    router.get(receiptsIndex.url(), { dates: dates.value || undefined, date: value || undefined }, {
        preserveState: true,
        replace: true,
    });
});

function deleteReceipt(receipt: Receipt) {
    if (confirm(`Are you sure you want to delete "${receipt.tenant?.name} - ${receipt.unit.property?.name} - ${receipt.unit.room?.name}"?`)) {
        router.delete(receiptsDestroy.url(receipt.id));
    }
}
</script>

<template>

    <Head title="Receipts List" />

    <Card>
        <CardHeader>
            <CardTitle>Receipts List</CardTitle>
            <CardAction>
                <div class="flex items-center gap-2">
                    <Select v-model="dates">
                        <SelectTrigger id="dates">
                            <SelectValue placeholder="Select Option" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="transaction-date">Transaction Date</SelectItem>
                            <SelectItem value="rent-start-end-date">Lease Start & End Date</SelectItem>
                        </SelectContent>
                    </Select>
                    <Input v-model="date" type="date" placeholder="Search receipts..." class="w-24" />
                    <Button as-child>
                        <Link :href="receiptsCreate.url()">
                            + New Receipt
                        </Link>
                    </Button>
                    <Button variant="outline" @click="router.reload()">
                        ↻ Refresh
                    </Button>
                </div>
            </CardAction>
        </CardHeader>
        <CardContent>
            <div class="overflow-x-auto rounded-md">
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/10">
                            <TableHead>Occupant</TableHead>
                            <TableHead>Property</TableHead>
                            <TableHead>Room</TableHead>
                            <TableHead>Total</TableHead>
                            <TableHead>Start Date</TableHead>
                            <TableHead>End Date</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="receipts.data.length === 0">
                            <TableCell colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                No receipts found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="receipt in receipts.data" :key="receipt.id" class="hover:bg-muted/10">
                            <TableCell>{{ receipt.tenant?.name }}</TableCell>
                            <TableCell>{{ receipt.unit.property?.name }}</TableCell>
                            <TableCell>{{ receipt.unit.room?.name }}</TableCell>
                            <TableCell>{{ Number(receipt.total).toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }) }}</TableCell>
                            <TableCell>{{ receipt.start_date }}</TableCell>
                            <TableCell>{{ receipt.end_date }}</TableCell>
                            <TableCell class="flex items-center justify-center gap-1">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="receiptsEdit.url(receipt.id)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button variant="destructive" size="sm" @click="deleteReceipt(receipt)">
                                    Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div v-if="receipts.last_page > 1" class="mt-4 flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing page {{ receipts.current_page }} of {{ receipts.last_page }}
                    ({{ receipts.total }} total)
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in receipts.links" :key="link.label">
                        <Button v-if="link.url" variant="outline" size="sm"
                            :class="{ 'bg-primary text-primary-foreground': link.active }" as-child>
                            <Link :href="link.url">{{ link.label }}</Link>
                        </Button>
                        <Button v-else variant="outline" size="sm" disabled>{{ link.label }}</Button>
                    </template>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
