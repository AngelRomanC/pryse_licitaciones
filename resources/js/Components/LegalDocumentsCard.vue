<template>
  <CardBox 
    class="hover:shadow-lg transition-shadow duration-300 cursor-pointer group"
    @click="redirectToCreate"
    :title="tooltip"
  >
    <div class="flex items-center">
      <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4 group-hover:bg-green-200 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div>
        <!-- 🔹 label dinámico con valor por defecto -->
        <h3 class="text-sm font-medium text-gray-500">{{ label }}</h3>
        <p class="text-2xl font-bold text-green-600">{{ count }}</p>
      </div>
    </div>
  </CardBox>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import CardBox from "@/Components/CardBox.vue"

const props = defineProps({
  count: {
    type: Number,
    required: true,
  },
  tooltip: {
    type: String,
    default: 'Crear nuevo documento legal',
  },
  // 🔹 nuevo prop label con valor por defecto
  label: {
    type: String,
    default: 'Documentos Legales',
  },
})

const redirectToCreate = () => {
  router.get(route('documento-legal.create', {
    redirect: route('dashboard'),
  }))
}
</script>

<style scoped>
.card-hover-effect {
  transition: all 0.3s ease;
}
.card-hover-effect:hover {
  transform: translateY(-2px);
}
</style>
