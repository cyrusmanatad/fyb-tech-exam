<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import { useProductsStore } from "@/stores/products";
import PulseLoader from "vue-spinner/src/PulseLoader.vue";

const store = useProductsStore();
const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const deleting = ref(false);

const emit = defineEmits(['close']);

const handleDelete = async () => {
  try {
    deleting.value = !deleting.value;
    await store.deleteProduct(props.product.id);
    console.log("Product deleted successfully");
  } catch (error) {
    console.error("Failed to delete product", error);
  } finally {
    deleting.value = !deleting.value;
    closeModal();
  }
};

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
          <h2 class="text-2xl font-bold text-gray-900">This action will permanently delete the product. Continue?</h2>
          <p class="text-md text-gray-600">{{ product.department }}</p>
        </div>
      </div>

      <div class="p-6 overflow-y-auto">
        <div class="prose max-w-none text-sm text-gray-600">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">
            {{ product.sku_desc}}
          </h3>
          <p>{{ product.sku_code }}</p>
          <p>P{{ product.sku_price }}</p>
        </div>
      </div>

      <div
        class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-end space-x-3"
      >
        <button
          @click="closeModal"
          type="button"
          class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 hover:cursor-pointer"
        >
          Cancel
        </button>
        <form v-on:submit.prevent="handleDelete">
          <button
            type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm"
            :class="[
              deleting
                ? 'text-black bg-gray-300 border-gray-800 cursor-not-allowed opacity-50'
                : 'text-white bg-red-600 hover:bg-red-700 hover:cursor-pointer',
            ]"
          >
            {{deleting ? "Please wait" : "Delete"}}
            <PulseLoader v-show="deleting" class="ml-2" size="8px" color="black"/>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>