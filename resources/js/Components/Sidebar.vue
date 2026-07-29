<script setup>
import { computed } from "vue"
import { Link, usePage, router } from "@inertiajs/vue3";
import Swal from 'sweetalert2'
import {
  LayoutDashboard,
  Users,
  ListOrdered,
  Settings,
  LogOut,
} from "lucide-vue-next";

const page = usePage();

const user = computed(() => page.props.auth.user);
const roles = computed(() => page.props.auth.roles);

const menus = [
  {
    name: "Dashboard",
    href: "/dashboard",
    icon: LayoutDashboard,
    roles: ["admin", "petugas"],
  },
  {
    name: "Users",
    href: "/users",
    icon: Users,
    roles: ["admin"],
  },
  {
    name: "Poli",
    href: "/polis",
    icon: ListOrdered,
    roles: ["admin"],
  },
  {
    name: "Settings",
    href: "/setting",
    icon: Settings,
  },
];

const konfirmasiLogout = () => {
    Swal.fire({
        title: "Yakin ingin keluar?",
        text: "Anda harus login kembali untuk mengakses sistem.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626", // Warna merah Tailwind (red-600)
        cancelButtonColor: "#475569", // Warna abu-abu Tailwind (slate-600)
        confirmButtonText: "Ya, Logout",
        cancelButtonText: "Batal",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Gunakan router Inertia untuk menembak rute POST logout
            // route('logout') berasal dari Ziggy (sama seperti href sebelumnya)
            router.post(route('logout'));
        }
    });
};
</script>


<template>

<aside class="w-72 min-h-screen bg-slate-950 text-slate-300 flex flex-col">

    <!-- Logo -->
    <div class="h-20 flex items-center px-6 border-b border-slate-800">

        <img
            :src="$page.props.app_settings.logo_klinik || '/images/default-logo.png'"
            alt="Logo"
            class="h-9 w-auto object-contain"
        />

        <div class="ml-3">
            <h1 class="text-white font-bold text-lg">
                Sistem Antrean
            </h1>

            <p class="text-xs text-slate-400">
                {{$page.props.app_settings.nama_klinik||'Klinik Management'}}
            </p>
        </div>

    </div>


    <!-- Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2">

        <p class="text-xs uppercase text-slate-500 px-3 mb-3">
            Menu Utama
        </p>


        <Link
            v-for="menu in menus.filter(menu =>
                !menu.roles ||
                menu.roles.some(role => roles.includes(role))
            )"
            :key="menu.href"
            :href="menu.href"

            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"

            :class="
                page.url === menu.href
                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                : 'hover:bg-slate-800 hover:text-white'
            "
        >

            <component
                :is="menu.icon"
                class="w-5 h-5"
            />

            <span>
                {{ menu.name }}
            </span>

        </Link>


    </nav>


    <!-- User -->
    <!-- <div class="p-4 border-t border-slate-800">

        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-800">

            <div
                class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white"
            >
                H
            </div>


            <div>
                <p class="text-white text-sm font-semibold">
                    Harits
                </p>

                <p class="text-xs text-slate-400">
                    Administrator
                </p>
            </div>

        </div> -->
<!--
    </div> -->
     <div class="p-4 border-t border-slate-700">

      <button
        @click="konfirmasiLogout"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-600 transition text-slate-300 hover:text-white"
      >
        <LogOut class="w-5 h-5" />
        <span>Logout</span>
      </button>
    </div>
</aside>

</template>
