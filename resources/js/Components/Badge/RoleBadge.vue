<script setup>
defineProps({
    role: String,
});
</script>

<template>
    <span
        class="px-3 py-1 rounded-full text-xs font-semibold"
        :class="
            role === 'admin'
                ? 'bg-blue-100 text-blue-700'
                : 'bg-green-100 text-green-700'
        "
    >
        {{ role }}
    </span>
</template>
