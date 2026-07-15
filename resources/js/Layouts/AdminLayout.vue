<script setup>
import { watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { toast, Toaster } from "vue-sonner";

const page = usePage();

watch(
    () => page.props.flash?.success,
    (success) => {
        if (success) {
            toast.success(success);
        }
    },
    {
        immediate: true,
    }
);

watch(
    () => page.props.flash?.error,
    (error) => {
        if (error) {
            toast.error(error);
        }
    },
    {
        immediate: true,
    }
);

import Sidebar from "@/Components/Sidebar.vue";
import Navbar from "@/Components/Navbar.vue";
</script>

<template>
  <div class="flex min-h-screen">
    <Sidebar />

    <div class="flex-1 flex flex-col">
      <Navbar />

      <main class="p-6 bg-gray-100 flex-1">
        <slot />
      </main>
    </div>
    </div>
    <Toaster
        position="top-right"
        richColors
        closeButton
    />
</template>
