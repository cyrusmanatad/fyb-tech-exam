<script setup>
import CreateProductModal from '@/components/modals/CreateProductModal.vue';
import Whatzup from '@/assets/whatzup.svg';
import { ref } from 'vue';
import { useProductsStore } from '../stores/products';

const store = useProductsStore();
const isModalOpen = ref(false);

const onCreated = async () => await store.loadProducts();

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

</script>

<template>
  <div className="text-center bg-white p-6 rounded-lg shadow-md">
    <img
      :src=Whatzup
      alt="Hiring illustration"
      className="mx-auto h-32 w-32"
    />
    <h3 className="mt-4 text-sm font-medium text-gray-900">
      Thinking of new product listings?
    </h3>
    <p className="mt-1 text-sm text-gray-500">
      Create a new product listing to attract potential customers and boost your sales.
    </p>
    <button
      type="button"
      @click="openModal"
      className="mt-6 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 hover:cursor-pointer"
    >
      CREATE NOW
    </button>
  </div>

  <CreateProductModal v-if="isModalOpen" @close="closeModal" @created="onCreated" />
</template>

<style scoped></style>
