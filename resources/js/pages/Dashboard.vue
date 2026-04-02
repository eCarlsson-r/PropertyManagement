<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BarChart3,
    Building,
    Calendar,
    ArrowRight,
    User,
    Receipt
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import properties from '@/routes/properties';
import receipts from '@/routes/receipts';
import tenants from '@/routes/tenants';

defineProps<{
    receipt_list: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>

    <Head title="Dashboard" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
        </div>

        <!-- Stat Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card class="hover:border-primary transition-all duration-300 group">
                <Link :href="tenants.index()" class="block">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Tenants</CardTitle>
                        <User class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xs text-muted-foreground uppercase font-bold tracking-wider opacity-60">Manage
                            tenant data</div>
                    </CardContent>
                </Link>
            </Card>

            <Card class="hover:border-primary transition-all duration-300 group">
                <Link :href="properties.index()" class="block">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Properties</CardTitle>
                        <Building class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xs text-muted-foreground uppercase font-bold tracking-wider opacity-60">
                            Property unit listings</div>
                    </CardContent>
                </Link>
            </Card>

            <Card class="hover:border-primary transition-all duration-300 group">
                <Link href="/reports/schedule" class="block">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Schedule</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xs text-muted-foreground uppercase font-bold tracking-wider opacity-60">
                            Property availability</div>
                    </CardContent>
                </Link>
            </Card>

            <Card class="hover:border-primary transition-all duration-300 group">
                <Link href="/reports" class="block">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Statistics</CardTitle>
                        <BarChart3 class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-xs text-muted-foreground uppercase font-bold tracking-wider opacity-60">
                            Detailed site analytics</div>
                    </CardContent>
                </Link>
            </Card>
        </div>

        <!-- Latest Receipts Table Section -->
        <Card class="overflow-hidden">
            <CardHeader class="flex flex-row items-center justify-between px-6 py-4">
                <div class="space-y-1">
                    <CardTitle class="text-xl">Recent Receipts</CardTitle>
                </div>
                <Link :href="receipts.index()"
                    class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline">
                    See More
                    <ArrowRight class="h-3 w-3" />
                </Link>
            </CardHeader>
            <CardContent class="p-0 border-t border-white/5">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead class="bg-muted/10">
                            <tr class="border-b border-white/5">
                                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Tenant
                                </th>
                                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Property
                                </th>
                                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Unit</th>
                                <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Total
                                </th>
                                <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Start
                                </th>
                                <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">End
                                </th>
                            </tr>
                        </thead>
                        <tbody v-if="receipt_list && receipt_list.length > 0" class="last:border-0">
                            <tr v-for="receipt in receipt_list" :key="receipt.id">
                                <td class="p-4 align-middle font-medium">{{ receipt.tenant.name }}</td>
                                <td class="p-4 align-middle font-medium">{{ receipt.unit.property.name }}</td>
                                <td class="p-4 align-middle font-medium">{{ receipt.unit.name }}</td>
                                <td class="p-4 text-right align-middle font-medium">Rp {{
                                    Number(receipt.total).toLocaleString()
                                    }}</td>
                                <td class="p-4 text-center align-middle font-medium">{{ receipt.start_date }}</td>
                                <td class="p-4 text-center align-middle font-medium">{{ receipt.end_date }}</td>
                            </tr>
                        </tbody>
                        <tbody v-else class="last:border-0">
                            <tr class="hover:bg-muted/10 border-b border-white/5 transition-colors">
                                <td colspan="6" class="p-8 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center gap-2">
                                        <Receipt class="h-8 w-8 opacity-20" />
                                        <span class="text-xs font-bold uppercase tracking-widest opacity-50">Receipt
                                            data will automatically appear here.</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>


</template>
