<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Sparkles, Loader2, AlertCircle, Bot, Database } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

interface ContextItem {
    id: number;
    name?: string;
    title?: string;
    notes?: string;
    description?: string;
}

const query = ref('');
const type = ref('tenants');
const loading = ref(false);
const result = ref<string | null>(null);
const context = ref<ContextItem[]>([]);
const error = ref<string | null>(null);

const runInsight = async () => {
    if (!query.value) {
        return
    }

    loading.value = true
    result.value = null
    context.value = []
    error.value = null

    try {
        const response = await fetch('/api/ai-insight', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                query: query.value,
                type: type.value
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = (await response.json()) as {
            insight: string;
            context_used: ContextItem[];
        };

        result.value = data.insight;
        context.value = data.context_used;
    } catch (e) {
        console.error(e)
        error.value = 'We could not generate insights right now. Please try again.'
    } finally {
        loading.value = false
    }
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
                    <Button variant="secondary" size="sm" @click="query = 'Which tenants are likely to pay late?'">
                        Late Payment Risk
                    </Button>
                    <Button variant="secondary" size="sm" @click="query = 'Show unusual expenses this month'">
                        Unusual Expenses
                    </Button>
                    <Button variant="secondary" size="sm" @click="query = 'Which properties need attention?'">
                        Property Issues
                    </Button>
                </div>

                <!-- Textarea -->
                <Textarea v-model="query" placeholder="e.g. Which tenants frequently pay late or request extensions?"
                    class="w-full mb-4 bg-background/50 border-white/10" rows="4" />

                <!-- Controls -->
                <div class="flex flex-wrap gap-3 items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Select v-model="type">
                            <SelectTrigger id="type" class="w-[180px]">
                                <SelectValue placeholder="Select Data Source" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="tenants">Tenants</SelectItem>
                                <SelectItem value="expenses">Expenses</SelectItem>
                                <SelectItem value="properties">Properties</SelectItem>
                                <SelectItem value="rules">Rules</SelectItem>
                            </SelectContent>
                        </Select>

                        <span class="text-xs text-muted-foreground flex items-center gap-1">
                            <Database class="h-3 w-3" /> Analyzing {{ type }} data
                        </span>
                    </div>

                    <Button @click="runInsight" :disabled="loading" class="min-w-[150px]">
                        <Loader2 v-if="loading" class="w-4 h-4 mr-2 animate-spin" />
                        <Sparkles v-else class="w-4 h-4 mr-2" />
                        {{ loading ? 'Analyzing...' : 'Generate Insight' }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Loading State -->
        <Card v-if="loading" class="border-primary/20 bg-primary/5 animate-pulse">
            <CardContent class="pt-6 flex flex-col items-center justify-center py-12">
                <Bot class="h-12 w-12 text-primary mb-4 animate-bounce" />
                <p class="font-medium text-lg">Analyzing your data...</p>
                <p class="text-sm text-muted-foreground">Please wait while the AI discovers patterns</p>
            </CardContent>
        </Card>

        <!-- Error State -->
        <Card v-if="error" class="border-destructive/50 bg-destructive/10">
            <CardContent class="pt-6 flex flex-col items-center justify-center py-8">
                <AlertCircle class="h-10 w-10 text-destructive mb-3" />
                <p class="font-medium text-destructive">{{ error }}</p>
            </CardContent>
        </Card>

        <!-- Empty State -->
        <Card v-if="!result && !loading && !error"
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
        <Card v-if="result" class="glass border-primary/30 shadow-[0_0_30px_-10px_var(--color-primary)]">
            <CardHeader class="pb-3 border-b border-white/5">
                <CardTitle class="flex items-center gap-2 text-primary">
                    <Bot class="h-5 w-5" /> AI Insight
                </CardTitle>
            </CardHeader>
            <CardContent class="pt-6">
                <p class="whitespace-pre-line text-sm leading-relaxed">{{ result }}</p>
                <p
                    class="text-xs text-muted-foreground mt-4 pt-4 border-t border-white/5 flex items-center justify-end gap-1">
                    <Sparkles class="h-3 w-3" /> Generated using semantic search + AI reasoning
                </p>
            </CardContent>
        </Card>

        <!-- Context -->
        <Card v-if="context.length" class="bg-muted/10 border-white/5">
            <CardHeader class="pb-3 border-b border-white/5 bg-muted/20">
                <CardTitle class="text-sm">Supporting Data</CardTitle>
                <CardDescription>Records used to generate this insight</CardDescription>
            </CardHeader>
            <CardContent class="pt-0 px-0">

                <div class="divide-y divide-white/5">
                    <div v-for="item in context" :key="item.id" class="p-4 text-sm hover:bg-muted/10 transition-colors">
                        <div v-if="type === 'tenants'">
                            <strong class="text-foreground">{{ item.name }}</strong>
                            <p class="text-muted-foreground mt-1">{{ item.notes }}</p>
                        </div>

                        <div v-else-if="type === 'expenses'">
                            <strong class="text-foreground">{{ item.title }}</strong>
                            <p class="text-muted-foreground mt-1">{{ item.notes }}</p>
                        </div>

                        <div v-else-if="type === 'properties'">
                            <strong class="text-foreground">{{ item.name }}</strong>
                            <p class="text-muted-foreground mt-1">{{ item.notes }}</p>
                        </div>

                        <div v-else-if="type === 'rules'">
                            <strong class="text-foreground">{{ item.title }}</strong>
                            <p class="text-muted-foreground mt-1">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>


    </div>
</template>