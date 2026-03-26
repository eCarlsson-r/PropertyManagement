<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/locations';

export interface LocationFormData {
    id: number;
    address: string;
    country: string;
    province: string;
    city: string;
    district: string;
    subdistrict: string;
    postal: string;
}

const form = defineModel<InertiaForm<LocationFormData>>('form', { required: true });

defineProps<{
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="grid gap-2">
            <Label for="address">Alamat</Label>
            <Input id="address" v-model="form.address" placeholder="Alamat" required />
            <InputError :message="form.errors.address" />
        </div>

        <div class="grid gap-2">
            <Label for="country">Negara</Label>
            <Input id="country" v-model="form.country" placeholder="Negara" required />
            <InputError :message="form.errors.country" />
        </div>

        <div class="grid gap-2">
            <Label for="province">Provinsi</Label>
            <Input id="province" v-model="form.province" placeholder="Provinsi" required />
            <InputError :message="form.errors.province" />
        </div>

        <div class="grid gap-2">
            <Label for="city">Kota</Label>
            <Input id="city" v-model="form.city" placeholder="Kota" required />
            <InputError :message="form.errors.city" />
        </div>

        <div class="grid gap-2">
            <Label for="district">Kecamatan / Desa</Label>
            <Input id="district" v-model="form.district" placeholder="Kecamatan" required />
            <InputError :message="form.errors.district" />
        </div>

        <div class="grid gap-2">
            <Label for="subdistrict">Kelurahan</Label>
            <Input id="subdistrict" v-model="form.subdistrict" placeholder="Kelurahan" required />
            <InputError :message="form.errors.subdistrict" />
        </div>

        <div class="grid gap-2">
            <Label for="postal">Kode Pos</Label>
            <Input id="postal" v-model="form.postal" placeholder="Kode Pos" required />
            <InputError :message="form.errors.postal" />
        </div>

        <div class="flex items-center gap-4">
            <Button :disabled="form.processing">{{ submitLabel }}</Button>
            <Button variant="outline" as-child>
                <Link :href="index.url()">Batal</Link>
            </Button>
            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
            </Transition>
        </div>
    </form>
</template>
