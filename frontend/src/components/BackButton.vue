<script setup>
import { RouterLink } from 'vue-router';
</script>

<template>
  <section>
    <div class="container m-auto px-2 py-2">
      <RouterLink
        to="/jobs"
        class="flex items-center text-green-500 hover:text-green-600"
      >
        <i class="pi pi-arrow-circle-left mr-2"></i> Back to Job Listings
      </RouterLink>
    </div>
  </section>
</template>
