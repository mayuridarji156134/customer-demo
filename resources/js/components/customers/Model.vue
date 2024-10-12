<template>
  <div class="fixed z-10 inset-0 overflow-y-auto" v-if="isVisible">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Modal Background Overlay -->
      <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeModal">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>

      <!-- Modal Content -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <!-- Slot for content (form fields) -->
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  isVisible: Boolean,
  editMode: Boolean, // Determine if it's edit or add mode
  customerId: Number, // The customer ID in case of edit mode
});

const emits = defineEmits(['close', 'save']);

const closeModal = () => {
  emits('close');
};

const handleSave = () => {
  emits('save');
};
</script>
