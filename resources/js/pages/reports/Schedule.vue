<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Building2,
    CalendarCheck,
    ChevronLeft,
    ChevronRight,
    CircleCheck,
    CircleDashed,
    Clock,
    RefreshCw,
    Wrench,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const props = defineProps<{
    units: any[];
    properties: any[];
    filters: {
        days: number;
        property_id: any;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Reports', href: '/reports' },
            { title: 'Schedule', href: '/reports/schedule' },
        ],
    },
});

const selectedProperty = ref(String(props.filters.property_id));
const selectedRange = ref(String(props.filters.days));
const isLoading = ref(false);

const refreshReport = () => {
    isLoading.value = true;
    router.get(
        '/reports/schedule',
        {
            property_id: selectedProperty.value,
            days: selectedRange.value,
        },
        {
            preserveState: true,
            onFinish: () => (isLoading.value = false),
        },
    );
};

// Auto-refresh when filters change
watch([selectedProperty, selectedRange], () => {
    refreshReport();
});

// Generate dynamic timeline days
const timelineDays = computed(() => {
    const days = [];
    const today = new Date();

    for (let i = 0; i < parseInt(selectedRange.value); i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + i);
        days.push({
            name: date.toLocaleDateString('en-US', { weekday: 'short' }),
            num: date.getDate(),
            full: date.toISOString().split('T')[0],
        });
    }

    return days;
});

const getReceiptForDay = (unit: any, dayFull: string) => {
    return unit.receipts?.find((r: any) => r.start_date <= dayFull && r.end_date >= dayFull);
};

const currentMonth = computed(() => {
    return new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const stats = computed(() => [
    {
        label: 'Upcoming Bookings',
        value: props.units.reduce((acc, u) => acc + (u.receipts?.length || 0), 0),
        icon: CalendarCheck,
        color: 'text-emerald-500',
    },
    {
        label: 'Pending Cleaning',
        value: props.units.filter((u) => u.condition === 'dirty').length,
        icon: CircleDashed,
        color: 'text-amber-500',
    },
    {
        label: 'Under Maintenance',
        value: props.units.filter((u) => u.condition === 'repair').length,
        icon: Wrench,
        color: 'text-red-500',
    },
]);
</script>

<template>

    <Head title="Room Schedule" />

    <div class="flex flex-col gap-8 p-6 md:p-8 max-w-7xl mx-auto w-full">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold tracking-tight">Booking Schedule</h1>
                <p class="text-muted-foreground">Visualize your property availability and booking timeline.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center bg-white dark:bg-neutral-900 border rounded-lg p-1">
                    <Button variant="ghost" size="icon" class="h-8 w-8">
                        <ChevronLeft class="h-4 w-4" />
                    </Button>
                    <span class="px-4 text-sm font-medium">{{ currentMonth }}</span>
                    <Button variant="ghost" size="icon" class="h-8 w-8">
                        <ChevronRight class="h-4 w-4" />
                    </Button>
                </div>
                <Button variant="outline" size="icon" @click="refreshReport" :disabled="isLoading">
                    <RefreshCw class="h-4 w-4" :class="{ 'animate-spin': isLoading }" />
                </Button>
            </div>
        </div>

        <!-- Filters Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 items-center">
            <Select v-model="selectedProperty">
                <SelectTrigger>
                    <Building2 class="mr-2 h-4 w-4 opacity-50" />
                    <SelectValue placeholder="Select Property" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Properties</SelectItem>
                    <SelectItem v-for="property in props.properties" :key="property.id" :value="String(property.id)">
                        {{ property.name }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <Select v-model="selectedRange">
                <SelectTrigger>
                    <Clock class="mr-2 h-4 w-4 opacity-50" />
                    <SelectValue placeholder="Range" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="7">Next 7 Days</SelectItem>
                    <SelectItem value="14">Next 14 Days</SelectItem>
                    <SelectItem value="30">Next 30 Days</SelectItem>
                </SelectContent>
            </Select>

            <div
                class="lg:col-span-2 flex justify-end gap-6 text-xs text-muted-foreground font-medium uppercase tracking-widest">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div> Occupied
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full border bg-white dark:bg-neutral-800"></div> Available
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div> Maintenance
                </div>
            </div>
        </div>

        <!-- Schedule Grid Content -->
        <Card class="overflow-hidden border-none shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-800">
            <CardHeader class="border-b bg-neutral-50/50 dark:bg-neutral-900/50 p-4">
                <div class="flex items-center justify-between">
                    <CardTitle class="text-lg">Property Timeline</CardTitle>
                    <div class="flex gap-2">
                        <Button variant="ghost" size="sm" class="text-xs h-8">Download PDF</Button>
                        <Button variant="ghost" size="sm" class="text-xs h-8">Share Link</Button>
                    </div>
                </div>
            </CardHeader>
            <CardContent class="p-0">
                <div class="min-w-[800px] w-full overflow-x-auto relative">
                    <!-- Table Header: Days -->
                    <div class="flex border-b">
                        <div
                            class="w-64 border-r p-4 shrink-0 bg-neutral-50/10 dark:bg-neutral-900/10 backdrop-blur-md sticky left-0 z-20 font-bold uppercase tracking-wider text-xs">
                            Unit / Room</div>
                        <div v-for="day in timelineDays" :key="day.full"
                            class="flex-1 min-w-[100px] border-r p-4 text-center">
                            <div class="text-xs font-bold uppercase opacity-50">{{ day.name }}</div>
                            <div class="text-xl font-bold">{{ day.num }}</div>
                        </div>
                    </div>

                    <!-- Table Body: Rows -->
                    <div class="divide-y relative">
                        <div v-for="unit in props.units" :key="unit.id"
                            class="flex hover:bg-neutral-50/50 dark:hover:bg-neutral-900/40 transition-colors">
                            <div
                                class="w-64 border-r p-4 shrink-0 bg-white/80 dark:bg-neutral-900/80 sticky left-0 z-10 flex flex-col items-start gap-1">
                                <span class="font-bold text-sm">{{ unit.name }}</span>
                                <span
                                    class="text-[10px] text-muted-foreground uppercase bg-neutral-100 dark:bg-neutral-800 px-1.5 py-0.5 rounded">{{
                                    unit.room?.name || 'No Room' }}</span>
                            </div>
                            <div v-for="day in timelineDays" :key="day.full"
                                class="flex-1 min-w-[100px] border-r p-1 flex items-center justify-center relative">
                                <!-- Status Marker -->
                                <template v-if="getReceiptForDay(unit, day.full)">
                                    <div
                                        class="absolute h-10 w-[calc(100%+8px)] left-[-4px] bg-emerald-500/15 border-y-2 border-emerald-500/30 z-1 flex items-center px-4">
                                        <div
                                            class="flex items-center gap-2 overflow-hidden text-emerald-800 dark:text-emerald-300">
                                            <CircleCheck class="h-3 w-3 shrink-0" />
                                            <span class="text-[10px] font-bold truncate">
                                                {{ getReceiptForDay(unit, day.full).tenant?.name || 'Booked' }}
                                            </span>
                                        </div>
                                    </div>
                                </template>
                                <div v-else-if="unit.condition === 'dirty'"
                                    class="absolute h-10 w-[calc(100%+8px)] left-[-4px] bg-amber-500/15 border-y-2 border-amber-500/30 z-1 flex items-center px-4">
                                    <div
                                        class="flex items-center gap-2 overflow-hidden text-amber-800 dark:text-amber-300">
                                        <CircleDashed class="h-3 w-3 shrink-0" />
                                        <span class="text-[10px] font-bold truncate">Cleaning</span>
                                    </div>
                                </div>
                                <div v-else-if="unit.condition === 'repair'"
                                    class="absolute h-10 w-[calc(100%+8px)] left-[-4px] bg-red-500/15 border-y-2 border-red-500/30 z-1 flex items-center px-4">
                                    <div class="flex items-center gap-2 overflow-hidden text-red-800 dark:text-red-300">
                                        <Wrench class="h-3 w-3 shrink-0" />
                                        <span class="text-[10px] font-bold truncate">Maintenance</span>
                                    </div>
                                </div>
                                <div v-else class="text-[8px] text-neutral-300 uppercase font-medium tracking-tighter">
                                    Available</div>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>


        <!-- Summary Legend -->
        <div class="grid gap-6 md:grid-cols-3">
            <Card v-for="item in stats" :key="item.label"
                class="border-none shadow-none bg-neutral-50/50 dark:bg-neutral-900/30 ring-1 ring-neutral-200 dark:ring-neutral-800">
                <CardContent class="p-6 flex items-center gap-4">
                    <div
                        class="p-3 rounded-2xl bg-white dark:bg-neutral-800 shadow-sm transition-transform hover:scale-110 duration-300">
                        <item.icon class="h-5 w-5" :class="item.color" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-bold font-mono">{{ item.value }}</span>
                        <span class="text-xs text-muted-foreground uppercase font-bold tracking-tight">{{ item.label
                        }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
