<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CountrySelect from '@/components/CountrySelect.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export interface Location {
    id?: number;
    address: string;
    country: string;
    province: string;
    city: string;
    district: string;
    subdistrict: string;
    postal: string;
}

const props = defineProps<{
    location: Location;
}>();
const isDialogOpen = ref(false);

const form = useForm<Location>(props.location);

const emit = defineEmits<{
    'update:location': [location: Location];
}>();

const submit = () => {
    emit('update:location', { ...form.data() });
    isDialogOpen.value = false;
}
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <Button variant="outline">
                Add Location
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="submit" class="space-y-6">
                <DialogHeader>
                    <DialogTitle>Add Location</DialogTitle>
                    <DialogDescription>
                        Enter property location details
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="address">Address</Label>
                    <Input id="address" v-model="form.address" placeholder="Address" required />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="grid gap-2">
                    <Label for="country">Country</Label>
                    <CountrySelect v-model:countryCode="form.country" attribute="name" />
                    <InputError :message="form.errors.country" />
                </div>

                <div class="grid gap-2">
                    <Label for="province">Province</Label>
                    <Input id="province" v-model="form.province" placeholder="Province" required />
                    <InputError :message="form.errors.province" />
                </div>

                <div class="grid gap-2">
                    <Label for="city">City</Label>
                    <Input id="city" v-model="form.city" placeholder="City" required />
                    <InputError :message="form.errors.city" />
                </div>

                <div class="grid gap-2">
                    <Label for="district">District / Village</Label>
                    <Input id="district" v-model="form.district" placeholder="District" required />
                    <InputError :message="form.errors.district" />
                </div>

                <div class="grid gap-2">
                    <Label for="subdistrict">Sub-district</Label>
                    <Input id="subdistrict" v-model="form.subdistrict" placeholder="Sub-district" required />
                    <InputError :message="form.errors.subdistrict" />
                </div>

                <div class="grid gap-2">
                    <Label for="postal">Postal Code</Label>
                    <Input id="postal" v-model="form.postal" placeholder="Postal Code" required />
                    <InputError :message="form.errors.postal" />
                </div>
                <DialogFooter>
                    <Button type="submit" :disabled="form.processing">Use Location</Button>
                    <DialogClose as-child>
                        <Button variant="outline">Cancel</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
