<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler
} from 'chart.js';
import type { ChartData, ChartOptions } from 'chart.js';

import {
    RefreshCw,
    Home,
    DoorOpen,
    Wrench,
    CheckCircle2,
    TrendingUp,
    Building2,
    Calendar,
    ArrowUpRight
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Doughnut, Line } from 'vue-chartjs';

const props = defineProps<{
    occupancy: any;
    properties: any[];
}>();
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

ChartJS.register(Title, Tooltip, Legend, ArcElement, LineElement, PointElement, CategoryScale, LinearScale, Filler);

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Reports', href: '/reports' },
            { title: 'Occupancy', href: '/reports/occupancy' },
        ],
    },
});

const selectedProperty = ref('all');
const isLoading = ref(false);

const refreshReport = () => {
    router.reload({
        only: ['occupancy'],
        data: { property_id: selectedProperty.value },
        onBefore: () => isLoading.value = true,
        onFinish: () => isLoading.value = false,
    });
};

// Mock data for occupancy stats
const stats = computed(() => {
    const data = props.occupancy || {
        occupied_room: 0,
        vacant_room: 0,
        occupancy_rate: 0,
        average_rate: 0
    };

    return [
        { title: 'Occupied Units', value: data.occupied_room?.toString() || '0', icon: CheckCircle2, color: 'text-emerald-400', bgColor: 'bg-emerald-500/10' },
        { title: 'Vacant Units', value: data.vacant_room?.toString() || '0', icon: DoorOpen, color: 'text-cyan-400', bgColor: 'bg-cyan-500/10' },
        { title: 'Out of Order', value: '0', icon: Wrench, color: 'text-rose-400', bgColor: 'bg-rose-500/10' },
        { title: 'Units Available', value: (Number(data.occupied_room || 0) + Number(data.vacant_room || 0)).toString(), icon: Home, color: 'text-cyan-400', bgColor: 'bg-cyan-500/10' }
    ];
});

const occupancyData = computed<ChartData<'doughnut'>>(() => ({
    labels: ['Occupied', 'Vacant', 'Out of Order'],
    datasets: [{
        data: [
            props.occupancy?.occupied_room || 0,
            props.occupancy?.vacant_room || 0,
            0
        ],
        backgroundColor: ['#10b981', '#06b6d4', '#e11d48'],
        borderWidth: 0,
        hoverOffset: 4
    }]
}));

const historicalData: ChartData<'line'> = {
    labels: ['Day 1', 'Day 5', 'Day 10', 'Day 15', 'Day 20', 'Day 25', 'Day 30'],
    datasets: [{
        label: 'Occupancy Rate',
        data: [65, 68, 72, 70, 75, 80, 78],
        borderColor: '#06b6d4',
        backgroundColor: 'rgba(6, 182, 212, 0.15)',
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#06b6d4',
        pointBorderColor: '#fff',
        pointHoverRadius: 6
    }]
};

const chartOptions: ChartOptions<'doughnut'> = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%',
    plugins: {
        legend: { display: false }
    }
};

const lineChartOptions: ChartOptions<'line'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { y: { display: false } }
};
</script>

<template>

    <Head title="Occupancy Report" />

    <div class="flex flex-col gap-8 p-6 md:p-8 max-w-7xl mx-auto w-full">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold tracking-tight">Occupancy Metrics</h1>
                <p class="text-muted-foreground">Monitor your real estate utilization performance.</p>
            </div>

            <div class="flex items-center gap-3">
                <Select v-model="selectedProperty">
                    <SelectTrigger class="w-[200px]">
                        <Building2 class="mr-2 h-4 w-4 opacity-50" />
                        <SelectValue placeholder="All Properties" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Properties</SelectItem>
                        <SelectItem v-for="property in properties" :key="property.id" :value="property.id.toString()">
                            {{ property.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Button variant="outline" size="icon" @click="refreshReport" :disabled="isLoading">
                    <RefreshCw class="h-4 w-4" :class="{ 'animate-spin': isLoading }" />
                </Button>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="stat in stats" :key="stat.title"
                class="glass group transition-all duration-300 hover:scale-[1.02] hover:neon-glow border-white/5">
                <CardContent class="p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-xs font-semibold text-muted-foreground uppercase tracking-widest">{{
                            stat.title }}</CardTitle>
                        <div class="p-2.5 rounded-2xl transition-transform group-hover:rotate-12 group-hover:scale-110" :class="stat.bgColor">
                            <stat.icon class="h-5 w-5" :class="stat.color" />
                        </div>
                    </div>
                    <div class="text-3xl font-bold tracking-tight mt-2">{{ stat.value }}</div>
                </CardContent>
            </Card>
        </div>

        <!-- Charts Grid -->
        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Left: Doughnut -->
            <Card class="lg:col-span-1 glass border-white/5 overflow-hidden">
                <CardHeader>
                    <CardTitle class="text-lg">Status Breakdown</CardTitle>
                    <CardDescription>Live unit availability distribution.</CardDescription>
                </CardHeader>
                <CardContent class="h-[280px] relative">
                    <Doughnut :data="occupancyData" :options="chartOptions" />
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-8">
                        <span class="text-4xl font-extrabold tracking-tighter">{{ Number(props.occupancy?.occupancy_rate || 0).toFixed(0)
                            }}%</span>
                        <span class="text-[10px] uppercase font-bold text-muted-foreground tracking-widest">Occupancy</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Right: Historical Line -->
            <Card class="lg:col-span-2 glass border-white/5 overflow-hidden">
                <CardHeader>
                    <CardTitle class="text-lg">Utilization Trends</CardTitle>
                    <CardDescription>Occupancy performance over the last 30 days.</CardDescription>
                </CardHeader>
                <CardContent class="h-[280px]">
                    <Line :data="historicalData" :options="lineChartOptions" />
                </CardContent>
            </Card>
        </div>

        <!-- Derived Metrics (ARR) -->
        <div class="grid gap-6 md:grid-cols-2">
            <Card
                class="glass border-primary/20 hover:neon-glow transition-all duration-500 overflow-hidden relative">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <TrendingUp class="h-24 w-24 text-primary" />
                </div>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <CardTitle class="text-primary font-bold">Average Room Rate (ARR)</CardTitle>
                            <CardDescription>Revenue earned per occupied room per day.</CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="flex items-baseline gap-2 mt-4">
                        <div class="text-5xl font-black text-primary font-mono tracking-tighter">
                            Rp {{ Number(props.occupancy?.average_rate || 0).toLocaleString() }}
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mt-6">
                         <p class="text-xs text-muted-foreground flex items-center">
                            <ArrowUpRight class="mr-1.5 h-3 w-3 text-primary" />
                            Live calculation
                        </p>
                        <div class="h-1 w-24 bg-primary/20 rounded-full overflow-hidden">
                            <div class="h-full bg-primary w-2/3 animate-pulse"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="glass border-dashed border-white/10 flex flex-col justify-center transition-all hover:border-accent/40 hover:accent-glow">
                <CardContent class="py-8 flex flex-col items-center text-center">
                    <div class="p-4 rounded-full bg-accent/10 mb-4">
                        <Calendar class="h-10 w-10 text-accent opacity-80" />
                    </div>
                    <h3 class="text-lg font-bold text-accent">30-Day Outlook</h3>
                    <p class="text-sm text-muted-foreground max-w-xs mt-2 leading-relaxed">
                        Future projection shows an <span class="text-emerald-400 font-bold italic">increasing trend</span> in bookings for the next quarter.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
