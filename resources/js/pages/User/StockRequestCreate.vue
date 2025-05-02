<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    products: Array,
});

const breadcrumbs = [
    { title: 'Stock', href: '/stock-requests' },
    { title: 'Stock Request', href: '/stock-requests/create' },
];

const form = useForm({
    product_id: '',
    quantity: '',
    note: '',
});

const submitRequest = () => {
    form.post('/stock-requests', {
        onSuccess: () => {
            router.visit('/stock-requests'); // redirect ke halaman list stock request
        },
    });
};
</script>

<template>
    <Head title="Create Request" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex max-w-2xl flex-col gap-4 rounded-xl p-4">
            <h1 class="mb-4 text-2xl font-semibold">Ajukan Pengurangan Stok</h1>

            <form @submit.prevent="submitRequest">
                <div class="mb-4">
                    <label for="product_id" class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                    <select v-model="form.product_id" id="product_id" class="mt-1 h-8 w-full rounded-md border px-2">
                        <option v-for="product in products" :key="product.id" :value="product.id">
                            {{ product.name }} ({{ product.stock }} stok tersisa)
                        </option>
                    </select>
                    <span v-if="form.errors.product_id" class="text-sm text-red-500">{{ form.errors.product_id }}</span>
                </div>

                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Pengurangan</label>
                    <input v-model="form.quantity" type="number" min="1" id="quantity" class="mt-1 h-8 w-full rounded-md border px-2" required />
                    <span v-if="form.errors.quantity" class="text-sm text-red-500">{{ form.errors.quantity }}</span>
                </div>

                <div class="mb-4">
                    <label for="note" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                    <textarea v-model="form.note" id="note" class="mt-1 w-full rounded-md border px-2"></textarea>
                </div>

                <div>
                    <button type="submit" class="rounded bg-[var(--color-primary)] px-4 py-2 text-white" :disabled="form.processing">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
