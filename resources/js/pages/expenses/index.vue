<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardAction, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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

    <Card>
        <CardHeader shadow="none">
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
            <div class="overflow-x-auto rounded-md">
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/10">
                            <TableHead>Date</TableHead>
                            <TableHead>Property</TableHead>
                            <TableHead>Title</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead>Amount</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="expenses.data.length === 0">
                            <TableCell colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                No expenses found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="expense in expenses.data" :key="expense.id" class="hover:bg-muted/10">
                            <TableCell>{{ expense.date }}</TableCell>
                            <TableCell>{{ expense.property?.name }}</TableCell>
                            <TableCell>{{ expense.title }}</TableCell>
                            <TableCell>{{ expense.category }}</TableCell>
                            <TableCell>{{ Number(expense.amount).toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }) }}</TableCell>
                            <TableCell class="flex items-center justify-center gap-1">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="expensesEdit.url(expense.id)">
                                        Edit
                                    </Link>
                                </Button>
                                <Button variant="destructive" size="sm" @click="deleteExpense(expense)">
                                    Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
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
</template>
