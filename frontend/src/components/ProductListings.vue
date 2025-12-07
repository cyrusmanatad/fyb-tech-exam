<script setup>
import { onMounted, reactive, watch, computed, onWatcherCleanup } from "vue";
import SkeletonLoader from "./SkeletonLoader.vue";
import ProductCard from "./ProductCard.vue";
import axios from "axios";
import { useProductsStore } from '@/stores/products';
import { useRoute } from "vue-router";

const route = useRoute();
const store = useProductsStore();

// Props
const props = defineProps({
  limit: Number,
  showButton: {
    type: Boolean,
    default: false,
  },
  showFilter: {
    type: Boolean,
    default: false,
  },
});

// State
const state = reactive({
  search: ""
});

// Computed
const displayedProducts = computed(() =>
  props.limit ? store.products.slice(0, props.limit) : store.products,
);

// Methods
function goToPage(page) {
  if (page >= 1 && page <= store.lastPage) {
    store.currentPage = page;
    store.loadProducts(page, store.search);
  }
}

// Watchers
watch(
  () => state.search,
  (newSearch) => {
    const controller = new AbortController();
    store.currentPage = 1;

    store.loadProducts(1, newSearch.toLowerCase());

    onWatcherCleanup(() => {
      controller.abort();
    });
  },
);

watch(
  () => store.currentPage,
  (newPage) => {
    store.loadProducts(newPage);
  },
);

// Lifecycle
onMounted(async () => {
  await store.loadProducts();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Filter Section -->
    <div
      v-if="showFilter"
      class="flex flex-col md:flex-row md:items-center md:justify-between"
    >
      <h2 class="mb-4 text-2xl font-bold text-gray-900 md:mb-0">
        Product Listings
      </h2>

      <div class="flex items-center space-x-4">
        <!-- Search Box -->
        <div class="relative">
          <div
            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
          >
            <i class="pi pi-search text-gray-400"></i>
          </div>
          <input
            v-model="state.search"
            class="block w-100 rounded-md border border-gray-300 bg-white px-7 py-2 text-sm placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
            placeholder="Type a product name or sku code"
            type="search"
          />
        </div>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <SkeletonLoader v-if="store.loading" v-for="n in limit || 5" :key="n" />

    <!-- Product Cards -->
    <div v-else class="space-y-4">
      <ProductCard v-for="product in displayedProducts" :key="product.id" :product="product" />

      <!-- Pagination -->
      <div v-if="store.total > 0 && store.products.length > 0 && route.name !== 'home'" class="mt-6 flex justify-center space-x-2">
        <button
          @click="goToPage(1)"
          class="cursor-pointer rounded border border-gray-200 bg-white px-2 py-1 text-sm text-gray-700 hover:bg-blue-500 hover:text-white"
        >
          &lsaquo; First
        </button>

        <button
          v-for="link in store.links"
          :key="link.page"
          @click="goToPage(link.page)"
          v-html="link.label"
          class="cursor-pointer rounded border border-gray-200 bg-blue-500 px-2 py-1 text-sm text-gray-700 hover:bg-blue-500 hover:text-white"
          :class="
            link.active ? 'bg-blue-50 text-white' : 'bg-white'
          "
        ></button>

        <button
          @click="goToPage(store.lastPage)"
          class="cursor-pointer rounded border border-gray-200 bg-white px-2 py-1 text-sm text-gray-700 hover:bg-blue-500 hover:text-white"
        >
          Last &rsaquo;
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
select#sortOptions {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='gray'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
  background-size: 1.5em;
}
</style>
