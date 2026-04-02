<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement
} from 'chart.js';
import type { ChartData, ChartOptions } from 'chart.js';
import {
    Download,
    RefreshCw,
    ArrowUpRight,
    ArrowDownRight,
    CalendarDays,
    Building2,
    History
} from 'lucide-vue-next';
import { ref, onMounted, reactive, computed } from 'vue';
import { Bar, Pie } from 'vue-chartjs';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

defineProps<{
    properties: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Reports', href: '/reports' },
            { title: 'Financial', href: '/reports/financial' },
        ],
    },
});

const reportType = ref('profit-loss');
const fileType = ref('xlsx');
const period = ref('last-30-days');
const selectedProperty = ref('all');
const isLoading = ref(false);

const financialData = reactive({
    total_income: 0,
    total_expense: 0,
    net_profit: 0,
    income_trend: [] as any[],
    expense_trend: [] as any[],
    expense_summary: [] as any[],
});

const refreshReport = async () => {
    isLoading.value = true;

    try {
        const queryParams = new URLSearchParams({
            property_id: selectedProperty.value,
            range: period.value.replace('last-', '') // Adjusting for controller matches (match match match)
        }).toString();

        const [financeRes, profitLossRes] = await Promise.all([
            fetch(`/reports/finance-data?${queryParams}`),
            fetch(`/reports/profit-loss-data?${queryParams}`)
        ]);

        const finance = await financeRes.json();
        const profitLoss = await profitLossRes.json();

        financialData.income_trend = finance.income_trend;
        financialData.expense_trend = finance.expense_trend;
        financialData.expense_summary = finance.expense_summary;

        financialData.total_income = profitLoss.total_income;
        financialData.total_expense = profitLoss.total_expense;
        financialData.net_profit = profitLoss.net_profit;

    } catch (error) {
        console.error('Failed to fetch financial data:', error);
    } finally {
        isLoading.value = false;
    }
};

const downloadReport = async () => {
    const queryParams = new URLSearchParams({
        property_id: selectedProperty.value,
        range: period.value.replace('last-', '')
    }).toString();

    window.location.href = `/reports/financial/download?${queryParams}`;
};

onMounted(() => {
    refreshReport();
});

// Chart data computed from financialData
const barData = computed<ChartData<'bar'>>(() => ({
    labels: financialData.income_trend.map(i => i.date),
    datasets: [
        {
            label: 'Income',
            backgroundColor: '#10b981', // Emerald
            borderRadius: 6,
            data: financialData.income_trend.map(i => i.amount)
        },
        {
            label: 'Expense',
            backgroundColor: '#f43f5e', // Rose
            borderRadius: 6,
            data: financialData.expense_trend.map(i => i.amount)
        }
    ]
}));

const pieData = computed<ChartData<'pie'>>(() => ({
    labels: financialData.expense_summary.map(e => e.category),
    datasets: [
        {
            backgroundColor: ['#06b6d4', '#0ea5e9', '#10b981', '#34d399', '#2dd4bf', '#3b82f6'],
            borderWidth: 0,
            hoverOffset: 15,
            data: financialData.expense_summary.map(e => e.amount)
        }
    ]
}));

const barOptions: ChartOptions<'bar'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' }
    }
};

const pieOptions: ChartOptions<'pie'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' }
    },
    cutout: '60%'
};
</script>

<template>

    <Head title="Financial Report" />

    <div class="flex flex-col gap-6 p-6 md:p-8 max-w-7xl mx-auto w-full">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold tracking-tight">Financial Performance</h1>
                <p class="text-muted-foreground">Analyze your revenue streams and operating costs.</p>
            </div>

            <div class="flex items-center gap-2">
                <Button variant="outline" @click="refreshReport" :disabled="isLoading"
                    class="h-9 glass border-white/10">
                    <RefreshCw class="mr-2 h-4 w-4" :class="{ 'animate-spin': isLoading }" />
                    Refresh
                </Button>
                <Button variant="default" @click="downloadReport" class="h-9 neon-glow">
                    <Download class="mr-2 h-4 w-4" />
                    Download {{ fileType.toUpperCase() }}
                </Button>
            </div>
        </div>

        <!-- Filters Section -->
        <Card class="glass border-white/5 shadow-xl">
            <CardContent class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase text-muted-foreground">Report Type</label>
                        <Select v-model="reportType">
                            <SelectTrigger class="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="profit-loss">Profit & Loss Report</SelectItem>
                                <SelectItem value="income-history">Income History</SelectItem>
                                <SelectItem value="income-source">Income Sources</SelectItem>
                                <SelectItem value="expense-source">Expense Sources</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase text-muted-foreground">Format</label>
                        <Select v-model="fileType" class="w-full">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="xlsx">Excel (.xlsx)</SelectItem>
                                <SelectItem value="csv">CSV (.csv)</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase text-muted-foreground">Property</label>
                        <Select v-model="selectedProperty" class="w-full">
                            <SelectTrigger>
                                <Building2 class="mr-2 h-4 w-4 opacity-50" />
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Properties</SelectItem>
                                <SelectItem v-for="property in properties" :key="property.id"
                                    :value="property.id.toString()">
                                    {{ property.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase text-muted-foreground">Period</label>
                        <Select v-model="period">
                            <SelectTrigger class="w-full">
                                <CalendarDays class="mr-2 h-4 w-4 opacity-50" />
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="last-7-days">Last 7 Days</SelectItem>
                                <SelectItem value="last-30-days">Last 30 Days</SelectItem>
                                <SelectItem value="last-year">Last Year</SelectItem>
                                <SelectItem value="custom">Custom Range</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- KPI Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <Card class="glass group hover:neon-glow transition-all duration-300 border-primary/20">
                <CardHeader class="pb-2">
                    <CardTitle class="text-xs font-bold uppercase tracking-widest text-primary">Total Income</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-black tracking-tight">Rp {{ financialData.total_income.toLocaleString() }}
                    </div>
                    <div class="flex items-center text-[10px] uppercase font-bold text-emerald-400 mt-2">
                        <ArrowUpRight class="mr-1 h-3 w-3" />
                        Live tracking
                    </div>
                </CardContent>
            </Card>

            <Card class="glass group hover:accent-glow transition-all duration-300 border-accent/20">
                <CardHeader class="pb-2">
                    <CardTitle class="text-xs font-bold uppercase tracking-widest text-accent">Operating Expenses
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-black tracking-tight">Rp {{ financialData.total_expense.toLocaleString()
                        }}</div>
                    <div class="flex items-center text-[10px] uppercase font-bold text-rose-400 mt-2">
                        <ArrowDownRight class="mr-1 h-3 w-3" />
                        Live tracking
                    </div>
                </CardContent>
            </Card>

            <Card
                class="glass group border-emerald-500/20 hover:shadow-[0_0_20px_-5px_rgba(16,185,129,0.3)] transition-all duration-300">
                <CardHeader class="pb-2">
                    <CardTitle class="text-xs font-bold uppercase tracking-widest text-emerald-400">Net Profit
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-black tracking-tight">Rp {{ financialData.net_profit.toLocaleString() }}
                    </div>
                    <div class="flex items-center text-[10px] uppercase font-bold text-emerald-400 mt-2">
                        <ArrowUpRight class="mr-1 h-3 w-3" />
                        Healthy margin
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Charts Grid -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Main Trend Chart -->
            <Card class="glass border-white/5">
                <CardHeader>
                    <CardTitle class="text-lg">Income vs Expenses Trend</CardTitle>
                    <CardDescription>Visual comparison of cash flow performance.</CardDescription>
                </CardHeader>
                <CardContent class="h-[350px]">
                    <Bar :data="barData" :options="barOptions" />
                </CardContent>
            </Card>

            <!-- Breakdown Chart -->
            <Card class="glass border-white/5">
                <CardHeader>
                    <CardTitle class="text-lg">Revenue Sources</CardTitle>
                    <CardDescription>Composition of total income by category.</CardDescription>
                </CardHeader>
                <CardContent class="h-[350px] flex items-center justify-center">
                    <Pie :data="pieData" :options="pieOptions" />
                </CardContent>
            </Card>
        </div>

        <!-- Footer Actions -->
        <div class="flex justify-center">
            <Button variant="ghost" as-child class="text-muted-foreground hover:text-primary underline">
                <Link href="/receipts">
                <History class="mr-2 h-4 w-4" />
                Check Full Transaction History
                </Link>
            </Button>
        </div>
    </div>
</template>
