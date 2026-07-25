<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import axios from 'axios'; // Jika butuh request langsung via axios

// 1. Terima data dari Laravel Controller via Props Inertia
const props = defineProps({
    daftarPoli: {
        type: Array,
        default: () => []
    }
});

// 2. Buat reaktif copy dari props agar bisa ter-update otomatis dari WebSocket
const daftarPoliLokal = ref([...props.daftarPoli]);

const loading = ref(false);

// Logika Fungsi Panggil
const panggilAntrian = async (poliId, namaPoli) => {
    loading.value = true;
    try {
        const response = await axios.post('/panggil-antrian', { poli_id: poliId });

        const index = daftarPoliLokal.value.findIndex(p => p.id === poliId);

        if (index !== -1) {
            // PERBAIKAN DI SINI: Akses ke response.data.data.no_antrian
            daftarPoliLokal.value[index].nomorTerkini = response.data.data.no_antrian;
        }

    } catch (error) {
        alert(error.response?.data?.message || 'Gagal memanggil antrian.');
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const antrianBerikutnya = async (poliId, namaPoli) => {
    loading.value = true;
    try {
        const response = await axios.post('/antrian-berikutnya', { poli_id: poliId });

        const index = daftarPoliLokal.value.findIndex(p => p.id === poliId);

        if (index !== -1) {
            // PERBAIKAN DI SINI: Akses ke response.data.data.no_antrian
            daftarPoliLokal.value[index].nomorTerkini = response.data.no_antrian;
        }

    } catch (error) {
        alert(error.response?.data?.message || 'Gagal memproses antrian berikutnya.');
        console.error(error);
    } finally {
        loading.value = false;
    }
};

// Logika Fungsi Lewati
const lewatiAntrian = async (poliId, namaPoli) => {
    loading.value = true;
    try {
        // Uncomment baris di bawah ini dan sesuaikan endpoint-nya nanti
        await axios.post('/api/lewati-antrian', { poli_id: poliId });

        alert(`Melewati antrian saat ini di ${namaPoli}.`);
    } catch (error) {
        alert('Gagal melewati antrian.');
        console.error(error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    window.Echo.channel('antrian-channel')
        .listen('.panggilan.baru', (e) => {
            const index = daftarPoliLokal.value.findIndex(p => p.id === e.poli_id);
            if (index !== -1) {
                daftarPoliLokal.value[index].nomorTerkini = e.nomorAntrian;
            }
        });
});

onUnmounted(() => {
    window.Echo.leave('antrian-channel');
});
</script>

<template>
    <AdminLayout>

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Dashboard Pelayanan</h2>
            <p class="text-gray-500 mt-1">Manajemen pemanggilan antrian pasien per loket.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <div
                v-for="poli in daftarPoliLokal"
                :key="poli.id"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col transition-all hover:shadow-md"
            >
                <div class="bg-indigo-600 px-4 py-3 text-center">
                    <h3 class="font-bold text-white tracking-wide uppercase">{{ poli.nama }}</h3>
                </div>

                <div class="p-8 flex flex-col items-center justify-center flex-1 bg-gray-50/50">
                    <p class="text-sm text-gray-400 font-semibold mb-2 uppercase tracking-wider">Antrian Saat Ini</p>
                    <span class="text-5xl font-extrabold text-gray-800 font-mono drop-shadow-sm">
                        {{ poli.nomorTerkini }}
                    </span>
                </div>

                <div class="p-4 bg-white border-t border-gray-100 grid grid-cols-3 gap-3">

                    <button
                        @click="lewatiAntrian(poli.id, poli.nama)"
                        :disabled="loading"
                        class="flex items-center justify-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 font-bold py-2.5 px-4 rounded-xl transition-colors disabled:opacity-50 border border-red-100"
                    >
                        Lewati
                    </button>

                    <button
                        @click="panggilAntrian(poli.id, poli.nama)"
                        :disabled="loading"
                        class="flex items-center justify-center gap-1 bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold py-2 px-2 rounded-xl text-sm transition-colors disabled:opacity-50 border border-amber-100"
                    >
                        Panggil
                    </button>

                    <button
                        @click="antrianBerikutnya(poli.id, poli.nama)"
                        :disabled="loading"
                        class="flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-4 rounded-xl transition-all shadow-sm hover:shadow-emerald-500/30 active:scale-95 disabled:opacity-50"
                    >
                        Berikutnya
                    </button>

                </div>
            </div>

        </div>
    </AdminLayout>
</template>
