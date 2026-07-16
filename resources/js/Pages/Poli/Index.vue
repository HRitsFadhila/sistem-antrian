<script setup>
import ConfirmModal from "@/Components/ConfirmModal.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { watchDebounced } from "@vueuse/core";
import { router } from "@inertiajs/vue3";
import { Pencil, Trash2 } from "lucide-vue-next";
import TableToolbar from "@/Components/Table/TableToolbar.vue";

// TAMBAHKAN IMPORT INI
import DataTable from "@/Components/Table/DataTable.vue";

const props = defineProps({
    polis: Object
});

const search = ref(props.filters?.search ?? "");

const tableColumns = [
    { label: 'Nama', key: 'name' },
    { label: 'Prefix', key: 'prefix' },
    { label: 'Status', key: 'status' },
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

const deleteUser = () => {
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
                <!-- <template #left>
                    <SearchInput
                        v-model="search"
                        placeholder="Cari user..."
                    />
                </template> -->

                <template #right>
                    <Link
                        :href="route('polis.create')"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg"
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
                <template #cell-action="{ item }">
                    <div class="flex justify-center gap-2">
                        <Link
                            :href="route('users.edit', item.id)"
                            class="p-2 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200"
                        >
                            <Pencil class="w-4 h-4" />
                        </Link>
                        <button
                            @click="confirmDelete(item)"
                            class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200"
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
        @confirm="deleteUser"
    />
</template>
