<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { currencies } from 'country-data-list';
import CountrySelect from '@/components/CountrySelect.vue';
import InputError from '@/components/InputError.vue';
import LocationModal from '@/components/properties/LocationModal.vue';
import type { Location } from '@/components/properties/LocationModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { index as propertiesIndex } from '@/routes/properties';

export interface PropertyFormData {
    id?: number;
    account_bank?: string;
    account_number?: string;
    account_owner?: string;
    name: string;
    currency: string;
    location: Location;
    notes?: string;
    owner_name: string;
    owner_country_code: string;
    owner_mobile: string;
    manager_name: string;
    manager_country_code: string;
    manager_mobile: string;
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

        <div class="flex gap-2">
            <Label>Lokasi</Label>
            <LocationModal v-model:location="form.location" />
            <Input v-model="form.location.city" readonly placeholder="Lokasi belum ditentukan" />
        </div>

        <div class="grid gap-2">
            <Label for="owner_name">Nama Pemilik</Label>
            <Input id="owner_name" v-model="form.owner_name" placeholder="Nama Pemilik" required />
            <InputError :message="form.errors.owner_name" />
        </div>

        <div class="grid gap-2">
            <Label for="owner_mobile">Nomor WA Pemilik</Label>
            <InputGroup>
                <InputGroupInput id="owner_mobile" v-model="form.owner_mobile" type="tel" placeholder="8123456789"
                    required class="flex-1" />
                <InputGroupAddon>
                    <CountrySelect v-model:countryCode="form.owner_country_code" attribute="code" />
                </InputGroupAddon>
            </InputGroup>
            <InputError :message="form.errors.owner_mobile" />
        </div>

        <div class="grid gap-2">
            <Label for="manager_name">Nama Pengelola Utama</Label>
            <Input id="manager_name" v-model="form.manager_name" placeholder="Pengelola Properti" required />
            <InputError :message="form.errors.manager_name" />
        </div>

        <div class="grid gap-2">
            <Label for="manager_mobile">Nomor WA Pengelola Utama</Label>
            <InputGroup>
                <InputGroupInput id="manager_mobile" v-model="form.manager_mobile" type="tel" placeholder="8123456789"
                    required class="flex-1" />
                <InputGroupAddon>
                    <CountrySelect v-model:countryCode="form.manager_country_code" attribute="code" />
                </InputGroupAddon>
            </InputGroup>
            <InputError :message="form.errors.manager_mobile" />
        </div>

        <div class="grid gap-2">
            <Label for="currency">Mata Uang</Label>
            <Select v-model="form.currency">
                <SelectTrigger id="currency">
                    <SelectValue placeholder="Pilih Mata Uang" />
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
            <Label for="account_owner">Nama Pemilik Rekening</Label>
            <Input id="account_owner" v-model="form.account_owner" placeholder="Nama Pemilik Rekening" required />
            <InputError :message="form.errors.account_owner" />
        </div>

        <div v-if="form.id" class="grid gap-2">
            <Label for="account_bank">Bank</Label>
            <Input id="account_bank" v-model="form.account_bank" placeholder="Bank" required />
            <InputError :message="form.errors.account_bank" />
        </div>

        <div v-if="form.id" class="grid gap-2">
            <Label for="account_number">Nomor Rekening</Label>
            <Input id="account_number" v-model="form.account_number" placeholder="Nomor Rekening" required />
            <InputError :message="form.errors.account_number" />
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
                <p v-if="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
            </Transition>
        </div>
    </form>
</template>
