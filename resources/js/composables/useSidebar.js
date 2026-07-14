import { ref } from "vue";

const isOpen = ref(true);

export function useSidebar() {
    const toggleSidebar = () => {
        isOpen.value = !isOpen.value;
    };

    return {
        isOpen,
        toggleSidebar
    };
}
