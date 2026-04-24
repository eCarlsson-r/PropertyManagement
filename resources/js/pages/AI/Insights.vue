<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Sparkles, Loader2, AlertCircle, Bot, Database } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{
    insight?: string;
    steps_taken?: any[]; // This contains the conversation and tool calls
    query?: string;
}>();
console.info(props);

const form = useForm({
    query: props.query ?? ''
});

const runInsight = () => {
    if (!form.query) {
        return
    }

    form.post('/ai-insight', {
        preserveState: true,
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="AI Insights" />

    <div class="flex flex-col gap-8 p-6 md:p-8 max-w-7xl mx-auto w-full">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold tracking-tight flex items-center gap-2">
                    <Sparkles class="h-8 w-8 text-primary" /> AI Insights
                </h1>
                <p class="text-muted-foreground">
                    Ask questions about your tenants, expenses, or properties to get AI-powered insights.
                </p>
            </div>
        </div>

        <!-- Input Section -->
        <Card class="glass border-white/5">
            <CardContent class="pt-6">
                <!-- Suggested Queries -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <Button variant="secondary" size="sm" @click="form.query = 'Which tenants are likely to pay late?'">
                        Late Payment Risk
                    </Button>
                    <Button variant="secondary" size="sm" @click="form.query = 'Show unusual expenses this month'">
                        Unusual Expenses
                    </Button>
                    <Button variant="secondary" size="sm" @click="form.query = 'Which properties need attention?'">
                        Property Issues
                    </Button>
                </div>

                <!-- Textarea -->
                <Textarea v-model="form.query" placeholder="e.g. Which tenants frequently pay late or request extensions?"
                    class="w-full mb-4 bg-background/50 border-white/10" rows="4" />

                <!-- Controls -->
                <Button @click="runInsight" :disabled="form.processing" class="min-w-[150px]">
                    <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                    <Sparkles v-else class="w-4 h-4 mr-2" />
                    {{ form.processing ? 'Analyzing...' : 'Generate Insight' }}
                </Button>
            </CardContent>
        </Card>

        <!-- Loading State -->
        <Card v-if="form.processing" class="border-primary/20 bg-primary/5 animate-pulse">
            <CardContent class="pt-6 flex flex-col items-center justify-center py-12">
                <Bot class="h-12 w-12 text-primary mb-4 animate-bounce" />
                <p class="font-medium text-lg">Analyzing your data...</p>
                <p class="text-sm text-muted-foreground">Please wait while the AI discovers patterns</p>
            </CardContent>
        </Card>

        <!-- Error State -->
        <Card v-if="form.errors.query" class="border-destructive/50 bg-destructive/10">
            <CardContent class="pt-6 flex flex-col items-center justify-center py-8">
                <AlertCircle class="h-10 w-10 text-destructive mb-3" />
                <p class="font-medium text-destructive">{{ form.errors.query }}</p>
            </CardContent>
        </Card>

        <!-- Empty State -->
        <Card v-if="!insight && !form.processing"
            class="border-dashed border-2 bg-neutral-50/50 dark:bg-neutral-900/50">
            <CardContent class="pt-6 py-12 flex flex-col items-center justify-center text-center opacity-60">
                <Sparkles class="h-12 w-12 mb-4 text-muted-foreground" />
                <h3 class="text-lg font-medium">No insights generated yet</h3>
                <p class="text-sm text-muted-foreground max-w-sm mt-1">
                    Try asking questions like "Which tenants are risky?" or "Are there unusual expenses?"
                </p>
            </CardContent>
        </Card>

        <!-- Result -->
        <Card v-if="insight" class="glass border-primary/30 shadow-[0_0_30px_-10px_var(--color-primary)]">
            <CardHeader class="pb-3 border-b border-white/5">
                <CardTitle class="flex items-center gap-2 text-primary">
                    <Bot class="h-5 w-5" /> AI Insight
                </CardTitle>
            </CardHeader>

            <CardContent class="pt-6">
                <div v-if="steps_taken && steps_taken.length > 1" class="mb-6 space-y-3">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground/70 flex items-center gap-2">
                        <Database class="h-3 w-3" /> Agent Execution Path
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <template v-for="(step, index) in steps_taken" :key="index">
                            <div v-if="step.parts && step.parts[0]?.functionCall" 
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-primary/5 border border-primary/20 text-[11px] font-medium text-primary shadow-sm">
                                <div class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse" />
                                Sub-Agent: {{ step.parts[0].functionCall.name.replace('search_', '').toUpperCase() }}
                            </div>
                        </template>
                    </div>
                    <div class="h-px w-full bg-linear-to-r from-transparent via-white/10 to-transparent my-4" />
                </div>

                <p class="whitespace-pre-line text-sm leading-relaxed text-foreground/90 font-medium">
                    {{ insight }}
                </p>

                <p class="text-xs text-muted-foreground mt-4 pt-4 border-t border-white/5 flex items-center justify-end gap-1 italic">
                    <Sparkles class="h-3 w-3 text-primary" /> Orchestrated by Gemini Multi-Agent System
                </p>
            </CardContent>
        </Card>
    </div>
</template>