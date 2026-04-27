<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Trash } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import type { Location } from '@/components/properties/LocationModal.vue';
import PropertyForm from '@/components/properties/PropertyForm.vue';
import type { Rule } from '@/components/properties/RuleModal.vue';
import RuleModal from '@/components/properties/RuleModal.vue';
import type { Unit } from '@/components/properties/UnitModal.vue';
import UnitModal from '@/components/properties/UnitModal.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes';
import { index as propertiesIndex, update as propertiesUpdate } from '@/routes/properties';
import type { Room } from '../rooms/index.vue';

interface Property {
    id: number;
    name: string;
    currency: string;
    owner_name: string;
    owner_country_code: string;
    owner_mobile: string;
    manager_name: string;
    manager_country_code: string;
    manager_mobile: string;
    account_owner: string;
    account_bank: string;
    account_number: string;
    notes: string;
    location: Location;
    rules?: Rule[];
    units?: Unit[];
}

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(value);
};

const props = defineProps<{
    property: Property;
    locations: Location[];
    rooms: Room[];
}>();

const selectedRule = ref<Rule | null>(null);
const isRuleModalOpen = ref(false);
const selectedUnit = ref<Unit | null>(null);
const isUnitModalOpen = ref(false);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Properties',
                href: propertiesIndex(),
            },
            {
                title: 'Edit Property',
            },
        ],
    },
});

const form = useForm({
    name: props.property.name,
    currency: props.property.currency,
    owner_name: props.property.owner_name,
    owner_country_code: props.property.owner_country_code,
    owner_mobile: props.property.owner_mobile,
    manager_name: props.property.manager_name,
    manager_country_code: props.property.manager_country_code,
    manager_mobile: props.property.manager_mobile,
    location: props.property.location,
    notes: props.property.notes,
    account_owner: props.property.account_owner,
    account_bank: props.property.account_bank,
    account_number: props.property.account_number,
    rules: props.property.rules,
    units: props.property.units
});

// Inside edit.vue script
function addRule(rule: Rule) {
    if (!form.rules) {
        form.rules = [];
    }

    if (selectedRule.value && selectedRule.value.id) {
        const index = form.rules.findIndex((r) => r.id === rule.id);

        if (index !== -1) {
            form.rules[index] = rule;
        }
    } else {
        form.rules.push({ ...rule });
    }
}

function addUnit(unit: Unit) {
    if (!form.units) {
        form.units = [];
    }

    if (selectedUnit.value && selectedUnit.value.id) {
        const index = form.units.findIndex((u) => u.id === unit.id);

        if (index !== -1) {
            form.units[index] = unit;
        }
    } else {
        form.units.push({ ...unit, room: props.rooms.find((r) => r.id === unit.room_id) });
    }
}

function removeRule(rule: Rule) {
    if (form.rules) {
        form.rules = form.rules.filter((r) => r.id !== rule.id);
    }
}

function removeUnit(unit: Unit) {
    if (form.units) {
        form.units = form.units.filter((u) => u.id !== unit.id);
    }
}

watch(selectedRule, (newVal) => {
    if (newVal) {
        addRule(newVal);
    }
});

watch(selectedUnit, (newVal) => {
    if (newVal) {
        addUnit(newVal);
    }
});

function submit() {
    form.put(propertiesUpdate.url(props.property.id));
}
</script>

<template>

    <Head title="Edit Property" />

    <div class="grid grid-cols-2 gap-x-6 space-y-6">
        <div>
            <Card>
                <CardHeader>
                    <CardTitle>Edit Property</CardTitle>
                </CardHeader>
                <CardContent>
                    <PropertyForm v-model:form="form" :locations="locations" submit-label="Update" @submit="submit" />
                </CardContent>
            </Card>
        </div>

        <div class="space-y-6">
            <Card>
                <CardHeader class="flex justify-between">
                    <CardTitle>Property Rules</CardTitle>
                    <RuleModal v-model:rule="selectedRule" v-model:open="isRuleModalOpen" />
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Title</TableHead>
                                <TableHead>Description</TableHead>
                                <TableHead>Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="rule in (form.rules ?? [])" :key="rule.id"
                                class="cursor-pointer hover:bg-neutral-100 p-2 rounded">
                                <TableCell>{{ rule.title }}</TableCell>
                                <TableCell>{{ rule.description }}</TableCell>
                                <TableCell class="flex space-x-2 items-center">
                                    <Button variant="outline" @click="selectedRule = rule; isRuleModalOpen = true">
                                        <Pencil />
                                    </Button>
                                    <Button variant="destructive" @click="removeRule(rule)">
                                        <Trash />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex justify-between">
                    <CardTitle>Property Units</CardTitle>
                    <UnitModal v-model:unit="selectedUnit" v-model:open="isUnitModalOpen" :rooms="rooms ?? []" />
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Daily Rent</TableHead>
                                <TableHead>Weekly Rent</TableHead>
                                <TableHead>Monthly Rent</TableHead>
                                <TableHead>Annual Rent</TableHead>
                                <TableHead>Rentable</TableHead>
                                <TableHead>Condition</TableHead>
                                <TableHead>Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="unit in (form.units ?? [])" :key="unit.id">
                                <TableCell>{{ unit.name }}</TableCell>
                                <TableCell>{{ formatCurrency(unit.room?.daily_price || 0) }}</TableCell>
                                <TableCell>{{ formatCurrency(unit.room?.weekly_price || 0) }}</TableCell>
                                <TableCell>{{ formatCurrency(unit.room?.monthly_price || 0) }}</TableCell>
                                <TableCell>{{ formatCurrency(unit.room?.annual_price || 0) }}</TableCell>
                                <TableCell>
                                    <Switch v-model="unit.rentable" disabled />
                                </TableCell>
                                <TableCell>
                                    <Select v-model="unit.condition" disabled>
                                        <SelectTrigger>
                                            <SelectValue :placeholder="unit.condition" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="clean">Clean</SelectItem>
                                            <SelectItem value="dirty">Dirty</SelectItem>
                                            <SelectItem value="maintenance">Maintenance</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </TableCell>
                                <TableCell class="flex space-x-2 items-center">
                                    <Button variant="outline" @click="selectedUnit = unit; isUnitModalOpen = true">
                                        <Pencil />
                                    </Button>
                                    <Button variant="destructive" @click="removeUnit(unit)">
                                        <Trash />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </div>
</template>