<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex items-center gap-2">
        <div
            class="flex aspect-square size-9 items-center justify-center rounded-xl bg-primary/10 text-primary dark:bg-primary/20">
            <AppLogoIcon class="size-6 transition-transform group-hover:scale-110" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-sm">
            <span class="leading-tight font-bold text-lg tracking-tight">Urus<span
                    class="text-primary italic">Properti</span></span>
        </div>
    </div>
</template>
