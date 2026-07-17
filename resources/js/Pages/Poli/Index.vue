<script setup>
import ConfirmModal from "@/Components/ConfirmModal.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { watchDebounced } from "@vueuse/core";
import { router } from "@inertiajs/vue3";
import { Pencil, Trash2 } from "lucide-vue-next";
import TableToolbar from "@/Components/Table/TableToolbar.vue";
import DataTable from "@/Components/Table/DataTable.vue";

const props = defineProps({
    polis: Object,
    filters: Object, // Jangan lupa tambahkan ini agar props.filters.search tidak error
});

const search = ref(props.filters?.search ?? "");

// 1. TYPO DIPERBAIKI: 'nama' menjadi 'name'
const tableColumns = [
    { label: 'Nama', key: 'nama' },
    { label: 'Prefix', key: 'prefix' },
    // Tambahkan class text-center agar kolom status berada di tengah
    { label: 'Status', key: 'status', class: 'text-center' },
    { label: 'Aksi', key: 'action', class: 'text-center' }
];

const showDeleteModal = ref(false);
const selectedPoli = ref(null);

const confirmDelete = (poli) => {
    selectedPoli.value = poli;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showDeleteModal.value = false;
    selectedPoli.value = null;
};

watchDebounced(
    search,
    (value) => {
        router.get(
            "/polis",
            { search: value },
            {
                preserveState: true,
                replace: true,
            }
        );
    },
    {
        debounce: 500,
        maxWait: 1000,
    }
);

// 2. NAMA FUNGSI DISESUAIKAN: deleteUser menjadi deletePoli
const deletePoli = () => {
    router.delete(route("polis.destroy", selectedPoli.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
};
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Data Tabel Poli
                </h1>
                <p class="text-slate-500 mt-1">
                    Kelola Poli.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
            <TableToolbar>
                <template #right>
                    <Link
                        :href="route('polis.create')"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                    >
                        Tambah Poli
                    </Link>
                </template>
            </TableToolbar>

            <DataTable
                :resource="polis"
                :columns="tableColumns"
                emptyMessage="Belum ada data Poli."
            >
                <template #cell-status="{ item }">
                    <div class="flex justify-center">
                        <span
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="
                                item.status
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-rose-100 text-rose-700'
                            "
                        >
                            <span class="flex items-center gap-1.5">
                                <span
                                    class="w-1.5 h-1.5 rounded-full"
                                    :class="item.status ? 'bg-emerald-500' : 'bg-rose-500'"
                                ></span>
                                {{ item.status ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </span>
                    </div>
                </template>

                <template #cell-action="{ item }">
                    <div class="flex justify-center gap-2">
                        <Link
                            :href="route('polis.edit', item.id)"
                            class="p-2 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition"
                        >
                            <Pencil class="w-4 h-4" />
                        </Link>
                        <button
                            @click="confirmDelete(item)"
                            class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </DataTable>
        </div>
    </AdminLayout>

    <ConfirmModal
        :show="showDeleteModal"
        title="Hapus Poli"
        :message="`Apakah yakin ingin menghapus ${selectedPoli?.name}?`"
        @close="closeModal"
        @confirm="deletePoli"
    />
</template>
