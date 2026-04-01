<script setup lang="ts">
import { ref } from 'vue'

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
    <div class="p-6 max-w-4xl mx-auto">

        <!-- Header -->
        <h1 class="text-2xl font-bold mb-1">AI Insights</h1>
        <p class="text-sm text-gray-500 mb-4">
            Ask questions about your tenants, expenses, or properties to get AI-powered insights.
        </p>

        <!-- Input Section -->
        <div class="bg-white shadow rounded-xl p-4 mb-6">

            <!-- Suggested Queries -->
            <div class="flex flex-wrap gap-2 mb-3">
                <button @click="query = 'Which tenants are likely to pay late?'"
                    class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200">
                    Late Payment Risk
                </button>
                <button @click="query = 'Show unusual expenses this month'"
                    class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200">
                    Unusual Expenses
                </button>
                <button @click="query = 'Which properties need attention?'"
                    class="text-xs bg-gray-100 px-2 py-1 rounded hover:bg-gray-200">
                    Property Issues
                </button>
            </div>

            <!-- Textarea -->
            <textarea v-model="query" placeholder="e.g. Which tenants frequently pay late or request extensions?"
                class="w-full border rounded p-3 mb-3" rows="3" />

            <!-- Controls -->
            <div class="flex flex-wrap gap-3 items-center">
                <select v-model="type" class="border rounded p-2">
                    <option value="tenants">Tenants</option>
                    <option value="expenses">Expenses</option>
                    <option value="properties">Properties</option>
                    <option value="rules">Rules</option>
                </select>

                <button @click="runInsight" class="bg-blue-600 text-white px-4 py-2 rounded flex items-center gap-2"
                    :disabled="loading">
                    <span v-if="loading" class="animate-pulse">Analyzing...</span>
                    <span v-else>Generate Insight</span>
                </button>

                <!-- Helper text -->
                <span class="text-xs text-gray-400">
                    Uses AI to analyze your data
                </span>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <p class="text-sm font-medium mb-2">Analyzing your data...</p>
            <ul class="text-xs text-gray-600 space-y-1">
                <li>• Understanding your question</li>
                <li>• Searching relevant records</li>
                <li>• Generating insights</li>
            </ul>
        </div>

        <!-- Error State -->
        <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <p class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Empty State -->
        <div v-if="!result && !loading && !error" class="text-gray-400 text-sm mb-6">
            Try asking questions like:
            <ul class="list-disc ml-5 mt-1">
                <li>Which tenants are risky?</li>
                <li>Are there unusual expenses?</li>
                <li>What issues should I address?</li>
            </ul>
        </div>

        <!-- Result -->
        <div v-if="result" class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <h2 class="font-semibold mb-2">AI Insight</h2>
            <p class="whitespace-pre-line text-sm">{{ result }}</p>
            <p class="text-xs text-gray-400 mt-2">
                Generated using semantic search + AI reasoning
            </p>
        </div>

        <!-- Context -->
        <div v-if="context.length" class="bg-gray-50 border rounded-xl p-4">
            <h2 class="font-semibold mb-2">Supporting Data</h2>
            <p class="text-xs text-gray-500 mb-3">
                These records were used to generate the insight
            </p>

            <div v-for="item in context" :key="item.id" class="border-b py-2 text-sm">
                <div v-if="type === 'tenants'">
                    <strong>{{ item.name }}</strong>
                    <p class="text-gray-600">{{ item.notes }}</p>
                </div>

                <div v-else-if="type === 'expenses'">
                    <strong>{{ item.title }}</strong>
                    <p class="text-gray-600">{{ item.notes }}</p>
                </div>

                <div v-else-if="type === 'properties'">
                    <strong>{{ item.name }}</strong>
                    <p class="text-gray-600">{{ item.notes }}</p>
                </div>

                <div v-else-if="type === 'rules'">
                    <strong>{{ item.title }}</strong>
                    <p class="text-gray-600">{{ item.description }}</p>
                </div>
            </div>
        </div>

    </div>
</template>