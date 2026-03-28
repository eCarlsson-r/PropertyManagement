<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Property } from '@/pages/properties/index.vue';
import { index as propertiesIndex } from '@/routes/properties';

export interface RoomFormData {
    id?: number;
    property_id: number;
    name: string;
    area: number;
    daily_price: number;
    weekly_price: number;
    monthly_price: number;
    annual_price: number;
    notes?: string
}

const form = defineModel<InertiaForm<RoomFormData>>('form', { required: true });

defineProps<{
    properties: Property[];
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();

</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="grid gap-2">
            <Label for="property_id">Property</Label>
            <Select v-model="form.property_id">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Property" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="property in properties" :key="property.id" :value="property.id">
                        {{ property.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.property_id" />
        </div>

        <div class="grid gap-2">
            <Label for="name">Room Type Name</Label>
            <Input id="name" v-model="form.name" placeholder="Room Type Name" required />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="area">Area</Label>
            <Input id="area" v-model="form.area" placeholder="Area (m2)" required />
            <InputError :message="form.errors.area" />
        </div>

        <div class="grid gap-2">
            <Label for="daily_price">Daily Price</Label>
            <Input id="daily_price" v-model="form.daily_price" placeholder="Daily Price" required />
            <InputError :message="form.errors.daily_price" />
        </div>

        <div class="grid gap-2">
            <Label for="weekly_price">Weekly Price</Label>
            <Input id="weekly_price" v-model="form.weekly_price" placeholder="Weekly Price" required />
            <InputError :message="form.errors.weekly_price" />
        </div>

        <div class="grid gap-2">
            <Label for="monthly_price">Monthly Price</Label>
            <Input id="monthly_price" v-model="form.monthly_price" placeholder="Monthly Price" required />
            <InputError :message="form.errors.monthly_price" />
        </div>

        <div class="grid gap-2">
            <Label for="annual_price">Annual Price</Label>
            <Input id="annual_price" v-model="form.annual_price" placeholder="Annual Price" required />
            <InputError :message="form.errors.annual_price" />
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