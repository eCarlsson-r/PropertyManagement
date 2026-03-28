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
import Textarea from '../ui/textarea/Textarea.vue';

export interface Rule {
    id?: number;
    title: string;
    description: string;
}

const open = defineModel<boolean>('open', { default: false });
const rule = defineModel<Rule | null>('rule', { default: null });

const form = useForm<Rule>({
    title: '',
    description: '',
});

// Watch for external rule changes to sync the form
watch(() => rule.value, (newRule) => {
    if (newRule) {
        form.title = newRule.title;
        form.description = newRule.description;
    } else {
        form.title = '';
        form.description = '';
    }
}, { immediate: true });

const submit = () => {
    rule.value = { ...form.data() };
    open.value = false;
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="outline" @click="rule = null">
                <Plus /> Add
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="submit" class="space-y-6">
                <DialogHeader>
                    <DialogTitle>Add Property Rule</DialogTitle>
                    <DialogDescription>
                        Enter property rule details
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" placeholder="Title" required />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" v-model="form.description" placeholder="Description" required />
                    <InputError :message="form.errors.description" />
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
