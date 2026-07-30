<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import axios from 'axios';
import { router } from '@inertiajs/vue3';

let pollingInterval = null;

// 1. Terima data dari Laravel Controller via Props Inertia
const props = defineProps({
    daftarPoli: {
        type: Array,
        default: () => []
    }
});

watch(() => props.daftarPoli, (newData) => {
    daftarPoliLokal.value = newData;
}, { deep: true });

// 2. Buat reaktif copy dari props agar bisa ter-update otomatis dari WebSocket
const daftarPoliLokal = ref(props.daftarPoli.map(p => ({
    ...p,
    daftarDilewati: p.daftarDilewati || []
})));

const loading = ref(false);

// Logika Fungsi Panggil (Re-call)
const panggilAntrian = async (poliId, namaPoli) => {
    loading.value = true;
    try {
        await axios.post('/panggil-antrian', { poli_id: poliId });
        console.log(`Memanggil ulang antrian di ${namaPoli}`);
    } catch (error) {
        alert(error.response?.data?.message || 'Gagal memanggil antrian.');
        console.error(error);
    } finally {
        loading.value = false;
    }
};

// Logika Fungsi Berikutnya (Next)
const antrianBerikutnya = async (poliId, namaPoli) => {
    loading.value = true;
    try {
        const response = await axios.post('/antrian-berikutnya', { poli_id: poliId });
        const index = daftarPoliLokal.value.findIndex(p => p.id === poliId);

        if (index !== -1) {
            daftarPoliLokal.value[index].nomorTerkini = response.data.nomor_antrian;
        }
        router.reload({ only: ['daftarPoli'] });
    } catch (error) {
        alert(error.response?.data?.message || 'Gagal memproses antrian berikutnya.');
        console.error(error);
    } finally {
        loading.value = false;
    }
};

// Logika Fungsi Lewati (Memindahkan nomor aktif ke daftar dilewati)
const lewatiAntrian = async (poliId, namaPoli) => {
    loading.value = true;
    try {
        const response = await axios.post('/lewati-antrian', { poli_id: poliId });
        const index = daftarPoliLokal.value.findIndex(p => p.id === poliId);

        if (index !== -1) {
            const nomorDilewati = response.data.nomor_antrian;

            // Masukkan nomor ke daftarDilewati di Vue agar langsung TAMPIL
            if (nomorDilewati && !daftarPoliLokal.value[index].daftarDilewati.includes(nomorDilewati)) {
                daftarPoliLokal.value[index].daftarDilewati.push(nomorDilewati);
            }

            // Kosongkan layar utama
            daftarPoliLokal.value[index].nomorTerkini = '-';
        }
        router.reload({ only: ['daftarPoli'] });
    } catch (error) {
        alert(error.response?.data?.message || 'Gagal melewati antrian.');
    } finally {
        loading.value = false;
    }
};

// Logika Fungsi Panggil Kembali (Memanggil dari daftar dilewati)
const panggilDilewati = async (poliId, nomorAntrian) => {
    loading.value = true;
    try {
        const response = await axios.post('/panggil-dilewati', {
            poli_id: poliId,
            nomor_antrian: nomorAntrian
        });

        const index = daftarPoliLokal.value.findIndex(p => p.id === poliId);
        if (index !== -1) {
            // Tampilkan kembali nomor tersebut di layar utama
            daftarPoliLokal.value[index].nomorTerkini = response.data.nomor_antrian;

            // Hapus nomor tersebut dari daftar dilewati
            daftarPoliLokal.value[index].daftarDilewati = daftarPoliLokal.value[index].daftarDilewati.filter(
                n => n !== nomorAntrian
            );
        }
        router.reload({ only: ['daftarPoli'] });
    } catch (error) {
        alert(error.response?.data?.message || 'Gagal memanggil antrian terlewat.');
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
    pollingInterval = setInterval(() => {
        router.reload({
            only: ['daftarPoli'], // Hanya mengambil array daftarPoli
            preserveState: true,  // Menjaga agar state/loading tidak reset
            preserveScroll: true  // Menjaga agar halaman tidak tergulir ke atas
        });
    }, 5000);
});

onUnmounted(() => {
    window.Echo.leave('antrian-channel');

    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
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

                <div class="px-5 py-3 bg-white border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Sisa Antrean</span>
                    <span class="bg-orange-100 text-orange-600 py-1 px-3 rounded-lg text-sm font-extrabold">
                        {{ poli.sisaAntrean }} Orang
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

                <div
                    v-if="poli.daftarDilewati && poli.daftarDilewati.length > 0"
                    class="p-4 bg-red-50 border-t border-red-100"
                >
                    <p class="text-xs font-bold text-red-600 mb-2 uppercase">Antrian Dilewati:</p>

                    <div class="flex flex-col gap-2">
                        <div
                            v-for="nomor in poli.daftarDilewati"
                            :key="nomor"
                            class="flex items-center justify-between bg-white border border-red-200 px-3 py-2 rounded-lg shadow-sm"
                        >
                            <span class="font-bold font-mono text-gray-800">{{ nomor }}</span>
                            <button
                                @click="panggilDilewati(poli.id, nomor)"
                                :disabled="loading"
                                class="text-xs bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded-md transition disabled:opacity-50"
                            >
                                Panggil Ulang
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
