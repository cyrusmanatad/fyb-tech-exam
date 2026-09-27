<script setup lang="ts">
import { FunnelIcon, XMarkIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'

// defineProps<{
//   isOpen: boolean
//   categories: string[]
//   selectedCategories: string[]
//   minPrice: number
//   maxPrice: number
//   inStockOnly: boolean
// }>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'update:selectedCategories', value: string[]): void
  (e: 'update:minPrice', value: number): void
  (e: 'update:maxPrice', value: number): void
  (e: 'update:inStockOnly', value: boolean): void
  (e: 'reset'): void
}>()

const toggleCategory = (cat: string) => {
  const newCategories = props.selectedCategories.includes(cat)
    ? props.selectedCategories.filter((c) => c !== cat)
    : [...props.selectedCategories, cat]
  emit('update:selectedCategories', newCategories)
}

// Accessing props in script setup
const props = defineProps<{
  isOpen: boolean
  categories: string[]
  selectedCategories: string[]
  minPrice: number
  maxPrice: number
  inStockOnly: boolean
}>()
</script>

<template>
  <div>
    <!-- MOBILE OVERLAY -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"
        @click="emit('close')"
      ></div>
    </Transition>

    <!-- SIDEBAR -->
    <aside
      :class="isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
      class="w-72 bg-white dark:bg-dark-card border-r border-gray-200 dark:border-dark-border flex flex-col fixed h-full z-50 transition-transform duration-300 ease-in-out"
    >
      <div class="p-6 flex items-center justify-between border-b dark:border-dark-border">
        <div class="flex items-center gap-2">
          <div
            class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center text-white shadow-lg shadow-teal-500/30"
          >
            <FunnelIcon class="w-5 h-5" />
          </div>
          <span class="text-xl font-bold text-gray-900 dark:text-white tracking-tight"
            >Filters</span
          >
        </div>
        <button
          type="button"
          class="lg:hidden p-1 text-gray-400 hover:text-gray-600 active:scale-95"
          @click="emit('close')"
        >
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-8">
        <!-- Categories -->
        <div>
          <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
            Categories
          </h3>
          <div class="space-y-2">
            <label
              v-for="cat in categories"
              :key="cat"
              class="flex items-center gap-3 group cursor-pointer"
            >
              <div class="relative flex items-center">
                <input
                  type="checkbox"
                  :checked="selectedCategories.includes(cat)"
                  class="peer h-5 w-5 appearance-none rounded-md border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 checked:bg-teal-500 checked:border-teal-500 transition-all cursor-pointer"
                  @change="toggleCategory(cat)"
                />
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="4"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <span
                class="text-sm font-medium text-gray-600 dark:text-slate-400 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition"
              >
                {{ cat }}
              </span>
            </label>
          </div>
        </div>

        <!-- Price Range -->
        <div>
          <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
            Price Range
          </h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between gap-4">
              <div class="flex-1">
                <label class="text-[10px] text-gray-400 font-bold block mb-1">Min</label>
                <input
                  type="number"
                  :value="minPrice"
                  class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl text-xs font-bold focus:ring-2 focus:ring-teal-500/20 outline-none"
                  @input="
                    emit('update:minPrice', Number(($event.target as HTMLInputElement).value))
                  "
                />
              </div>
              <div class="flex-1">
                <label class="text-[10px] text-gray-400 font-bold block mb-1">Max</label>
                <input
                  type="number"
                  :value="maxPrice"
                  class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl text-xs font-bold focus:ring-2 focus:ring-teal-500/20 outline-none"
                  @input="
                    emit('update:maxPrice', Number(($event.target as HTMLInputElement).value))
                  "
                />
              </div>
            </div>
            <input
              type="range"
              min="0"
              max="15000"
              step="100"
              :value="maxPrice"
              class="w-full h-1.5 bg-gray-200 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer accent-teal-500"
              @input="emit('update:maxPrice', Number(($event.target as HTMLInputElement).value))"
            />
          </div>
        </div>

        <!-- Availability -->
        <div>
          <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
            Availability
          </h3>
          <label class="flex items-center justify-between group cursor-pointer">
            <span class="text-sm font-medium text-gray-600 dark:text-slate-400">In Stock Only</span>
            <div class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                :checked="inStockOnly"
                class="sr-only peer"
                @change="emit('update:inStockOnly', ($event.target as HTMLInputElement).checked)"
              />
              <div
                class="w-11 h-6 bg-gray-200 dark:bg-slate-800 rounded-full peer peer-focus-visible:ring-2 peer-focus-visible:ring-teal-700 peer-focus-visible:ring-offset-2 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"
              ></div>
            </div>
          </label>
        </div>
      </div>

      <div class="p-6 border-t dark:border-dark-border">
        <button
          type="button"
          class="w-full flex items-center justify-center gap-2 px-4 py-3 text-gray-500 hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 border border-gray-200 dark:border-dark-border hover:border-red-500 dark:hover:border-red-400 rounded-2xl text-sm font-bold transition active:scale-95"
          @click="emit('reset')"
        >
          <ArrowPathIcon class="w-4 h-4" /> Reset Filters
        </button>
      </div>
    </aside>
  </div>
</template>
