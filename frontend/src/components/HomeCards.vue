<script setup>
import { RouterLink } from "vue-router";
import Card from "./Card.vue";
</script>

<template>
  <!-- Developers and Employers -->
  <section class="py-4">
    <div class="container-xl m-auto lg:container">
      <div class="grid grid-cols-1 gap-4 rounded-lg p-4 md:grid-cols-2">
        <Card>
          <h2 class="text-2xl font-bold">Job Seekers</h2>
          <p class="mt-2 mb-4">
            Browse our jobs listings and start your career today
          </p>
          <RouterLink
            to="/jobs"
            class="inline-block rounded-lg bg-black px-4 py-2 text-white hover:bg-gray-700"
          >
            Browse Jobs
          </RouterLink>
        </Card>
        <Card bg="bg-green-100">
          <h2 class="text-2xl font-bold">For Employers</h2>
          <p class="mt-2 mb-4">
            List your job to find the perfect developer for the role
          </p>
          <RouterLink
            to="/jobs/add"
            class="inline-block rounded-lg bg-green-500 px-4 py-2 text-white hover:bg-green-600"
          >
            Add Job
          </RouterLink>
        </Card>
      </div>
    </div>
  </section>
</template>
