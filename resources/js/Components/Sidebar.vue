<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import {
  LayoutDashboard,
  Users,
  ListOrdered,
  Settings,
} from "lucide-vue-next";

const page = usePage();

const menus = [
  {
    name: "Dashboard",
    href: "/dashboard",
    icon: LayoutDashboard,
  },
  {
    name: "Users",
    href: "/users",
    icon: Users,
  },
  {
    name: "Antrean",
    href: "/queue",
    icon: ListOrdered,
  },
  {
    name: "Settings",
    href: "/settings",
    icon: Settings,
  },
];
</script>


<template>

<aside class="w-72 min-h-screen bg-slate-950 text-slate-300 flex flex-col">

    <!-- Logo -->
    <div class="h-20 flex items-center px-6 border-b border-slate-800">

        <div
            class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold"
        >
            A
        </div>

        <div class="ml-3">
            <h1 class="text-white font-bold text-lg">
                Sistem Antrean
            </h1>

            <p class="text-xs text-slate-400">
                Klinik Management
            </p>
        </div>

    </div>


    <!-- Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2">

        <p class="text-xs uppercase text-slate-500 px-3 mb-3">
            Menu Utama
        </p>


        <Link
            v-for="menu in menus"
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

      <Link
        :href="route('logout')"
        method="post"
        as="button"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-600 transition"
      >
        <LogOut class="w-5 h-5" />

        <span>Logout</span>
      </Link>

    </div>
</aside>

</template>
