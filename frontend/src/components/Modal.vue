<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close']);

const closeModal = () => {
  emit('close');
};
</script>

<template>
  <div
    class="fixed inset-0 h-full bg-black/50 z-50 flex justify-center items-center p-4"
  >
    <div
      class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col"
    >
      <div
        class="p-6 border-b border-gray-200 flex justify-between items-center"
      >
        <div>
          <h2 class="text-2xl font-bold text-gray-900">{{ product.title }}</h2>
          <p class="text-md text-gray-600">{{ product.department }}</p>
        </div>
        <button
          @click="closeModal"
          class="p-2 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600"
        >
          <i class="pi pi-times"></i>
        </button>
      </div>

      <div class="p-6 overflow-y-auto">
        <div
          class="flex flex-wrap gap-x-6 gap-y-3 text-sm text-gray-700 mb-6"
        >
          <div v-if="product.location" class="flex items-center">
            <i class="pi pi-map-marker mr-1.5 text-gray-500"></i>
            <span>{{ product.location }}</span>
          </div>
          <div class="flex items-center">
            <i class="pi pi-dollar mr-1.5 text-gray-500"></i>
            <span>{{ product.salary }}</span>
          </div>
          <div class="flex items-center">
            <i class="pi pi-clock mr-1.5 text-gray-500"></i>
            <span>Posted {{ product.postedAgo }}</span>
          </div>
          <div v-if="product.type" class="flex items-center">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
            >
              {{ product.type }}
            </span>
          </div>
        </div>

        <div class="prose max-w-none text-sm text-gray-600">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">
            Job Description
          </h3>
          <p>{{ product.description || 'No description provided.' }}</p>
        </div>
      </div>

      <div
        class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-end space-x-3"
      >
        <button
          @click="closeModal"
          type="button"
          class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Close
        </button>
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
        >
          Apply Now
        </button>
      </div>
    </div>
  </div>
</template>