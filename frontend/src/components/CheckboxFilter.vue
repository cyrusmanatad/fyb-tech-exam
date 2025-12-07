<script setup>

const props = defineProps({
  label: String,
  tooltip: {
    type: Boolean,
    default: false,
  },
});

</script>

<template>
  <div class="flex items-center">
    <input
      :id="label"
      :name="label"
      type="checkbox"
      class="h-4 w-4 text-blue-600 border-gray-200 rounded focus:ring-blue-500 bg-white"
    />
    <label :for="label" class="ml-3 text-sm text-gray-600"
      >{{label}}</label
    >
    <!-- <InfoIcon v-if="tooltip" class="ml-1.5 w-4 h-4 text-gray-400" /> -->
  </div>
</template>
