<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    username: "",
    email: "",
    password: "",
    role: "",
});

const submit = () => {
    form.post("/users");
};
</script>

<template>
    <AdminLayout>

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-800">
                Tambah User
            </h1>

            <p class="text-slate-500 mt-1">
                Tambahkan akun admin atau petugas baru.
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">

            <div class="px-6 py-5 border-b">
                <h2 class="font-semibold text-lg">
                    Informasi User
                </h2>
            </div>

            <form
                @submit.prevent="submit"
                class="p-6 space-y-6"
            >

                <!-- Nama -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Nama Lengkap
                    </label>

                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        placeholder="Masukkan nama lengkap"
                    />

                    <p
                        v-if="form.errors.name"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Username -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Username
                    </label>

                    <input
                        v-model="form.username"
                        type="text"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan username"
                    />

                    <p
                        v-if="form.errors.username"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.username }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-slate-700">
                        Email
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan email"
                    />

                    <p
                        v-if="form.errors.email"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Password & Role -->
                <div class="grid md:grid-cols-2 gap-6">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">
                            Password
                        </label>

                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-blue-500"
                            placeholder="********"
                        />

                        <p
                            v-if="form.errors.password"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">
                            Role
                        </label>

                        <select
                            v-model="form.role"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-blue-500"
                        >
                            <option disabled value="">
                                Pilih Role
                            </option>

                            <option value="admin">
                                Admin
                            </option>

                            <option value="petugas">
                                Petugas
                            </option>
                        </select>

                        <p
                            v-if="form.errors.role"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.role }}
                        </p>
                    </div>

                </div>

                <!-- Button -->
                <div class="flex justify-end gap-3 pt-4 border-t">

                    <button
                        type="button"
                        class="px-5 py-3 rounded-xl border border-slate-300 hover:bg-slate-100"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition disabled:opacity-50"
                    >
                        {{ form.processing ? "Menyimpan..." : "Simpan User" }}
                    </button>

                </div>

            </form>

        </div>

    </AdminLayout>
</template>
