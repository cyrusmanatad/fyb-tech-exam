<script setup lang="ts">
defineProps<{
  text: string
}>()
</script>

<template>
  <div
    class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-white/60 dark:bg-slate-900/50 backdrop-blur-[2px] rounded-xl transition-opacity duration-300"
  >
    <!-- Modern Spinner -->
    <div class="relative flex items-center justify-center">
      <div
        class="w-12 h-12 border-4 border-teal-500/20 border-t-teal-500 rounded-full animate-spin"
      ></div>
      <div
        class="absolute w-6 h-6 border-4 border-teal-500/10 border-b-teal-500 rounded-full animate-spin-slow"
      ></div>
    </div>
    <p class="mt-4 text-sm font-bold text-gray-600 dark:text-slate-400 animate-pulse tracking-wide">
      {{ text }}
    </p>
  </div>
</template>
