<script setup></script>

<template>
  <div
      class="rounded-lg border border-gray-200 bg-white p-6 transition-shadow animate-pulse duration-200"
    >
    <div class="flex flex-col sm:flex-row sm:justify-between">
      <div>
        <h3
          class="cursor-pointer text-lg font-bold text-gray-900 hover:text-blue-600"
        >
          <div class="h-4 bg-gray-300 my-2 rounded w-3/4"></div>
        </h3>
        <div
          class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500"
        >
          <span class="font-medium bg-gray-300 my-2 w-10 h-2 rounded"></span>
          <!-- <span class="hidden sm:inline">&bull;</span> -->
          <span class="font-medium bg-gray-300 my-2 w-10 h-2 rounded"></span>
          <!-- <span class="hidden sm:inline">&bull;</span> -->
          <span class="font-medium bg-gray-300 my-2 w-10 h-2 rounded"></span>
        </div>
      </div>
      <div class="mt-4 flex shrink-0 items-start space-x-2 sm:mt-0">
        <button class="rounded-md border border-transparent bg-gray-300 my-2 px-8 py-2 text-sm font-medium">
          &nbsp;
        </button>
        <button class="rounded-md border border-transparent bg-gray-300 my-2 px-8 py-2 text-sm font-medium">
          &nbsp;
        </button>
        <button class="rounded-md border border-transparent bg-gray-300 my-2 px-8 py-2 text-sm font-medium">
          &nbsp;
        </button>
      </div>
    </div>
    <div class="mt-4 border-t border-gray-200 pt-4">
      <div class="h-4 bg-gray-300 my-2 rounded w-5/6"></div>
    </div>
  </div>
</template>