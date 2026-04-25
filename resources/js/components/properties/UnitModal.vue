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
import { Switch } from '@/components/ui/switch';
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
                <Plus /> Add
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="submit" class="space-y-6">
                <DialogHeader>
                    <DialogTitle>Add Property Unit</DialogTitle>
                    <DialogDescription>
                        Enter property unit details
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="name">Title</Label>
                    <Input id="name" v-model="form.name" placeholder="Title" required />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="room_id">Room Type</Label>
                    <Select id="room_id" v-model="form.room_id" placeholder="Room Type" class="w-full" required>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Room Type" />
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
                    <Label for="floor">Floor</Label>
                    <Input id="floor" v-model="form.floor" placeholder="Floor" required />
                    <InputError :message="form.errors.floor" />
                </div>

                <div class="grid gap-2">
                    <Label for="rentable">Rentable</Label>
                    <Switch id="rentable" v-model="form.rentable" required />
                    <InputError :message="form.errors.rentable" />
                </div>

                <div class="grid gap-2">
                    <Label for="condition">Condition</Label>
                    <Select id="condition" v-model="form.condition" placeholder="Condition" class="w-full" required>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Condition" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="new">New</SelectItem>
                            <SelectItem value="good">Good</SelectItem>
                            <SelectItem value="poor">Poor</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.condition" />
                </div>

                <DialogFooter>
                    <Button type="submit" :disabled="form.processing">Save</Button>
                    <DialogClose as-child>
                        <Button variant="outline">Cancel</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
