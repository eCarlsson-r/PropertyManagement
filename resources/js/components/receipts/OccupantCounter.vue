<script setup lang="ts">
import InputGroup from '../ui/input-group/InputGroup.vue';
import InputGroupAddon from '../ui/input-group/InputGroupAddon.vue';
import InputGroupInput from '../ui/input-group/InputGroupInput.vue';
import Label from '../ui/label/Label.vue';


const model = defineModel<number>();

defineProps<{
    label: string;
    id?: string;
    name?: string;
    min?: number;
    max?: number;
}>();
</script>

<template>
    <div class="grid gap-2">
        <InputGroup>
            <InputGroupInput type="number" class="form-control" :id="id" :name="name" :min="min ?? 0" :max="max ?? 10"
                v-model="model" />
            <InputGroupAddon align="inline-end">
                <Label :for="id">
                    {{ label }}
                </Label>
            </InputGroupAddon>
        </InputGroup>
    </div>
</template>
