<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useProductStore } from '@/stores/products'
import { useCartStore } from '@/stores/cart'
import { useToastStore } from '@/stores/toast'
import FilterSidebar from '@/components/order-entry/FilterSidebar.vue'
import OrderHeader from '@/components/order-entry/OrderHeader.vue'
import HeroBanner from '@/components/order-entry/HeroBanner.vue'
import ProductCard from '@/components/order-entry/ProductCard.vue'
import CartSlideOver from '@/components/order-entry/CartSlideOver.vue'
import QuickViewModal from '@/components/order-entry/QuickViewModal.vue'
import ToastNotifications from '@/components/order-entry/ToastNotifications.vue'
import LogoutModal from '@/components/product/modals/LogoutModal.vue'
import type { Product } from '@/types/data-types'
import { rand } from '@vueuse/core'

const productStore = useProductStore()
const cartStore = useCartStore()
const toastStore = useToastStore()

// State
const isSidebarOpen = ref(false)
const isQuickViewOpen = ref(false)
const selectedProduct = ref<Product | null>(null)
const isLogoutModalOpen = ref(false)

// Filters
const searchQuery = ref('')
const sortBy = ref('default')
const minPrice = ref(0)
const maxPrice = ref(15000)
const selectedCategories = ref<string[]>([])
const inStockOnly = ref(false)

// Computed
const filteredProducts = computed(() => {
  let result = [...productStore.products]

  // Search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(
      (p) =>
        p.title.toLowerCase().includes(query) ||
        p.category.toLowerCase().includes(query) ||
        p.description?.toLowerCase().includes(query),
    )
  }

  // Categories
  if (selectedCategories.value.length > 0) {
    result = result.filter((p) => selectedCategories.value.includes(p.category))
  }

  // Price
  result = result.filter((p) => p.price >= minPrice.value && p.price <= maxPrice.value)

  // Stock
  if (inStockOnly.value) {
    result = result.filter((p) => p.stock > 0)
  }

  // Sort
  if (sortBy.value === 'price-low') result.sort((a, b) => a.price - b.price)
  if (sortBy.value === 'price-high') result.sort((a, b) => b.price - a.price)
  if (sortBy.value === 'title-az') result.sort((a, b) => a.title.localeCompare(b.title))

  return result
})

const categoriesList = computed(() => {
  return productStore.categories.map((c) => c.name)
})

// Actions
const handleAddToCart = (product: Product) => {
  cartStore.addToCart(product)
  toastStore.addToast(`Added ${product.title} to cart!`)
}

const handleQuickView = (product: Product) => {
  selectedProduct.value = product
  isQuickViewOpen.value = true
}

const resetFilters = () => {
  searchQuery.value = ''
  sortBy.value = 'default'
  minPrice.value = 0
  maxPrice.value = 15000
  selectedCategories.value = []
  inStockOnly.value = false
}

const handleLogout = () => {
  productStore.toggleModal('logout', true)
}

onMounted(async () => {
  await productStore.fetchCategories()
  await productStore.fetchProducts()
})
</script>

<template>
  <div class="min-h-screen bg-[#F9FAFB] dark:bg-dark-bg text-gray-800 dark:text-slate-200">
    <FilterSidebar
      :is-open="isSidebarOpen"
      :categories="categoriesList"
      v-model:selected-categories="selectedCategories"
      v-model:min-price="minPrice"
      v-model:max-price="maxPrice"
      v-model:in-stock-only="inStockOnly"
      @close="isSidebarOpen = false"
      @reset="resetFilters"
    />

    <main class="lg:ml-72 min-h-screen transition-all duration-300">
      <OrderHeader
        v-model:search-query="searchQuery"
        v-model:sort-by="sortBy"
        @open-sidebar="isSidebarOpen = true"
        @logout="handleLogout"
      />

      <div class="p-4 lg:p-8">
        <HeroBanner
          title="Summer Tech Sale <br>Up to 50% Off"
          subtitle="Limited Time Offer"
          description="Upgrade your digital lifestyle with our premium electronics and accessories. Get free shipping on all orders over &#8369 500."
          button-text="Shop the Sale"
          @action="searchQuery = 'Electronics'"
        />

        <!-- RESULTS INFO -->
        <div class="flex items-center justify-between mb-8">
          <p class="text-sm font-medium text-gray-500 dark:text-slate-400">
            Showing
            <span class="font-black text-gray-900 dark:text-white">{{
              filteredProducts.length
            }}</span>
            Products
          </p>
          <!-- Mobile Sort -->
          <div class="md:hidden relative">
            <select
              v-model="sortBy"
              class="appearance-none pl-3 pr-8 py-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-teal-500/10 outline-none"
            >
              <option value="default">Default</option>
              <option value="price-low">Price ↑</option>
              <option value="price-high">Price ↓</option>
              <option value="title-az">A-Z</option>
            </select>
          </div>
        </div>

        <!-- PRODUCT GRID -->
        <div
          v-if="filteredProducts.length > 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6"
        >
          <ProductCard
            v-for="product in filteredProducts"
            :key="product.id"
            :product="product"
            :rating="rand(1, 5)"
            @add-to-cart="handleAddToCart"
            @quick-view="handleQuickView"
          />
        </div>

        <!-- EMPTY STATE -->
        <div v-else class="py-32 flex flex-col items-center justify-center text-center">
          <div
            class="w-24 h-24 bg-gray-100 dark:bg-slate-900 rounded-[2.5rem] flex items-center justify-center text-gray-300 mb-6 shadow-inner"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="w-12 h-12"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
          <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">
            Oops! No items found
          </h3>
          <p class="text-gray-500 dark:text-slate-400 max-w-xs mx-auto mb-8">
            We couldn't find any products matching your current filters. Try resetting them!
          </p>
          <button
            type="button"
            class="px-8 py-3 bg-teal-500 text-white rounded-2xl font-black text-sm shadow-xl shadow-teal-500/20 hover:bg-teal-600 transition active:scale-95"
            @click="resetFilters"
          >
            Clear Filters
          </button>
        </div>
      </div>
    </main>

    <CartSlideOver />

    <QuickViewModal
      :show="isQuickViewOpen"
      :product="selectedProduct"
      @close="isQuickViewOpen = false"
      @add-to-cart="handleAddToCart"
    />

    <LogoutModal />

    <ToastNotifications />
  </div>
</template>
