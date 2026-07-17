<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    polis: Object,
});

const form = useForm({
    nama: props.polis.nama,
    prefix: props.polis.prefix,
    status: props.polis.status,
});

const submit = () => {
    form.put(route("polis.update", props.polis.id));
};
</script>

<template>
    <AdminLayout>

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-800">
                Tambah Poli
            </h1>

            <p class="text-slate-500 mt-1">
                Tambahkan poli kesehatan.
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">

            <div class="px-6 py-5 border-b">
                <h2 class="font-semibold text-lg">
                    Informasi Poli
                </h2>
            </div>

            <form
                @submit.prevent="submit"
                class="p-6 space-y-6"
            >

                <!-- Nama -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Nama
                    </label>

                    <input
                        v-model="form.nama"
                        type="text"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        placeholder="Masukkan nama Poli"
                    />

                    <p
                        v-if="form.errors.nama"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.nama }}
                    </p>
                </div>

                <!-- Username -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Prefix
                    </label>

                    <input
                        v-model="form.prefix"
                        type="text"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan kode prefix"
                    />

                    <p
                        v-if="form.errors.prefix"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.prefix }}
                    </p>
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Status Poli
                        </label>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Tentukan apakah poli ini langsung aktif atau nonaktif setelah dibuat.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="form.status = !form.status"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        :class="form.status ? 'bg-blue-600' : 'bg-slate-200'"
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="form.status ? 'translate-x-5' : 'translate-x-0'"
                        />
                    </button>
                </div>

                <!-- Button -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <Link
                        :href="route('polis.index')"
                        class="px-5 py-3 rounded-xl border border-slate-300 hover:bg-slate-100 flex items-center"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition disabled:opacity-50"
                    >
                        {{ form.processing ? "Update..." : "Update Poli" }}
                    </button>
                </div>
            </form>
        </div>

    </AdminLayout>
</template>
