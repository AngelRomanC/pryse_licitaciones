<template>
  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
    <div class="flex items-center space-x-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
      </svg>
      <div>
        <p class="text-sm font-medium text-gray-500">{{ title }}</p>
        <p class="text-gray-800 font-medium">{{ value }}</p>
        <span v-if="status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1" :class="status.class">
          {{ status.text }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  icon: String,
  title: String,
  value: String,
  status: Object
});
</script>