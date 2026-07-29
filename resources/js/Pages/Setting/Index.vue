<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
});

// Inisialisasi Form Inertia
const form = useForm({
    nama_klinik: props.settings.nama_klinik || '',
    alamat: props.settings.alamat || '',
    telepon: props.settings.telepon || '',
    video: props.settings.video || '',
    logo: null,
});

// State untuk Preview Logo
const logoPreview = ref(props.settings.logo_klinik || null);

// Handle Perubahan File Logo
const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

// Submit Form
const submitForm = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Pengaturan berhasil diperbarui!');
        },
    });
};
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Pengaturan Sistem
                </h1>
                <p class="text-slate-500 mt-1">
                    Kelola identitas klinik.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
            <div class="p-6 md:p-8">
                <form @submit.prevent="submitForm" class="space-y-6">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Logo Klinik</label>
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 shrink-0 rounded-2xl border-2 border-dashed border-slate-300 flex items-center justify-center bg-slate-50 overflow-hidden shadow-sm">
                                <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-contain p-2" />
                                <span v-else class="text-xs text-slate-400 font-medium">No Logo</span>
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    @change="handleFileChange"
                                    accept="image/*"
                                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer focus:outline-none transition-colors"
                                />
                                <p class="text-xs text-slate-400 mt-2">Format yang didukung: PNG, JPG, JPEG, SVG. Maks: 2MB.</p>
                                <span v-if="form.errors.logo" class="text-xs text-red-500 mt-1 block font-medium">{{ form.errors.logo }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Klinik / Faskes</label>
                        <input
                            v-model="form.nama_klinik"
                            type="text"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Contoh: KLINIK SEHAT SELALU"
                        />
                        <span v-if="form.errors.nama_klinik" class="text-xs text-red-500 mt-1 block font-medium">{{ form.errors.nama_klinik }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Klinik</label>
                            <input
                                v-model="form.alamat"
                                type="text"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="Jl. Contoh Alamat No. 123"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Telepon</label>
                            <input
                                v-model="form.telepon"
                                type="text"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="08123456789"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">URL Video (YouTube Embed)</label>
                        <input
                            v-model="form.video"
                            type="text"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Contoh: https://www.youtube.com/embed/xxxxx?autoplay=1&mute=1&loop=1"
                        />
                        <p class="text-xs text-slate-500 mt-2">
                            Gunakan link <strong>Embed</strong> dari YouTube. Pastikan menambahkan <code>?autoplay=1&mute=1&loop=1</code> di akhir link agar video memutar otomatis.
                        </p>
                    </div>

                    <div class="flex items-center justify-end pt-6 mt-4 border-t border-slate-200">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>
