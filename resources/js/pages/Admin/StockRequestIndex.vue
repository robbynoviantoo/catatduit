<!-- resources/js/Pages/Admin/StockRequestIndex.vue -->
<template>
    <Head title="Request Stock" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Stock Reduction Request </h1>
                <Link :href="route('user.stockRequests.create')" class="rounded-lg bg-[var(--color-primary)] px-4 py-2 text-white hover:hover:bg-[var(--color-hover)]">
                    + Add Request
                </Link>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-300 dark:border-gray-600">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Produk</th>
                            <th class="px-4 py-2 text-left">Jumlah</th>
                            <th class="px-4 py-2 text-left">Pengguna</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th v-if="$page.props.auth.user.role === 'admin'" class="px-4 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="request in requests" :key="request.id" class="border-t">
                            <td class="px-4 py-2">{{ request.product.name }}</td>
                            <td class="px-4 py-2">{{ request.quantity }}</td>
                            <td class="px-4 py-2">{{ request.user.name }}</td>
                            <td class="px-4 py-2">
                                <span
                                    :class="{
                                        'bg-yellow-200 text-yellow-800': request.status === 'pending',
                                        'bg-green-200 text-green-800': request.status === 'approved',
                                        'bg-red-200 text-red-800': request.status === 'rejected',
                                    }"
                                    class="rounded-full px-2 py-1"
                                >
                                    {{ request.status }}
                                </span>
                            </td>

                            <!-- Tampilkan kolom aksi hanya untuk admin -->
                            <td v-if="$page.props.auth.user.role === 'admin'" class="px-4 py-2">
                                <div class="flex gap-2" v-if="request.status === 'pending'">
                                    <button @click="approveRequest(request)" class="rounded bg-green-600 px-4 py-1 text-white">Setujui</button>
                                    <button @click="rejectRequest(request)" class="rounded bg-red-600 px-4 py-1 text-white">Tolak</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router  } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    requests: Array,
});

const approveRequest = (request) => {
    if (confirm(`Apakah Anda yakin ingin menyetujui permintaan stok untuk ${request.product.name}?`)) {
        // API call to approve request
        Inertia.post(`/stock-requests/${request.id}/approve`);
    }
};

const rejectRequest = (request) => {
    if (confirm(`Apakah Anda yakin ingin menolak permintaan stok untuk ${request.product.name}?`)) {
        // API call to reject request
        Inertia.post(`/stock-requests/${request.id}/reject`);
    }
};
const breadcrumbs = [{ title: 'Request Stock', href: '/stock-requests' }];
</script>
