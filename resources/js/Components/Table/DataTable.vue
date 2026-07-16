<script setup>
defineProps({
    resource: {
        type: Array,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    emptyMessage: {
        type: String,
        default: 'Data tidak ditemukan.',
    }
});
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr lass="text-left text-slate-600">
                    <th class="px-6 py-4">No</th>
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        class="px-6 py-4"
                        :class="col.class"
                    >
                        {{ col.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in resource.data"
                    :key="item.id || index"
                    class="border-t hover:bg-slate-50 transition"
                >
                    <td class="px-6 py-4">
                        {{ index + 1 }}
                    </td>

                    <td
                        v-for="col in columns"
                        :key="col.key"
                        class="px-6 py-4"
                        :class="col.cellClass"
                    >
                        <slot :name="`cell-${col.key}`" :item="item">
                            {{ item[col.key] }}
                        </slot>
                    </td>
                </tr>

                <tr v-if="resource.data.length === 0">
                    <td
                        :colspan="columns.length + 1"
                        class="text-center py-12 text-slate-500"
                    >
                        {{ emptyMessage }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-between items-center mt-6 p-2">
            <div class="text-sm text-gray-500">
                Menampilkan
                {{ resource.from || 0 }} - {{ resource.to || 0 }}
                dari {{ resource.total || 0 }} data
            </div>
            <div class="flex gap-2">
                <Link
                    v-for="link in resource.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-4 py-2 rounded-lg border transition"
                    :class="[
                        link.active
                            ? 'bg-blue-600 text-white'
                            : 'bg-white hover:bg-gray-100',
                        !link.url
                            ? 'opacity-50 cursor-not-allowed'
                            : ''
                    ]"
                />
            </div>
        </div>
    </div>
</template>
