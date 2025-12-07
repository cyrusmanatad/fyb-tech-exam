<script setup>
import ProductListings from "./ProductListings.vue";
</script>

<template>
  <div class="bg-gray-50 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">Product Activity Log</h2>
        <p class="mx-auto mt-4 max-w-4xl text-lg text-gray-500">
          View the latest products added or removed from the system. This section helps you track recent changes and stay updated on product management actions.
        </p>
      </div>

      <div class="mt-10 space-y-4">
        <ProductListings :limit="3" :showButton="false" />
      </div>
      <div class="mt-10 text-center">
        <a
          href="/products"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-5 py-3 text-base font-medium text-white hover:bg-blue-700"
        >
          View All Products
        </a>
      </div>
    </div>
  </div>
</template>
