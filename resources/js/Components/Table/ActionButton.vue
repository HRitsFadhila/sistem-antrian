<script setup>
import { Pencil, Trash2 } from "lucide-vue-next";

defineProps({
    editUrl: String,
});

const emit = defineEmits(["delete"]);
</script>

<template>
    <div class="flex justify-center gap-2">

        <Link
            :href="editUrl"
            class="p-2 rounded bg-yellow-100"
        >
            <Pencil class="w-4 h-4"/>
        </Link>

        <button
            @click="emit('delete')"
            class="p-2 rounded bg-red-100"
        >
            <Trash2 class="w-4 h-4"/>
        </button>

    </div>
</template>
