<script setup>
import { defineEmits, reactive, ref } from 'vue';
import axios from "@/axios"; // axios.js file
import { useAuthStore } from "@/stores/auth";
import PulseLoader from "vue-spinner/src/PulseLoader.vue";

const formData = reactive({
  user_id: null,
  sku_desc: null,
  sku_code: '',
  sku_price: 0,
  sku_uom: '',
});

const creating = ref(false);

const emit = defineEmits(['close', 'created']);

const handleCreate = async () => {
  const auth = useAuthStore();

  formData.user_id = auth.user.id;

  creating.value = !creating.value;
  
  try {
    const { data } = await axios.post(`/api/products`, {
      user_id: auth.user.id,
      sku_desc: formData.sku_desc,
      sku_code: formData.sku_code,
      sku_price: formData.sku_price,
      sku_uom: formData.sku_uom
    });

    // Optionally update the auth store or notify success
    emit('created');
    console.log("Product created successfully", data);
  } catch (error) {
    console.error("Failed to create product", error);
  } finally {
    creating.value = !creating.value;
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
          <h2 class="text-2xl font-bold text-gray-900">{{ formData.sku_desc || '----' }}</h2>
          <p class="text-md text-gray-600">Please fill out the form.</p>
        </div>
        <button
          @click="closeModal"
          class="p-2 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600"
        >
          <i class="pi pi-times"></i>
        </button>
      </div>

      <form class="space-y-6" v-on:submit.prevent="handleCreate">
        <div class="p-6 overflow-y-auto">
          <div>
            <label
              htmlFor="name"
              class="block text-sm font-medium text-gray-700"
            >
              Product Name
            </label>
            <div class="mt-1">
              <input
                id="name"
                name="name"
                type="text"
                v-model="formData.sku_desc"
                required
                class="block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label
              htmlFor="sku_code"
              class="block text-sm font-medium text-gray-700"
            >
              SKU Code
            </label>
            <div class="mt-1">
              <input
                id="sku_code"
                name="sku_code"
                type="text"
                v-model="formData.sku_code"
                required
                class="block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label
              htmlFor="description"
              class="block text-sm font-medium text-gray-700"
            >
              Description
            </label>
            <div class="mt-1">
              <input
                id="description"
                name="description"
                type="text"
                autoComplete="current-description"
                class="block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label
              htmlFor="price"
              class="block text-sm font-medium text-gray-700"
            >
              Price
            </label>
            <div class="mt-1">
              <input
                id="price"
                name="price"
                type="text"
                autoComplete="current-price"
                v-model="formData.sku_price"
                required
                class="block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label
              htmlFor="uom"
              class="block text-sm font-medium text-gray-700"
            >
              UOM
            </label>
            <div class="mt-1">
              <input
                id="uom"
                name="uom"
                type="text"
                autoComplete="current-uom"
                v-model="formData.sku_uom"
                required
                class="block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
              />
            </div>
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
            Close
          </button>
          <button
            type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm"
            :class="[
              creating
                ? 'text-black bg-gray-300 border-gray-800 cursor-not-allowed opacity-50'
                : 'text-white bg-blue-600 hover:bg-blue-700 hover:cursor-pointer',
            ]"
          >
            {{creating ? "Please wait" : "Save"}}
            <PulseLoader v-show="creating" class="ml-2" size="8px" color="black"/>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>