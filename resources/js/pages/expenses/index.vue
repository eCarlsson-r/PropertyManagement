<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppContent from '@/components/AppContent.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import { index as expensesIndex, create as expensesCreate, edit as expensesEdit, destroy as expensesDestroy } from '@/routes/expenses';
import type { Property } from '../properties/index.vue';

export interface Expense {
    id: number;
    date: string;
    property_id: number;
    property?: Property;
    title: string;
    category: string;
    amount: number;
    attachment_path?: string;
    created_at: string;
    updated_at: string;
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
    expenses: PaginatedData<Expense>;
    filters: {
        search?: string;
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
                title: 'Expenses',
                href: expensesIndex(),
            },
        ],
    },
});

const search = ref(props.filters.search ?? '');
const dateFilter = ref(props.filters.date ?? '');

watch([search, dateFilter], () => {
    router.get(expensesIndex.url(), { 
        search: search.value || undefined, 
        date: dateFilter.value || undefined 
    }, {
        preserveState: true,
        replace: true,
    });
});

function deleteExpense(expense: Expense) {
    if (confirm(`Are you sure you want to delete expense: "${expense.title}"?`)) {
        router.delete(expensesDestroy.url(expense.id));
    }
}
</script>

<template>
    <Head title="Expense List" />

    <AppContent>
        <Card>
            <CardHeader>
                <CardTitle>Expense List</CardTitle>
                <CardAction>
                    <div class="flex items-center gap-2">
                        <Input v-model="search" placeholder="Search by title..." class="w-64" />
                        <Input v-model="dateFilter" type="date" class="w-48" />
                        <Button as-child>
                            <Link :href="expensesCreate.url()">
                                + New Expense
                            </Link>
                        </Button>
                        <Button variant="outline" @click="router.reload()">
                            ↻ Refresh
                        </Button>
                    </div>
                </CardAction>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="px-4 py-3 text-left font-medium">Date</th>
                                <th class="px-4 py-3 text-left font-medium">Property</th>
                                <th class="px-4 py-3 text-left font-medium">Title</th>
                                <th class="px-4 py-3 text-left font-medium">Category</th>
                                <th class="px-4 py-3 text-right font-medium">Amount</th>
                                <th class="px-4 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="expenses.data.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                    No expenses found.
                                </td>
                            </tr>
                            <tr v-for="expense in expenses.data" :key="expense.id"
                                class="border-b transition-colors hover:bg-muted/50">
                                <td class="px-4 py-3">{{ expense.date }}</td>
                                <td class="px-4 py-3">{{ expense.property?.name }}</td>
                                <td class="px-4 py-3 font-medium">{{ expense.title }}</td>
                                <td class="px-4 py-3 capitalize">{{ expense.category }}</td>
                                <td class="px-4 py-3 text-right">{{ expense.amount.toLocaleString("id-ID", {
                                    style: "currency",
                                    currency: "IDR",
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="sm" as-child>
                                            <Link :href="expensesEdit.url(expense.id)">
                                                Edit
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="sm"
                                            class="text-destructive hover:text-destructive"
                                            @click="deleteExpense(expense)">
                                            Delete
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (same logic as receipts) -->
                <div v-if="expenses.last_page > 1" class="mt-4 flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Showing page {{ expenses.current_page }} of {{ expenses.last_page }}
                        ({{ expenses.total }} total)
                    </p>
                    <div class="flex items-center gap-1">
                        <template v-for="link in expenses.links" :key="link.label">
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
    </AppContent>
</template>
