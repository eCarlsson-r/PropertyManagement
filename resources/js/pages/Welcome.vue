<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, Building2, ShieldCheck } from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard, login, register } from '@/routes';

defineProps<{
    canRegister: boolean;
}>();

const page = usePage();
</script>

<template>

    <Head title="Welcome to UrusProperti" />

    <div
        class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden bg-slate-950 text-slate-200 selection:bg-cyan-500/30">
        <!-- Background Ambient Glows -->
        <div class="absolute top-0 -left-4 w-96 h-96 bg-cyan-500/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-0 -right-4 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px] animate-pulse"
            style="animation-delay: 2s;"></div>

        <div class="z-10 w-full max-w-5xl px-6 py-12 flex flex-col lg:flex-row items-center gap-12 lg:gap-24">

            <!-- Marketing Side -->
            <div class="flex-1 space-y-8 text-center lg:text-left">
                <div class="space-y-4">
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-6">
                        <AppLogoIcon class="size-16 text-cyan-400 drop-shadow-[0_0_15px_rgba(34,211,238,0.5)]" />
                        <h1 class="text-5xl lg:text-7xl font-black tracking-tighter">
                            Urus<span
                                class="text-transparent bg-clip-text bg-linear-to-r from-cyan-400 to-blue-500 italic">Properti</span>
                        </h1>
                    </div>
                    <p class="text-xl text-slate-400 max-w-xl leading-relaxed">
                        The high-performance dashboard for professional property owners.
                        Intelligence, precision, and visual excellence in one ecosystem.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                    <div class="flex items-start gap-3 text-left">
                        <div class="p-2 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                            <Building2 class="size-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-100 italic">Full Visibility</h3>
                            <p class="text-sm text-slate-500">Track units, tenants, and occupancy in real-time.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 text-left">
                        <div class="p-2 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20">
                            <ShieldCheck class="size-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-100 italic">Cashflow Security</h3>
                            <p class="text-sm text-slate-500">Automated receipts and expense monitoring.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auth Side -->
            <div class="w-full max-w-md">
                <Card class="glass border-white/10 shadow-2xl overflow-hidden relative group">
                    <div class="absolute inset-0 bg-linear-to-br from-cyan-500/5 to-transparent pointer-events-none">
                    </div>

                    <CardContent class="p-10 space-y-8 relative">
                        <div class="text-center space-y-2">
                            <h2 class="text-2xl font-bold text-white">
                                {{ page.props.auth.user ? 'Welcome Back!' : 'Get Started' }}
                            </h2>
                            <p class="text-slate-400 text-sm">
                                {{ page.props.auth.user ? 'You are logged in as ' + page.props.auth.user.name :
                                    'Access your property command center.' }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-4">
                            <template v-if="page.props.auth.user">
                                <Button as-child size="lg" class="neon-glow font-bold text-lg group">
                                    <Link :href="dashboard()">
                                        Go to dashboard
                                        <ArrowRight
                                            class="ml-2 size-5 group-hover:translate-x-1 transition-transform" />
                                    </Link>
                                </Button>
                            </template>
                            <template v-else>
                                <Button as-child size="lg"
                                    class="neon-glow font-bold text-lg group bg-cyan-600 hover:bg-cyan-500 border-none">
                                    <Link :href="login()">
                                        Sign In
                                        <ArrowRight
                                            class="ml-2 size-5 group-hover:translate-x-1 transition-transform" />
                                    </Link>
                                </Button>

                                <div v-if="canRegister" class="relative py-4 text-center">
                                    <div class="absolute inset-0 flex items-center">
                                        <span class="w-full border-t border-white/10"></span>
                                    </div>
                                    <span
                                        class="relative bg-slate-950 px-4 text-xs text-slate-600 uppercase tracking-widest font-bold">Or</span>
                                </div>

                                <Button v-if="canRegister" as-child variant="outline" size="lg"
                                    class="glass border-white/10 hover:bg-white/5">
                                    <Link :href="register()">Create an Account</Link>
                                </Button>
                            </template>
                        </div>

                        <p class="text-[10px] text-center text-slate-600 uppercase font-bold">
                            © 2026 UrusProperti. All Rights Reserved.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Decorative Footer Elements -->
        <div
            class="absolute bottom-0 left-0 right-0 h-px bg-linear-to-r from-transparent via-cyan-500/20 to-transparent">
        </div>
    </div>
</template>

<style scoped>
.starting\:opacity-0 {
    opacity: 0;
}
</style>
