<script setup>
import { onMounted, reactive } from "vue";
import JobCard from "./JobCard.vue";
import { RouterLink } from "vue-router";
import PulseLoader from "vue-spinner/src/PulseLoader.vue";
import axios from "axios";

defineProps({
  limit: Number,
  showButton: {
    type: Boolean,
    default: false,
  },
});

const state = reactive({
  jobs: [],
  isLoading: true,
});

onMounted(async () => {
  try {
    const response = await axios.get("api/job-listings");
    state.jobs = await response.data;
  } catch (error) {
  } finally {
    state.isLoading = false;
  }
});
</script>

<template>
  <!-- Browse Jobs -->
  <section class="bg-green-50 px-4 py-10">
    <div class="container-xl m-auto lg:container">
      <h2 class="mb-6 text-center text-3xl font-bold text-green-500">
        Browse Jobs
      </h2>
      <!-- Show loading spinner while loading is true -->
      <div v-if="state.isLoading" class="py-6 text-center text-gray-500">
        <PulseLoader />
      </div>
      <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Job Listing 1 -->
        <JobCard
          v-for="job in state.jobs.slice(0, limit || state.jobs.length)"
          :key="job.id"
          :job="job"
        />
      </div>
    </div>
  </section>

  <section v-if="showButton" class="m-auto my-10 max-w-lg px-6">
    <RouterLink
      to="/jobs"
      class="block rounded-xl bg-black px-6 py-4 text-center text-white hover:bg-gray-700"
      >View All Jobs</RouterLink
    >
  </section>
</template>
