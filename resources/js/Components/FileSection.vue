<template>
  <div class="mb-4">
    <h5 class="text-sm font-medium text-gray-700 mb-2">{{ title }}</h5>
    <div class="flex flex-wrap gap-2">
      <button 
        v-for="(file, index) in files" 
        :key="file.id"
        @click="$emit('view-file', file.ruta_archivo)"
        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        :class="buttonClass"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        {{ title.split(' ')[0] }} {{ files.length > 1 ? index + 1 : '' }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  files: Array,
  buttonClass: String
});

defineEmits(['view-file']);
</script>