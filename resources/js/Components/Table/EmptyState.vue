<script setup>
defineProps({
    colspan: Number,
    text: String,
});
</script>

<template>
<tr>

    <td
        :colspan="colspan"
        class="text-center py-12 text-gray-500"
    >
        {{ text }}
    </td>

</tr>
</template>
