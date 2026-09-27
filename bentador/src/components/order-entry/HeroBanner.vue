<script setup lang="ts">
import { BoltIcon } from '@heroicons/vue/24/outline'

defineProps<{
  title: string
  subtitle: string
  description: string
  buttonText: string
}>()

const emit = defineEmits(['action'])
</script>

<template>
  <div
    class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-teal-500 to-teal-700 p-8 lg:p-12 mb-10 text-white shadow-2xl shadow-teal-500/20 group"
  >
    <div class="relative z-10 max-w-2xl text-center md:text-left">
      <span
        class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6"
      >
        {{ subtitle }}
      </span>
      <h2
        class="text-4xl lg:text-6xl font-black mb-4 tracking-tighter leading-tight"
        v-html="title"
      ></h2>
      <p class="text-white text-sm lg:text-base font-medium mb-8 max-w-md mx-auto md:mx-0">
        {{ description }}
      </p>
      <button
        type="button"
        class="px-8 py-3.5 bg-white text-teal-600 rounded-2xl font-black text-sm hover:scale-105 active:scale-95 transition shadow-xl shadow-black/10"
        @click="emit('action')"
      >
        {{ buttonText }}
      </button>
    </div>
    <!-- Abstract Shapes -->
    <div
      class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[500px] h-[500px] bg-white/10 rounded-full blur-3xl pointer-events-none"
    ></div>
    <div
      class="absolute bottom-0 right-0 translate-y-1/2 translate-x-1/4 w-[300px] h-[300px] bg-teal-400/20 rounded-full blur-2xl pointer-events-none"
    ></div>
    <div
      class="absolute right-12 bottom-12 opacity-10 group-hover:scale-110 transition-transform duration-1000 hidden lg:block pointer-events-none"
    >
      <BoltIcon class="w-64 h-64" />
    </div>
  </div>
</template>
