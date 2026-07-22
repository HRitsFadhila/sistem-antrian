<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Stethoscope, CheckCircle2, Printer, X } from "lucide-vue-next";

// Menerima daftar poli dari Controller
const props = defineProps({
    polis: Array,
});

const isProcessing = ref(false);
const showTicketModal = ref(false);

// State untuk menyimpan data tiket sementara
const dataTiket = ref({
    namaPoli: "",
    nomorAntrian: "",
    tanggal: "",
});

const ambilAntrian = (poli) => {
    // Cegah klik berulang
    if (isProcessing.value) return;
    isProcessing.value = true;

    // Mengirim request POST ke backend
    router.post(
        "/antrian",
        { poli_id: poli.id },
        {
            preserveScroll: true,
            onSuccess: (page) => {
                isProcessing.value = false;

                // Mengambil data tiket dari flash message backend
                // (Pastikan backend mengirim flash 'nomor_antrian')
                dataTiket.value = {
                    namaPoli: poli.nama,
                    nomorAntrian: page.props.flash.no_antrian ?? 'MEMPROSES...',
                    tanggal: new Date().toLocaleString("id-ID", {
                        dateStyle: "long",
                        timeStyle: "short",
                    }),
                };

                // Tampilkan pop-up tiket
                showTicketModal.value = true;
            },
            onError: () => {
                isProcessing.value = false;
                alert("Gagal mengambil antrian. Silakan coba lagi.");
            }
        }
    );
};

const tutupTiket = () => {
    showTicketModal.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">

        <div class="max-w-4xl mx-auto text-center mb-12">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                Ambil Nomor Antrian
            </h1>
            <p class="mt-4 text-lg text-slate-500">
                Silakan pilih poli tujuan Anda dengan mengeklik kartu di bawah ini.
            </p>
        </div>

        <div class="max-w-5xl mx-auto grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            <button
                v-for="poli in polis"
                :key="poli.id"
                @click="ambilAntrian(poli)"
                :disabled="isProcessing"
                class="group relative bg-white rounded-3xl p-8 text-left shadow-sm border border-slate-200 hover:shadow-xl hover:border-blue-500 hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed"
            >
                <div class="flex items-center justify-between mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <Stethoscope class="w-7 h-7" />
                    </div>
                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full group-hover:bg-blue-100 group-hover:text-blue-700 transition-colors duration-300">
                        {{ poli.prefix }}
                    </span>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-blue-700 transition-colors">
                    {{ poli.nama }}
                </h3>

                <p class="text-slate-500 text-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Buka - Silakan ambil antrian
                </p>
            </button>
        </div>

        <div
            v-if="showTicketModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
        >
            <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">

                <div class="bg-blue-600 p-6 text-center text-white relative">
                    <button @click="tutupTiket" class="absolute top-4 right-4 text-white/70 hover:text-white transition">
                        <X class="w-6 h-6" />
                    </button>
                    <div class="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-4 backdrop-blur-md">
                        <CheckCircle2 class="w-10 h-10 text-white" />
                    </div>
                    <h2 class="text-2xl font-bold">Antrian Berhasil!</h2>
                    <p class="text-blue-100 text-sm mt-1">Silakan tangkap layar atau cetak tiket ini.</p>
                </div>

                <div class="p-8 text-center bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px]">
                    <p class="text-slate-500 font-medium mb-1">{{ dataTiket.namaPoli }}</p>

                    <div class="my-6 py-6 border-y-2 border-dashed border-slate-200">
                        <p class="text-sm text-slate-400 uppercase tracking-widest font-semibold mb-2">Nomor Antrian</p>
                        <h1 class="text-6xl font-black text-slate-900 tracking-tighter">
                            {{ dataTiket.nomorAntrian }}
                        </h1>
                    </div>

                    <p class="text-sm text-slate-500">{{ dataTiket.tanggal }}</p>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100">
                    <button
                        @click="tutupTiket"
                        class="w-full py-3 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-semibold transition"
                    >
                        Tutup & Kembali
                    </button>
                </div>

            </div>
        </div>

    </div>
</template>
