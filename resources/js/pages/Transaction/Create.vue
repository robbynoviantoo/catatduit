<template>
  <Head title="Create Transactions" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 rounded-xl p-4 max-w-2xl">
          <h1 class="mb-4 text-2xl font-bold">Tambah Transaksi</h1>

          <form @submit.prevent="submit">
              <div class="mb-4">
                  <label class="block mb-1 font-medium">Tanggal</label>
                  <input v-model="form.date" type="date" class="w-full border rounded px-4 py-2" />
              </div>

              <div class="mb-4">
                  <label class="block mb-1 font-medium">Jenis</label>
                  <select v-model="form.type" class="w-full border rounded px-4 py-2">
                      <option value="income">Pemasukan</option>
                      <option value="expense">Pengeluaran</option>
                  </select>
              </div>

              <div class="mb-4">
                  <label class="block mb-1 font-medium">Jumlah</label>
                  <input v-model="form.amount" type="number" class="w-full border rounded px-4 py-2" />
              </div>

              <div class="mb-4">
                  <label class="block mb-1 font-medium">Kategori</label>
                  <input v-model="form.category" type="text" class="w-full border rounded px-4 py-2" />
              </div>

              <div class="mb-4">
                  <label class="block mb-1 font-medium">Catatan</label>
                  <textarea v-model="form.remark" class="w-full border rounded px-4 py-2" />
              </div>

              <button type="submit" class="bg-green-500 px-4 py-2 text-white rounded hover:bg-green-600">
                  Simpan
              </button>
          </form>
      </div>
  </AppLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
import { useToast } from "vue-toastification";

const breadcrumbs = [
{ title: 'Transactions', href: '/transactions' },
{ title: 'Transactions Create', href: '/transactions/create' },
];

const toast = useToast();

const form = useForm({
  date: '',
  type: 'income',
  amount: '',
  category: '',
  remark: '',
});

function submit() {
  form.post('/transactions', {
      onSuccess: () => {
          toast.success('Transaksi berhasil disimpan!');
      },
      onError: () => {
          toast.error('Gagal menyimpan transaksi. Periksa kembali data Anda.');
      }
  });
}
</script>
