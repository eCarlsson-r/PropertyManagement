<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppContent from '@/components/AppContent.vue';
import ExpenseForm from '@/components/expenses/ExpenseForm.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as expensesIndex, store as expensesStore } from '@/routes/expenses';
import type { Property } from '../properties/index.vue';

defineProps<{
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
                title: 'New Expense',
            },
        ],
    },
});

const form = useForm({
    date: new Date().toISOString().split('T')[0],
    property_id: '',
    title: '',
    category: 'others',
    amount: 0,
    attachment: null as File | null,
});

function submit() {
    form.post(expensesStore.url());
}
</script>

<template>
    <Head title="New Expense" />

    <AppContent>
        <div class="max-w-3xl mx-auto py-8">
            <Card>
                <CardHeader>
                    <CardTitle>Record New Expense</CardTitle>
                </CardHeader>
                <CardContent>
                    <ExpenseForm 
                        v-model:form="form" 
                        :properties="properties" 
                        submit-label="Catat Pengeluaran" 
                        @submit="submit" 
                    />
                </CardContent>
            </Card>
        </div>
    </AppContent>
</template>
