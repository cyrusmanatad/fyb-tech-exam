<script setup>
import ProductFilters from "../components/ProductFilters.vue";
import ProductListings from "../components/ProductListings.vue";
</script>

<template>
  <div class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        <aside class="lg:col-span-1">
          <ProductFilters />
        </aside>
        <main class="lg:col-span-3">
          <ProductListings :showFilter="true" />
        </main>
      </div>
    </div>
  </div>
</template>
