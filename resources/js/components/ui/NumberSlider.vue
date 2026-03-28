<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue: number;
    min?: number;
    max?: number;
    label?: string;
    class?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: number];
}>();

const containerRef = ref<HTMLElement | null>(null);
const numbers = computed(() => {
    const min = props.min ?? 1;
    const max = props.max ?? 30;
    return Array.from({ length: max - min + 1 }, (_, i) => min + i);
});



const isDragging = ref(false);
const startX = ref(0);
const startScrollLeft = ref(0);

const startDrag = (e: MouseEvent) => {
    if (!containerRef.value) return;
    isDragging.value = true;
    startX.value = e.pageX - containerRef.value.offsetLeft;
    startScrollLeft.value = containerRef.value.scrollLeft;
    containerRef.value.style.scrollSnapType = 'none';
    containerRef.value.style.cursor = 'grabbing';
};

const stopDrag = () => {
    if (!isDragging.value || !containerRef.value) return;
    isDragging.value = false;
    containerRef.value.style.scrollSnapType = 'x mandatory';
    containerRef.value.style.cursor = 'grab';
    
    // Trigger one last check to snap correctly
    handleScroll();
};

const onDrag = (e: MouseEvent) => {
    if (!isDragging.value || !containerRef.value) return;
    e.preventDefault();
    const x = e.pageX - containerRef.value.offsetLeft;
    const walk = (x - startX.value) * 1.5; 
    containerRef.value.scrollLeft = startScrollLeft.value - walk;
};

const scrollIntoView = (value: number) => {
    if (!containerRef.value || isDragging.value) return;
    const item = containerRef.value.querySelector(`[data-value="${value}"]`) as HTMLElement;
    if (item) {
        item.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
};

let isScrolling = false;
let scrollTimeout: any = null;

const handleScroll = () => {
    if (!containerRef.value) return;
    
    const container = containerRef.value;
    const containerRect = container.getBoundingClientRect();
    const centerX = containerRect.left + containerRect.width / 2;
    
    let closestNum = props.modelValue;
    let minDistance = Infinity;
    
    const items = container.querySelectorAll('[data-value]');
    items.forEach((item) => {
        const rect = item.getBoundingClientRect();
        const itemCenterX = rect.left + rect.width / 2;
        const distance = Math.abs(centerX - itemCenterX);
        
        if (distance < minDistance) {
            minDistance = distance;
            closestNum = parseInt((item as HTMLElement).dataset.value || '1');
        }
    });
    
    if (closestNum !== props.modelValue) {
        emit('update:modelValue', closestNum);
    }
};

const onScroll = () => {
    if (isDragging.value) return;
    isScrolling = true;
    if (scrollTimeout) clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        handleScroll();
        isScrolling = false;
    }, 100);
};

function selectNumber(num: number) {
    if (props.modelValue === num) return;
    isScrolling = false; 
    emit('update:modelValue', num);
}

watch(() => props.modelValue, (newVal) => {
    if (!isScrolling && !isDragging.value) {
        nextTick(() => scrollIntoView(newVal));
    }
});

onMounted(() => {
    nextTick(() => scrollIntoView(props.modelValue));
    window.addEventListener('mouseup', stopDrag);
    window.addEventListener('mousemove', onDrag);
});

onUnmounted(() => {
    window.removeEventListener('mouseup', stopDrag);
    window.removeEventListener('mousemove', onDrag);
});
</script>

<template>
    <div :class="cn('relative w-full overflow-hidden rounded-lg border bg-background shadow-sm select-none', props.class)">
        <!-- Center Highlight -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-10 h-10 border-2 border-primary rounded-full pointer-events-none z-10 transition-all"></div>
        
        <div 
            ref="containerRef"
            @scroll="onScroll"
            @mousedown="startDrag"
            class="flex items-center gap-4 overflow-x-auto py-3 scrollbar-hide snap-x snap-mandatory cursor-grab active:cursor-grabbing"
            style="padding-left: calc(50% - 20px); padding-right: calc(50% - 20px);"
        >
            <button
                v-for="num in numbers"
                :key="num"
                :data-value="num"
                type="button"
                @click="selectNumber(num)"
                :class="cn(
                    'shrink-0 w-10 h-10 flex items-center justify-center transition-all snap-center rounded-full',
                    'text-base font-semibold',
                    modelValue === num 
                        ? 'text-primary scale-125 z-20' 
                        : 'text-muted-foreground hover:text-foreground opacity-50'
                )"
            >
                {{ num }}
            </button>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
