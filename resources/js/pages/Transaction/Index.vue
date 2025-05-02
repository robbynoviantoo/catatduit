<template>
    <Head title="List Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Transactions</h1>
                <Link :href="route('transactions.create')" class="rounded-lg bg-[var(--color-primary)] px-4 py-2 text-white hover:hover:bg-[var(--color-hover)]">
                    + Add Transactions
                </Link>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-300 dark:border-gray-600">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-left">Jenis</th>
                            <th class="px-4 py-2 text-left">Kategori</th>
                            <th class="px-4 py-2 text-left">Jumlah</th>
                            <th class="px-4 py-2 text-left">Catatan</th>
                            <th class="px-4 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="trx in transactions" :key="trx.id">
                            <td class="px-4 py-2">{{ trx.date }}</td>
                            <td class="px-4 py-2 capitalize">{{ trx.type }}</td>
                            <td class="px-4 py-2">{{ trx.category }}</td>
                            <td class="px-4 py-2">Rp {{ trx.amount.toLocaleString() }}</td>
                            <td class="px-4 py-2">{{ trx.remark }}</td>
                            <td class="px-4 py-2">
                                <Link :href="`/transactions/${trx.id}/edit`" class="mr-2 text-blue-500">Edit</Link>
                                <button @click="deleteTransaction(trx.id)" class="text-red-500">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
import Swal from 'sweetalert2';

const breadcrumbs = [{ title: 'Transactions', href: '/transactions' }];

defineProps(['transactions']);

function deleteTransaction(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Tindakan ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#553df2',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/transactions/${id}`, {
                onSuccess: () => {
                    Swal.fire('Terhapus!', 'Transaksi berhasil dihapus.', 'success');
                },
                onError: () => {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                }
            });
        }
    });
}
</script>
