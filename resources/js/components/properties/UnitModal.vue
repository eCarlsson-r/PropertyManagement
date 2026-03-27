<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { watch } from 'vue';
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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Room } from '@/pages/rooms/index.vue';

export interface Unit {
    id?: number;
    property_id?: number;
    name: string;
    floor: string;
    room_id: number;
    room?: Room;
    rentable: boolean;
    condition: string;
}

const open = defineModel<boolean>('open', { default: false });
const unit = defineModel<Unit | null>('unit');

defineProps<{
    rooms: Room[];
}>();

const form = useForm<Unit>({
    name: '',
    floor: '',
    room_id: 0,
    rentable: true,
    condition: 'clean'
});

// Watch for external unit changes to sync the form
watch(() => unit.value, (newUnit) => {
    if (newUnit) {
        form.name = newUnit.name;
        form.floor = newUnit.floor;
        form.room_id = newUnit.room_id;
    } else {
        form.reset();
    }
}, { immediate: true });

const submit = () => {
    unit.value = { ...form.data() };
    open.value = false;
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="outline">
                <Plus /> Tambah
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="submit" class="space-y-6">
                <DialogHeader>
                    <DialogTitle>Input Unit Properti</DialogTitle>
                    <DialogDescription>
                        Masukkan unit properti
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="name">Judul</Label>
                    <Input id="name" v-model="form.name" placeholder="Judul" required />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="room_id">Tipe Kamar</Label>
                    <Select id="room_id" v-model="form.room_id" placeholder="Tipe Kamar" class="w-full" required>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Tipe Kamar" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="room in rooms" :key="room.id" :value="room.id">
                                {{ room.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.room_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="floor">Lantai</Label>
                    <Input id="floor" v-model="form.floor" placeholder="Lantai" required />
                    <InputError :message="form.errors.floor" />
                </div>

                <DialogFooter>
                    <Button type="submit" :disabled="form.processing">Gunakan Lokasi</Button>
                    <DialogClose as-child>
                        <Button variant="outline">Batal</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
