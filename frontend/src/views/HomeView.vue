<script setup>
import Hero from "../components/Hero.vue";
import JobListings from "../components/JobListings.vue";
import HomeCards from "../components/HomeCards.vue";
</script>

<template>
  <Hero title="Find Your Dream Job!" />
  <HomeCards />
  <JobListings :limit="3" :show-button="true" />
</template>

<style scoped></style>
