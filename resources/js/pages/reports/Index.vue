<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { currencies } from 'country-data-list';
import {
    ArrowRight,
    BarChart3,
    Building2,
    Calendar,
    PieChart,
    RefreshCw,
    TrendingDown,
    TrendingUp,
    Wallet,
} from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const props = defineProps<{
    properties: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Reports',
                href: '/reports',
            },
        ],
    },
});

const selectedProperty = ref('all');
const selectedRange = ref('30days');
const selectedCurrency = ref('IDR');
const isLoading = ref(false);

const statsData = ref({
    income: 0,
    expense: 0,
    debt: 0,
    occupancy_rate: 0,
});

const fetchStats = async () => {
    isLoading.value = true;

    try {
        const response = await fetch(`/reports/stats?property_id=${selectedProperty.value}&range=${selectedRange.value}`);
        const data = await response.json();
        statsData.value = data;
    } catch (e) {
        console.error('Error fetching stats:', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchStats);
watch([selectedProperty, selectedRange], fetchStats);

const refreshStats = () => {
    fetchStats();
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: selectedCurrency.value,
        maximumFractionDigits: 0,
    }).format(val);
};

const kpiStats = computed(() => [
    {
        title: 'Total Realized Income',
        value: formatCurrency(statsData.value.income),
        change: '+0% from last month',
        icon: TrendingUp,
        color: 'text-green-600',
    },
    {
        title: 'Operating Expenses',
        value: formatCurrency(statsData.value.expense),
        change: '+0% from last month',
        icon: TrendingDown,
        color: 'text-red-600',
    },
    {
        title: 'Outstanding Debt',
        value: formatCurrency(statsData.value.debt),
        change: 'Stable',
        icon: Wallet,
        color: 'text-amber-600',
    },
    {
        title: 'Occupancy Rate',
        value: `${Math.round(statsData.value.occupancy_rate)}%`,
        change: 'Avg. across all units',
        icon: Building2,
        color: 'text-blue-600',
    },
]);

const reportCards = [
    {
        title: 'Financial Performance',
        description: 'Detailed revenue, expenses, and transaction history analytics.',
        href: '/reports/financial',
        icon: Wallet,
        color: 'text-blue-500',
        bgColor: 'bg-blue-50 dark:bg-blue-900/20',
    },
    {
        title: 'Occupancy Tracking',
        description: 'Monitor room availability, rental trends, and market metrics.',
        href: '/reports/occupancy',
        icon: PieChart,
        color: 'text-cyan-500',
        bgColor: 'bg-cyan-50 dark:bg-cyan-900/20',
    },
    {
        title: 'Room Schedule',
        description: 'Visual timeline of bookings, check-ins, and scheduled maintenance.',
        href: '/reports/schedule',
        icon: Calendar,
        color: 'text-cyan-500',
        bgColor: 'bg-cyan-50 dark:bg-cyan-900/20',
    },
];
</script>

<template>

    <Head title="Statistical Reports" />

    <div class="flex flex-col gap-8 p-6 md:p-8 max-w-7xl mx-auto w-full">
        <!-- Header & Filters -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold tracking-tight">Statistical Reports</h1>
                <p class="text-muted-foreground">Comprehensive analytics for your property performance.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <Select v-model="selectedCurrency">
                    <SelectTrigger class="w-[140px]">
                        <SelectValue placeholder="Currency" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="currency in currencies.all" :key="currency.code" :value="currency.code">
                            {{ currency.name }} ({{ currency.code }})
                        </SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="selectedProperty">
                    <SelectTrigger class="w-[180px]">
                        <SelectValue placeholder="Property" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Properties</SelectItem>
                        <SelectItem v-for="property in props.properties" :key="property.id"
                            :value="String(property.id)">
                            {{ property.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="selectedRange">
                    <SelectTrigger class="w-[160px]">
                        <SelectValue placeholder="Range" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="today">Today</SelectItem>
                        <SelectItem value="yesterday">Yesterday</SelectItem>
                        <SelectItem value="30days">Last 30 Days</SelectItem>
                        <SelectItem value="60days">Last 60 Days</SelectItem>
                    </SelectContent>
                </Select>

                <Button variant="outline" size="icon" @click="refreshStats" :disabled="isLoading">
                    <RefreshCw class="h-4 w-4" :class="{ 'animate-spin': isLoading }" />
                </Button>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="stat in kpiStats" :key="stat.title"
                class="overflow-hidden border-none shadow-sm dark:shadow-none bg-white/50 dark:bg-neutral-900/50 backdrop-blur-sm ring-1 ring-neutral-200 dark:ring-neutral-800">
                <CardContent class="p-6">
                    <div class="flex items-center justify-between space-y-0 pb-2">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ stat.title }}
                        </p>
                        <component :is="stat.icon" class="h-4 w-4" :class="stat.color" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="text-2xl font-bold tracking-tight">{{ stat.value }}</div>
                        <p class="text-[10px] text-muted-foreground">{{ stat.change }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Detailed Reports Navigation -->
        <div class="grid gap-6 md:grid-cols-3">
            <Link v-for="report in reportCards" :key="report.title" :href="report.href" class="group">
                <Card
                    class="h-full cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border-neutral-200 dark:border-neutral-800 hover:border-primary/20 bg-linear-to-br from-white to-neutral-50 dark:from-neutral-900 dark:to-neutral-950">
                    <CardHeader>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl transition-colors group-hover:scale-110 duration-300"
                            :class="report.bgColor">
                            <component :is="report.icon" class="h-6 w-6" :class="report.color" />
                        </div>
                        <CardTitle
                            class="text-xl group-hover:text-primary transition-colors flex items-center justify-between">
                            {{ report.title }}
                            <ArrowRight
                                class="h-4 w-4 opacity-0 transition-all -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0" />
                        </CardTitle>
                        <CardDescription class="text-sm leading-relaxed pt-2">
                            {{ report.description }}
                        </CardDescription>
                    </CardHeader>
                </Card>
            </Link>
        </div>

        <!-- History Placeholder -->
        <Card class="bg-neutral-50/50 dark:bg-neutral-900/50 border-dashed border-2">
            <CardContent class="py-12 flex flex-col items-center justify-center text-center opacity-60">
                <BarChart3 class="h-12 w-12 mb-4 text-muted-foreground" />
                <h3 class="text-lg font-medium">No recent trends to display</h3>
                <p class="text-sm text-muted-foreground max-w-xs">
                    Trend data will be automatically generated once more transactions are recorded in the system.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
