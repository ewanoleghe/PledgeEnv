<script setup>
import { defineProps, defineEmits, watch } from 'vue';

const props = defineProps({
    isOpen: Boolean,
    type: String,
    content: String
});

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close'); // Properly close the modal
};
</script>

<template>
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex justify-center items-center bg-black bg-opacity-50">
            <div class="modal-content p-6 w-[95%] max-w-2xl bg-white rounded-lg shadow-lg">
                <h2 class="text-lg font-bold mb-4">
                    {{ type === 'terms' ? 'Terms of Service' : 'Privacy Policy' }}
                </h2>
                <div class="markdown-body overflow-auto max-h-96 p-2 text-gray-700">
                    <p v-html="content"></p>
                </div>
                <footer class="flex justify-end mt-4">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="closeModal">
                        Close
                    </button>
                </footer>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.modal-content {
    max-height: 90vh;
    overflow-y: auto;
}

/* Fade transition */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
