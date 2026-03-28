<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { currencies } from 'country-data-list';
import CountrySelect from '@/components/CountrySelect.vue';
import InputError from '@/components/InputError.vue';
import type { Unit } from '@/components/properties/UnitModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Property } from '@/pages/properties/index.vue';
import { index as propertiesIndex } from '@/routes/properties';

export interface TenantFormData {
    id?: number;
    name: string;
    country_code: string;
    mobile: string;
    email?: string;
    cycle: string;
    birth_date?: string;
    gender?: string;
    marital_status?: string;
    address?: string;
    account_bank?: string;
    account_number?: string;
    account_owner?: string;
    currency: string;
    notes?: string;
    emergency_contact_name: string;
    emergency_contact_relationship: string;
    emergency_contact_country_code: string;
    emergency_contact_mobile: string;
    deposit?: number;
    deposit_bank?: string;
    deposit_number?: string;
    unit: {
        property_id: number;
        room_id: number;
    }
}

const form = defineModel<InertiaForm<TenantFormData>>('form', { required: true });

defineProps<{
    properties: Property[];
    units: Unit[];
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();

</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="grid gap-2">
            <Label for="name">Full Name</Label>
            <Input id="name" v-model="form.name" placeholder="Full Name" required />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="mobile">WhatsApp Number</Label>
            <InputGroup>
                <InputGroupInput id="mobile" v-model="form.mobile" type="tel" placeholder="8123456789" required
                    class="flex-1" />
                <InputGroupAddon>
                    <CountrySelect v-model:countryCode="form.country_code" attribute="code" />
                </InputGroupAddon>
            </InputGroup>
            <InputError :message="form.errors.mobile" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email Address</Label>
            <Input id="email" v-model="form.email" placeholder="Email" />
            <InputError :message="form.errors.email" />
        </div>

        <div class="flex gap-2">
            <Label for="property_id">Unit</Label>
            <Select v-model="form.unit.property_id">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Property" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="property in properties" :key="property.id" :value="property.id">
                        {{ property.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <Select v-model="form.unit.room_id">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Room" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="unit in units" :key="unit.id" :value="unit.id || 0">
                        {{ unit.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>

        <div class="flex gap-2">
            <Label>Lease Cycle</Label>
            <Select v-model="form.cycle">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Lease Cycle" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="daily">Daily</SelectItem>
                    <SelectItem value="weekly">Weekly</SelectItem>
                    <SelectItem value="monthly">Monthly</SelectItem>
                    <SelectItem value="annual">Annual</SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.cycle" />
        </div>

        <div class="grid gap-2">
            <Label for="birth_date">Birth Date</Label>
            <Input id="birth_date" v-model="form.birth_date" type="date" />
            <InputError :message="form.errors.birth_date" />
        </div>

        <div class="flex gap-2">
            <Label>Gender</Label>
            <Select v-model="form.gender">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Gender" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="male">Male</SelectItem>
                    <SelectItem value="female">Female</SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.gender" />
        </div>

        <div class="flex gap-2">
            <Label>Marital Status</Label>
            <Select v-model="form.marital_status">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Marital Status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="single">Single</SelectItem>
                    <SelectItem value="married">Married</SelectItem>
                    <SelectItem value="divorced">Divorced</SelectItem>
                    <SelectItem value="widowed">Widowed</SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.marital_status" />
        </div>

        <div class="grid gap-2">
            <Label for="address">Address as per ID</Label>
            <Input id="address" v-model="form.address" placeholder="Address" required />
            <InputError :message="form.errors.address" />
        </div>

        <div class="grid gap-2">
            <Label for="emergency_contact_name">Emergency Contact Name</Label>
            <Input id="emergency_contact_name" v-model="form.emergency_contact_name" placeholder="Contact Name"
                required />
            <InputError :message="form.errors.emergency_contact_name" />
        </div>

        <div class="flex gap-2">
            <Label>Emergency Contact Relationship</Label>
            <Select v-model="form.emergency_contact_relationship">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Relationship" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="parent">Parent</SelectItem>
                    <SelectItem value="spouse">Spouse</SelectItem>
                    <SelectItem value="sibling">Sibling</SelectItem>
                    <SelectItem value="grandparent">Grandparent</SelectItem>
                    <SelectItem value="relative">Relative</SelectItem>
                    <SelectItem value="friend">Friend</SelectItem>
                    <SelectItem value="other">Other</SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.emergency_contact_relationship" />
        </div>

        <div class="grid gap-2">
            <Label for="emergency_contact_mobile">Emergency Contact No. (WA)</Label>
            <InputGroup>
                <InputGroupInput id="emergency_contact_mobile" v-model="form.emergency_contact_mobile" type="tel"
                    placeholder="8123456789" required class="flex-1" />
                <InputGroupAddon>
                    <CountrySelect v-model:countryCode="form.emergency_contact_country_code" attribute="code" />
                </InputGroupAddon>
            </InputGroup>
            <InputError :message="form.errors.emergency_contact_mobile" />
        </div>

        <div class="grid gap-2">
            <Label for="currency">Currency</Label>
            <Select v-model="form.currency">
                <SelectTrigger id="currency" class="w-full">
                    <SelectValue placeholder="Select Currency" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="currency in currencies.all" :key="currency.code" :value="currency.code">
                        {{ currency.name }} ({{ currency.code }})
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.currency" />
        </div>

        <div v-if="form.id" class="grid gap-2">
            <Label for="deposit">Deposit</Label>
            <Input id="deposit" v-model="form.deposit" placeholder="Deposit" required />
            <InputError :message="form.errors.deposit" />
        </div>

        <div v-if="form.id" class="grid gap-2">
            <Label for="deposit_bank">Deposit Refund Bank</Label>
            <Input id="deposit_bank" v-model="form.deposit_bank" placeholder="Bank" required />
            <InputError :message="form.errors.deposit_bank" />
        </div>

        <div v-if="form.id" class="grid gap-2">
            <Label for="deposit_number">Deposit Refund Account Number</Label>
            <Input id="deposit_number" v-model="form.deposit_number" placeholder="Account Number" required />
            <InputError :message="form.errors.deposit_number" />
        </div>

        <div class="grid gap-2">
            <Label for="notes">Notes</Label>
            <Input id="notes" v-model="form.notes" placeholder="Property notes" />
            <InputError :message="form.errors.notes" />
        </div>

        <div class="flex items-center gap-4">
            <Button :disabled="form.processing">{{ submitLabel }}</Button>
            <Button variant="outline" as-child>
                <Link :href="propertiesIndex.url()">Cancel</Link>
            </Button>
            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                <p v-if="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
            </Transition>
        </div>
    </form>
</template>
