<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';

const breadcrumbs = [{ title: 'Produk', href: '/products' }];

interface Product {
    id: number;
    name: string;
    code: string;
    harga: number;
    hargajual: number;
    stock: number;
    remark: string;
    image?: string;
}

interface ProductForm {
    name: string;
    code: string;
    harga: number;
    hargajual: number;
    stock: number;
    remark?: string;
}

const showModal = ref(false);
const form = ref<ProductForm>({
    name: '',
    code: '',
    harga: 0,
    hargajual: 0,
    stock: 0,
    remark: '',
});

const products = ref<Product[]>([]);
const total = ref(0);
const page = ref(1);
const perPage = ref(10);
const search = ref('');
const loading = ref(false);

// Fetch Products with Search
const fetchProducts = async () => {
    loading.value = true;
    try {
        const { data }: { data: { data: Product[]; total: number } } = await axios.get('/api/products', {
            params: {
                page: page.value,
                per_page: perPage.value,
                search: search.value,
            },
        });
        products.value = data.data;
        total.value = data.total;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

// Watchers for page, perPage, and search changes
watch([page, perPage, search], fetchProducts);
onMounted(fetchProducts);

// Handle delete product
const deleteProduct = (id: number) => {
    if (!confirm('Yakin ingin menghapus produk ini?')) return;
    axios
        .delete(`/products/${id}`)
        .then(() => location.reload())
        .catch((err) => console.error(err));
};

// Computed for filtered products based on search input
const filteredProducts = computed(() => {
    return products.value.filter((product) => {
        const lowerSearch = search.value.toLowerCase();
        return product.name.toLowerCase().includes(lowerSearch) || product.code.toLowerCase().includes(lowerSearch);
    });
});
</script>

<template>
    <Head title="List Produk" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Products</h1>
                <Link :href="route('products.create')" class="rounded-lg bg-[var(--color-primary)] px-4 py-2 text-white hover:hover:bg-[var(--color-hover)]">
                    + Add Products
                </Link>
            </div>

            <!-- Search Box -->
            <div class="mb-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari produk..."
                    class="w-full rounded border border-gray-300 px-4 py-2 text-sm focus:ring focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                />
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-300 dark:border-gray-600">
                <table class="w-full text-sm">
                    <!-- Thead tanpa animasi -->
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Gambar</th>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">Kode</th>
                            <th class="px-4 py-2 text-left">Harga</th>
                            <th class="px-4 py-2 text-left">Harga Jual</th>
                            <th class="px-4 py-2 text-left">Stok</th>
                            <th class="px-4 py-2 text-left">Remark</th>
                            <th class="px-4 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- Skeleton loading hanya saat pertama kali atau saat loading -->
                        <template v-if="loading && !filteredProducts.length">
                            <tr v-for="n in 8" :key="'skeleton-' + n" class="animate-pulse border-t">
                                <td class="px-4 py-2">
                                    <Skeleton class="h-12 w-12" />
                                </td>
                                <td class="px-4 py-2">
                                    <Skeleton class="h-4 w-24" />
                                </td>
                                <td class="px-4 py-2">
                                    <Skeleton class="h-4 w-16" />
                                </td>
                                <td class="px-4 py-2">
                                    <Skeleton class="h-4 w-20" />
                                </td>
                                <td class="px-4 py-2">
                                    <Skeleton class="h-4 w-12" />
                                </td>
                                <td class="px-4 py-2">
                                    <Skeleton class="h-4 w-12" />
                                </td>
                                <td class="px-4 py-2">
                                    <Skeleton class="h-4 w-12" />
                                </td>
                                <td class="px-4 py-2">
                                    <Skeleton class="h-4 w-12" />
                                </td>
                            </tr>
                        </template>

                        <!-- Produk nyata -->
                        <template v-else>
                            <tr v-for="product in filteredProducts" :key="product.id" class="border-t">
                                <td class="px-4 py-2">
                                    <img
                                        v-if="product.image"
                                        :src="`/storage/${product.image}`"
                                        alt="Gambar Produk"
                                        class="h-12 w-12 rounded object-cover"
                                    />
                                </td>
                                <td class="px-4 py-2">{{ product.name }}</td>
                                <td class="px-4 py-2">{{ product.code }}</td>
                                <td class="px-4 py-2">Rp {{ product.harga.toLocaleString() }}</td>
                                <td class="px-4 py-2">Rp {{ product.hargajual.toLocaleString() }}</td>
                                <td class="px-4 py-2">{{ product.stock }}</td>
                                <td class="px-4 py-2">{{ product.remark }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="rounded bg-yellow-500 px-3 py-1 text-sm text-white">Edit</button>
                                        <button @click="deleteProduct(product.id)" class="rounded bg-red-600 px-3 py-1 text-sm text-white">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="my-2 flex items-center justify-between px-2">
                    <div>Menampilkan {{ (page - 1) * perPage + 1 }} - {{ Math.min(page * perPage, total) }} dari {{ total }} produk</div>
                    <div class="space-x-2">
                        <button
                            @click="page = Math.max(1, page - 1)"
                            :disabled="page === 1"
                            class="rounded bg-gray-300 px-3 py-1 text-sm disabled:opacity-50 dark:bg-gray-700 dark:text-white"
                        >
                            Sebelumnya
                        </button>
                        <button
                            @click="page = page + 1"
                            :disabled="page * perPage >= total"
                            class="rounded bg-gray-300 px-3 py-1 text-sm disabled:opacity-50 dark:bg-gray-700 dark:text-white"
                        >
                            Berikutnya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
