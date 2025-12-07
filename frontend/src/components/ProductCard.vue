<script setup>
import { defineEmits, ref } from 'vue';
import EditProductModal from './modals/EditProductModal.vue';
import DeleteProductModal from './modals/DeleteProductModal.vue';
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

const props = defineProps({
  product: Object,
});

const isModalOpen = ref(false);
const isEditModalOpen = ref(false);

const emit = defineEmits(['deleted']);

const postedAgo = (date) => {
  const now = new Date();
  const postedDate = new Date(date);
  const diffTime = Math.abs(now - postedDate);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 0) return 'today';
  if (diffDays === 1) return '1 day ago';
  return `${diffDays} days ago`;
};

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const openEditModal = () => {
  isEditModalOpen.value = true;
};

const closeEditModal = () => {
  isEditModalOpen.value = false;
};


</script>

<template>
  <div
    class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
  >
    <div class="flex flex-col sm:flex-row sm:justify-between">
      <div>
        <h3
          class="text-lg font-bold text-gray-900 hover:text-blue-600 cursor-pointer"
        >
          {{product.sku_desc}} | {{ product.sku_code }}
        </h3>
        <div
          class="mt-2 flex items-center flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500"
        >
          <span class="font-medium text-green-600">{{product.sku_price}}</span>
          <span class="hidden sm:inline">&bull;</span>
          <span>{{product.user.name}}</span>
          <span class="hidden sm:inline">&bull;</span>
          <span>{{ postedAgo(product.created_at) }}</span>
        </div>
      </div>
      <div v-if="product.user.id == auth.user.id" class="mt-4 sm:mt-0 flex items-start space-x-2 shrink-0">
        <button @click="openEditModal" class="px-4 cursor-pointer py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Edit
        </button>
        <button @click="openModal" class="px-4 cursor-pointer py-2 border border-gray-200 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Delete
        </button>
      </div>
    </div>
  </div>
  <DeleteProductModal v-if="isModalOpen" :product="product" @close="closeModal" />
  <EditProductModal v-if="isEditModalOpen" :product="product" @close="closeEditModal" />
</template>
