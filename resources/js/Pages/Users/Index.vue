<script setup>
import ConfirmModal from "@/Components/ConfirmModal.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { watchDebounced } from "@vueuse/core";
import { router } from "@inertiajs/vue3";
import {
    Plus,
    Search,
    Pencil,
    Trash2,
} from "lucide-vue-next";

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? "");

const showDeleteModal = ref(false);
const selectedUser = ref(null);

const confirmDelete = (user) => {
    selectedUser.value = user;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showDeleteModal.value = false;
    selectedUser.value = null;
};

watchDebounced(
    search,
    (value) => {
        router.get(
            "/users",
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
    router.delete(route("users.destroy", selectedUser.value.id), {
        preserveScroll: true,

        onSuccess: () => {
            closeModal();
        },
    });
};

</script>

<template>
    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Data User
                </h1>

                <p class="text-slate-500 mt-1">
                    Kelola akun administrator dan petugas.
                </p>
            </div>

            <Link
                href="/users/create"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl transition"
            >
                <Plus class="w-5 h-5" />
                Tambah User
            </Link>

        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
            <!-- Toolbar -->
            <div
                class="flex items-center justify-between p-5 border-b"
            >
                <div class="relative w-80">
                    <Search
                        class="absolute left-3 top-3 w-5 h-5 text-slate-400"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari user..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr class="text-left text-slate-600">

                            <th class="px-6 py-4">No</th>

                            <th class="px-6 py-4">
                                Nama
                            </th>

                            <th class="px-6 py-4">
                                Username
                            </th>

                            <th class="px-6 py-4">
                                Email
                            </th>

                            <th class="px-6 py-4">
                                Role
                            </th>

                            <th class="px-6 py-4 text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr
                            v-for="(user,index) in users.data"
                            :key="user.id"
                            class="border-t hover:bg-slate-50 transition"
                        >

                            <td class="px-6 py-4">
                                {{ index + 1 }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold"
                                    >
                                        {{ user.name.charAt(0) }}
                                    </div>

                                    <div>

                                        <p class="font-semibold text-slate-800">
                                            {{ user.name }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            <td class="px-6 py-4">
                                {{ user.username }}
                            </td>

                            <td class="px-6 py-4">
                                {{ user.email }}
                            </td>

                            <td class="px-6 py-4">

                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="
                                        user.roles[0]?.name === 'admin'
                                            ? 'bg-blue-100 text-blue-700'
                                            : 'bg-green-100 text-green-700'
                                    "
                                >
                                    {{ user.roles[0]?.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('users.edit', user.id)"
                                        class="p-2 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200"
                                    >
                                        <Pencil class="w-4 h-4" />
                                    </Link>
                                    <button
                                        @click="confirmDelete(user)"
                                        class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Empty -->
                        <tr
                            v-if="users.data.length === 0"
                        >
                            <td
                                colspan="6"
                                class="text-center py-12 text-slate-500"
                            >
                                Belum ada data user.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex justify-between items-center mt-6 p-2">
                    <div class="text-sm text-gray-500">
                        Menampilkan
                        {{ users.from }}
                        -
                        {{ users.to }}
                        dari
                        {{ users.total }}
                        data
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-4 py-2 rounded-lg border transition"
                            :class="[
                                link.active
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white hover:bg-gray-100',
                                !link.url
                                    ? 'opacity-50 cursor-not-allowed'
                                    : ''
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
    <ConfirmModal
        :show="showDeleteModal"
        title="Hapus User"
        :message="`Apakah yakin ingin menghapus ${selectedUser?.name}?`"
        @close="closeModal"
        @confirm="deleteUser"
    />
</template>
