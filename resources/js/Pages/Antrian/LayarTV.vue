<template>
    <div class="min-h-screen bg-gray-100 flex flex-col font-sans overflow-hidden relative">

        <div
            v-if="!audioAktif"
            @click="aktifkanAudio"
            class="fixed inset-0 bg-black/90 z-50 flex flex-col items-center justify-center text-white cursor-pointer backdrop-blur-sm"
        >
            <div class="bg-blue-600 px-10 py-8 rounded-3xl shadow-2xl text-center border-4 border-blue-400 animate-bounce">
                <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                </svg>
                <h2 class="text-3xl font-extrabold tracking-wide mb-2">Sistem Antrian Siap</h2>
                <p class="text-lg text-blue-100 font-medium">Klik di mana saja untuk memulai layar dan mengaktifkan suara</p>
            </div>
        </div>

        <header class="bg-blue-600 text-white p-4 flex justify-between items-center shadow-md">
            <div class="flex items-center space-x-4">
                <div class="bg-white p-2 rounded flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12zm0-9a1 1 0 011 1v2h2a1 1 0 110 2h-2v2a1 1 0 11-2 0v-2H7a1 1 0 110-2h2V8a1 1 0 011-1z"></path></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-wider">KLINIK SEHAT SELALU</h1>
                    <p class="text-xs text-blue-200">Jl. Contoh Alamat No. 123, Kota | Telp: 08123456789</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold">{{ tanggalSekarang }}</p>
                <h2 class="text-3xl font-bold font-mono">{{ waktuSekarang }}</h2>
            </div>
        </header>

        <main class="flex-1 grid grid-cols-12 gap-4 p-4">
            <div class="col-span-4 flex flex-col gap-4">
                <div class="bg-indigo-500 text-white rounded-lg shadow-lg flex-1 flex flex-col overflow-hidden border-4 border-indigo-400">
                    <div class="bg-indigo-600/50 py-3 text-center border-b border-indigo-400">
                        <h2 class="text-2xl font-bold tracking-widest uppercase">Nomor Antrian</h2>
                    </div>
                    <div class="flex-1 flex items-center justify-center">
                        <span class="text-9xl font-bold drop-shadow-md font-mono">{{ antrianAktif.nomor }}</span>
                    </div>
                    <div class="bg-indigo-600/50 py-4 text-center border-t border-indigo-400">
                        <h3 class="text-xl font-semibold uppercase tracking-widest">{{ antrianAktif.loket }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-span-8 bg-black rounded-lg shadow-lg overflow-hidden border-4 border-gray-300">
                <iframe
                    v-if="videoUrl"
                    class="w-full h-full"
                    :src="videoUrl"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </main>

        <div class="grid grid-cols-5 gap-4 px-4 pb-4">
            <div v-for="poli in daftarPoli" :key="poli.id" :class="[poli.color, 'text-white rounded-lg shadow-lg overflow-hidden flex flex-col text-center transition-all duration-500']">
                <div class="py-2 bg-black/20 font-semibold text-sm truncate px-2 uppercase">
                    {{ poli.nama }}
                </div>
                <div class="py-6 text-6xl font-bold font-mono">
                    {{ poli.nomorTerkini }}
                </div>
            </div>
        </div>

        <footer class="bg-blue-600 text-white py-2 overflow-hidden shadow-inner">
            <marquee class="text-sm font-semibold tracking-wide">
                JAM BUKA LAYANAN KAMI ADALAH PUKUL 07:00 S.D 21:00. TERIMA KASIH ATAS KUNJUNGAN ANDA. KAMI SENANTIASA MELAYANI SEPENUH HATI.
            </marquee>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

// ==========================================
// 1. STATE & DATA INITIALIZATION
// ==========================================
const audioAktif = ref(false); // State untuk overlay keamanan autoplay browser

const antrianAktif = ref({
    nomor: '---',
    loket: 'MENUNGGU PANGGILAN'
});

const daftarPoli = ref([]);

const daftarWarna = [
    'bg-purple-500', 'bg-green-500', 'bg-red-500',
    'bg-blue-500', 'bg-orange-500', 'bg-teal-500'
];

// Fungsi untuk bypass Autoplay Policy Browser
const aktifkanAudio = () => {
    audioAktif.value = true;
    // Pancingan audio agar browser memberikan izin Web Speech API
    const testSpeech = new SpeechSynthesisUtterance('');
    window.speechSynthesis.speak(testSpeech);
};

// Fungsi untuk mengambil data dari Laravel
const fetchDaftarPoli = async () => {
    try {
        const response = await axios.get('/api/layar-antrian');

        // Pastikan kita mendapat array, terlepas dari format JSON yang dikembalikan Laravel
        const rawData = Array.isArray(response.data) ? response.data : (response.data.data || []);

        daftarPoli.value = rawData.map((poli, index) => ({
            ...poli,
            // Fallback nama properti jika berbeda dari backend
            nomorTerkini: poli.nomorTerkini || poli.nomor_terkini || '-',
            color: daftarWarna[index % daftarWarna.length]
        }));
    } catch (error) {
        console.error("Gagal mengambil data poli:", error);
    }
};

// ==========================================
// 2. LOGIKA VIDEO YOUTUBE RANDOM
// ==========================================
const videoUrl = ref('');
const kumpulanVideoYoutube = ['5qap5aO4i9A', 'jfKfPfyJRdk', 'kJQP7kiw5Fk'];

const setRandomVideo = () => {
    const randomIndex = Math.floor(Math.random() * kumpulanVideoYoutube.length);
    const videoId = kumpulanVideoYoutube[randomIndex];
    videoUrl.value = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}&controls=0`;
};

// ==========================================
// 3. LOGIKA JAM & TANGGAL REALTIME
// ==========================================
const waktuSekarang = ref('');
const tanggalSekarang = ref('');
let timerInterval = null;

const updateJam = () => {
    const now = new Date();
    waktuSekarang.value = now.toLocaleTimeString('id-ID', { hour12: false });
    tanggalSekarang.value = now.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
};

// ==========================================
// 4. LOGIKA SUARA (TEXT-TO-SPEECH)
// ==========================================
const panggilSuara = (nomor, loket) => {
    // Memecah teks nomor agar dibaca per huruf/angka (Contoh: "A001" -> "A 0 0 1")
    const nomorDieja = nomor.split('').join(' ');

    const teksPanggilan = `Nomor antrian, ${nomorDieja}, silakan menuju, ${loket}`;

    const speech = new SpeechSynthesisUtterance(teksPanggilan);
    speech.lang = 'id-ID';
    speech.rate = 0.8;
    speech.pitch = 1.0;

    window.speechSynthesis.speak(speech);
};

// ==========================================
// 5. LOGIKA WEBSOCKET (LARAVEL ECHO / REVERB)
// ==========================================
const setupWebSocket = () => {
    window.Echo.channel('antrian-channel')
        .listen('.panggilan.baru', (e) => {
            console.log("Panggilan masuk via WS:", e);

            // Update Kotak Utama
            antrianAktif.value.nomor = e.nomorAntrian;
            antrianAktif.value.loket = e.loket;

            // Update Kotak Kecil Daftar Poli
            const poliIndex = daftarPoli.value.findIndex(p => p.id === e.poli_id);
            if (poliIndex !== -1) {
                daftarPoli.value[poliIndex].nomorTerkini = e.nomorAntrian;
            }

            // Putar Suara
            panggilSuara(e.nomorAntrian, e.loket);
        });
};

// ==========================================
// LIFECYCLE HOOKS
// ==========================================
onMounted(() => {
    fetchDaftarPoli();
    setRandomVideo();
    updateJam();
    timerInterval = setInterval(updateJam, 1000);
    setupWebSocket();
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
    window.Echo.leave('antrian-channel');
});
</script>

<style scoped>
/* Agar angka lebih rapi dan sama lebarnya */
.font-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
</style>
