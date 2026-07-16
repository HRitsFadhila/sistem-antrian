<script setup>
import { Search } from "lucide-vue-next";

defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "Cari...",
    },
});

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="relative w-80">
         <Search
            class="absolute left-3 top-3.5 w-5 h-5 text-slate-400"
        />

        <input
            :value="modelValue"
            @input="emit('update:modelValue', $event.target.value)"
            type="text"
            :placeholder="placeholder"
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        />
    </div>
</template>
