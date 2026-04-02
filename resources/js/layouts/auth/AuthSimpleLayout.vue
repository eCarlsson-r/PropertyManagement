<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Card, CardContent } from '@/components/ui/card';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const page = usePage();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 bg-slate-950 p-6 selection:bg-cyan-500/30 md:p-10 overflow-hidden">
        <!-- Background Ambient Glows -->
        <div class="absolute top-0 -left-4 w-96 h-96 bg-cyan-500/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-0 -right-4 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px] animate-pulse"
            style="animation-delay: 2s;"></div>

        <div class="z-10 w-full max-w-md">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="home()" class="flex flex-col items-center gap-2 font-medium group">
                        <div
                            class="mb-1 flex h-16 w-16 items-center justify-center rounded-2xl bg-cyan-500/10 border border-cyan-500/20 group-hover:scale-110 transition-transform duration-300">
                            <AppLogoIcon class="size-10 text-cyan-400 drop-shadow-[0_0_8px_rgba(34,211,238,0.5)]" />
                        </div>
                        <span class="text-3xl font-black tracking-tighter text-white">
                            Urus<span
                                class="text-transparent bg-clip-text bg-linear-to-r from-cyan-400 to-blue-500 italic">Properti</span>
                        </span>
                    </Link>
                </div>

                <Card class="glass border-white/10 shadow-2xl relative group overflow-hidden">
                    <div class="absolute inset-0 bg-linear-to-br from-cyan-500/5 to-transparent pointer-events-none">
                    </div>

                    <CardContent class="p-8 space-y-6 relative">
                        <div class="space-y-2 text-center mb-6">
                            <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
                            <p class="text-center text-sm text-slate-400">
                                {{ description }}
                            </p>
                        </div>
                        <slot />
                    </CardContent>
                </Card>

                <p v-if="!page.props.auth.user"
                    class="text-[10px] text-center text-slate-600 uppercase font-bold tracking-widest">
                    © 2026 UrusProperti. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</template>
