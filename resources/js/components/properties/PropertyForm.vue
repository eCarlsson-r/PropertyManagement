<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { index as propertiesIndex } from '@/routes/properties';

export interface Location {
    id: number;
    address: string;
    country: string;
    province: string;
    city: string;
    district: string;
    subdistrict: string;
    postal: string;
}

export interface PropertyFormData {
    name: string;
    currency: string;
    mobile: string;
    location_id: string;
    notes: string;
}

const form = defineModel<InertiaForm<PropertyFormData>>('form', { required: true });

defineProps<{
    locations: Location[];
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="grid gap-2">
            <Label for="name">Nama Properti</Label>
            <Input id="name" v-model="form.name" placeholder="Nama Properti" required />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="location_id">Lokasi</Label>
            <Select v-model="form.location_id">
                <SelectTrigger id="location_id">
                    <SelectValue placeholder="Pilih Lokasi" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="location in locations" :key="location.id" :value="String(location.id)">
                        {{ location.address }}, {{ location.city }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.location_id" />
        </div>

        <div class="grid gap-2">
            <Label for="mobile">No. WA Pengelola</Label>
            <div class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">+62</span>
                <Input id="mobile" v-model="form.mobile" type="tel" placeholder="8123456789" class="flex-1" required />
            </div>
            <InputError :message="form.errors.mobile" />
        </div>

        <div class="grid gap-2">
            <Label for="currency">Mata Uang</Label>
            <Select v-model="form.currency">
                <SelectTrigger id="currency">
                    <SelectValue placeholder="Pilih Mata Uang" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="IDR">Indonesian Rupiah (IDR)</SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.currency" />
        </div>

        <div class="grid gap-2">
            <Label for="notes">Catatan</Label>
            <Input id="notes" v-model="form.notes" placeholder="Catatan properti" />
            <InputError :message="form.errors.notes" />
        </div>

        <div class="flex items-center gap-4">
            <Button :disabled="form.processing">{{ submitLabel }}</Button>
            <Button variant="outline" as-child>
                <Link :href="propertiesIndex.url()">Batal</Link>
            </Button>
            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
            </Transition>
        </div>
    </form>
</template>
