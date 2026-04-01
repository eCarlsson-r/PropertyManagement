<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        viewBox="0 0 120 100"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        :class="className"
        v-bind="$attrs"
    >
        <!-- Ground -->
        <path d="M10 90H110" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        
        <!-- Trees -->
        <g stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M20 90V75M20 75C17 75 15 72 15 70C15 65 20 60 20 60C20 60 25 65 25 70C25 72 23 75 20 75Z" />
            <path d="M35 90V80M35 80C32 80 30 78 30 76C30 72 35 68 35 68C35 68 40 72 40 76C40 78 38 80 35 80Z" />
        </g>

        <!-- Sun -->
        <circle cx="35" cy="35" r="18" stroke="currentColor" stroke-width="1.5" />
        <circle cx="45" cy="30" r="15" fill="currentColor" fill-opacity="0.2" class="text-accent" />

        <!-- Houses -->
        <g stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round">
            <!-- Left House -->
            <path d="M45 90V65L65 50L85 65V90" />
            <!-- Right House -->
            <path d="M75 90V55L100 40L125 55V90" class="translate-x-[-15px]" />
        </g>
    </svg>
</template>
