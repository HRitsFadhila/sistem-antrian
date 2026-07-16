<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: Array,
});
</script>

<template>
    <div class="flex gap-2">
        <Link
            v-for="link in links"
            :key="link.label"
            :href="link.url ?? '#'"
            v-html="link.label"
            class="px-4 py-2 border rounded"
            :class="{
                'bg-blue-600 text-white': link.active,
                'opacity-50 pointer-events-none': !link.url
            }"
        />
    </div>
</template>
