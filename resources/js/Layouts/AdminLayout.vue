<script setup>
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
</template>
