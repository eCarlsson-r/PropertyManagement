<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { index as expensesIndex } from '@/routes/expenses';
import type { Property } from '../../pages/properties/index.vue';

export interface ExpenseFormData {
    id?: number;
    date: string;
    property_id: number | string;
    title: string;
    category: string;
    amount: number;
    attachment?: File | null;
}

const form = defineModel<InertiaForm<ExpenseFormData>>('form', { required: true });

defineProps<{
    properties: Property[];
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();

const categories = [
    { value: 'marketing', label: 'Marketing' },
    { value: 'grocery', label: 'Groceries' },
    { value: 'electricity', label: 'Electricity' },
    { value: 'water', label: 'Water' },
    { value: 'salary', label: 'Employee Salary' },
    { value: 'fixing', label: 'Repair/Maintenance' },
    { value: 'supplies', label: 'Supplies' },
    { value: 'others', label: 'Others' },
];
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="grid gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="expense-date">Date</Label>
                    <Input id="expense-date" type="date" v-model="form.date" required />
                </div>
                <div class="grid gap-2">
                    <Label for="expense-property">Property</Label>
                    <Select v-model="form.property_id">
                        <SelectTrigger id="expense-property" class="w-full">
                            <SelectValue placeholder="Select Property" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="property in properties" :key="property.id"
                                :value="property.id.toString()">
                                {{ property.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="expense-title">Expense Title</Label>
                <Input id="expense-title" v-model="form.title" placeholder="e.g. Promotion Payment" required />
            </div>

            <div class="grid gap-2">
                <Label for="expense-category">Expense Category</Label>
                <Select v-model="form.category">
                    <SelectTrigger id="expense-category" class="w-full">
                        <SelectValue placeholder="Select Category" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="cat in categories" :key="cat.value" :value="cat.value">
                            {{ cat.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="grid gap-2">
                <Label for="amount">Total (Rp)</Label>
                <InputGroup>
                    <InputGroupAddon>Rp.</InputGroupAddon>
                    <InputGroupInput id="amount" type="number" v-model="form.amount" required />
                </InputGroup>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t">
            <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
            <Button variant="outline" as-child>
                <Link :href="expensesIndex.url()">Cancel</Link>
            </Button>
        </div>
    </form>
</template>
