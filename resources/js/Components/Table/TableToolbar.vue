<script setup>
</script>

<template>
    <div class="flex items-center justify-between p-5 border-b">
        <div class="flex items-center gap-3">
            <slot name="left" />
        </div>

        <div class="flex items-center gap-3">
            <slot name="right" />
        </div>
    </div>
</template>
