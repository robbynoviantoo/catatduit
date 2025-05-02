<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';

const form = ref({
    name: '',
    code: '',
    harga: 0,
    hargajual: 0,
    stock: 0,
    remark: '',
});
const image = ref<File | null>(null);

const handleImageChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        image.value = input.files[0];
    }
};

const submitForm = () => {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('code', form.value.code);
    formData.append('harga', form.value.harga.toString());
    formData.append('hargajual', form.value.hargajual.toString());
    formData.append('stock', form.value.stock.toString());
    formData.append('remark', form.value.remark || '');
    if (image.value) {
        formData.append('image', image.value);
    }

    axios
        .post('/products', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })
        .then(() => {
            alert('Produk berhasil ditambahkan');
            window.location.href = '/products'; // Kembali ke halaman daftar produk
        })
        .catch((err) => console.error(err));
};
const breadcrumbs = [
    { title: 'Product', href: '/products' },
    { title: 'Product Create', href: '/products/create' },
];
</script>

<template>
    <Head title="Add Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h2 class="text-2xl font-bold">Tambah Produk</h2>
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <label class="block text-sm">Nama</label>
                    <input v-model="form.name" type="text" class="w-full rounded border p-2" />
                </div>
                <div>
                    <label class="block text-sm">Kode</label>
                    <input v-model="form.code" type="text" class="w-full rounded border p-2" />
                </div>
                <div>
                    <label class="block text-sm">Harga</label>
                    <input v-model="form.harga" type="number" class="w-full rounded border p-2" />
                </div>
                <div>
                    <label class="block text-sm">Harga Jual</label>
                    <input v-model="form.hargajual" type="number" class="w-full rounded border p-2" />
                </div>
                <div>
                    <label class="block text-sm">Stok</label>
                    <input v-model="form.stock" type="number" class="w-full rounded border p-2" />
                </div>
                <div>
                    <label class="block text-sm">Gambar</label>
                    <input type="file" accept="image/*" class="w-full rounded border p-2" @change="handleImageChange" />
                </div>
                <div>
                    <label class="block text-sm">Remark</label>
                    <textarea v-model="form.remark" class="w-full rounded border p-2"></textarea>
                </div>
                <div class="flex justify-start space-x-2">
                    <Link :href="route('products.index')" class="rounded-lg bg-gray-500 px-4 py-2 text-white hover:hover:bg-[var(--color-hover)]"> Batal </Link>
                    <button type="submit" class="rounded-lg bg-[var(--color-primary)] px-4 py-2 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
