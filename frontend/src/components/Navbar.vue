<script setup>
import { RouterLink, useRoute } from "vue-router";
import Logo from "../assets/vue.svg";
import { ref } from "vue";

const menus = ref([
  {
    title: "Home",
    url: "/",
  },
  {
    title: "Jobs",
    url: "/jobs",
  },
  {
    title: "Add Job",
    url: "/jobs/add",
  },
]);

const isActiveLink = (routePath) => {
  const route = useRoute();
  return route.path === routePath;
};
</script>

<template>
  <nav class="border-b border-green-500 bg-green-700">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="flex h-20 items-center justify-between">
        <div
          class="flex flex-1 items-center justify-center md:items-stretch md:justify-start"
        >
          <!-- Logo -->
          <RouterLink class="mr-4 flex flex-shrink-0 items-center" to="/">
            <img class="h-10 w-auto" :src="Logo" alt="Vue Jobs" />
            <span class="ml-2 hidden text-2xl font-bold text-white md:block"
              >Vue Jobs</span
            >
          </RouterLink>
          <div class="md:ml-auto">
            <div class="flex space-x-2">
              <RouterLink
                v-for="menu in menus"
                :key="menu.url"
                :to="menu.url"
                :class="[
                  isActiveLink(menu.url) ? 'bg-green-900' : '',
                  'rounded-md',
                  'px-3',
                  'py-2',
                  'text-white',
                  'hover:bg-gray-900',
                  'hover:text-white',
                ]"
                >{{ menu.title }}</RouterLink
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped></style>
