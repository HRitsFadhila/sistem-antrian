<script setup>
defineProps({
    show: Boolean,
    title: String,
    message: String,
});

const emit = defineEmits(["close", "confirm"]);
</script>

<template>
    <Transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">

                <div class="p-6">
                    <h2 class="text-xl font-bold">
                        {{ title }}
                    </h2>

                    <p class="mt-3 text-slate-500">
                        {{ message }}
                    </p>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t">

                    <button
                        @click="$emit('close')"
                        class="px-5 py-2 border rounded-lg"
                    >
                        Batal
                    </button>

                    <button
                        @click="$emit('confirm')"
                        class="px-5 py-2 bg-red-600 text-white rounded-lg"
                    >
                        Hapus
                    </button>

                </div>

            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: .2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
