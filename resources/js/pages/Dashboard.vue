<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import SparklineChart from '../components/SparklineChart.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    summary: {
        income: number;
        expense: number;
        balance: number;
        previous_balance: number;
        chart: Record<string, number>;
    };
}>();

const selectedRange = ref('week');
const summary = ref(props.summary);

function fetchSummary() {
    router.get(
        '/dashboard',
        { range: selectedRange.value },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['summary'],
            onSuccess: (page) => {
                summary.value = page.props.summary as typeof props.summary;
            },
        },
    );
}

const percentChange = computed(() => {
    const prev = summary.value.previous_balance;
    if (prev === 0) return 0;
    return ((summary.value.balance - prev) / prev) * 100;
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Card 1 -->
                <div class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h1 class="text-4xl font-semibold text-gray-700">Total Balance</h1>
                            <div class="relative">
                                <select
                                    v-model="selectedRange"
                                    @change="fetchSummary"
                                    class="appearance-none rounded-lg bg-[#f2f3f5] px-4 py-2 pr-10"
                                >
                                    <option value="today">Today</option>
                                    <option value="week">Weekly</option>
                                    <option value="month">Monthly</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-chevron-down"
                                    >
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <p class="pt-6 text-4xl font-bold text-gray-900">Rp {{ summary.balance.toLocaleString() }}</p>
                        <p class="text-gray-500">Last {{ selectedRange }}: Rp {{ summary.previous_balance.toLocaleString() }}</p>
                        <div class="mt-1 flex items-center gap-2">
                            <!-- <SparklineChart :key="selectedRange" :data="Object.values(summary.chart)" /> -->

                            <p
                                class="font-semibold"
                                :class="{
                                    'text-green-600': percentChange >= 0,
                                    'text-red-600': percentChange < 0,
                                }"
                            >
                                {{ percentChange.toFixed(2) }}%
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Placeholder Cards -->
                <div class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
                    <PlaceholderPattern />
                </div>
                <div class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
                    <PlaceholderPattern />
                </div>
            </div>

            <!-- Full Height Placeholder -->
            <div class="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] flex-1 rounded-xl border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
