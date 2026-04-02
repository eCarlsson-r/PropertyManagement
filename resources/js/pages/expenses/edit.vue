<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppContent from '@/components/AppContent.vue';
import ExpenseForm from '@/components/expenses/ExpenseForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as expensesIndex, update as expensesUpdate } from '@/routes/expenses';
import type { Property } from '../properties/index.vue';
import type { Expense } from './index.vue';

const props = defineProps<{
    expense: Expense;
    properties: Property[];
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
            {
                title: 'Edit Expense',
            },
        ],
    },
});

const form = useForm({
    id: props.expense.id,
    date: props.expense.date,
    property_id: props.expense.property_id.toString(),
    title: props.expense.title,
    category: props.expense.category,
    amount: props.expense.amount,
    attachment: null as File | null,
});

function submit() {
    form.put(expensesUpdate.url(props.expense.id));
}
</script>

<template>

    <Head title="Edit Expense" />

    <AppContent>
        <Card>
                <CardHeader>
                    <CardTitle>Edit Expense</CardTitle>
                </CardHeader>
                <CardContent>
                    <ExpenseForm v-model:form="form" :properties="properties" submit-label="Update Expense"
                        @submit="submit" />
                </CardContent>
            </Card>
    </AppContent>
</template>
