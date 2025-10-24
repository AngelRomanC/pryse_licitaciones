<template>
  <CardBox 
    class="hover:shadow-lg transition-shadow duration-300 cursor-pointer group"
    @click="redirectToCreate"
    :title="tooltip"
  >
    <div class="flex items-center">
      <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4 group-hover:bg-green-200 transition-colors">
        <!-- Ícono de handshake -->
        <svg
          xmlns="http://www.w3.org/2000/svg"
          :width="24"
          :height="24"
          viewBox="0 0 24 24"
          fill="currentColor"
        >
          <path :d="mdiHandshake" />
        </svg>
      </div>
      <div>
        <h3 class="text-sm font-medium text-gray-500">Licitaciones</h3>
        <p class="text-2xl font-bold text-green-600">{{ count }}</p>
      </div>
    </div>
  </CardBox>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import CardBox from "@/components/CardBox.vue"
import { mdiHandshake } from "@mdi/js"  // 👈 importamos el icono

const props = defineProps({
  count: {
    type: Number,
    required: true
  },
  tooltip: {
    type: String,
    default: 'Crear nueva Licitación'
  }
})

const redirectToCreate = () => {
  router.get(route('licitacion.create', {
    redirect: route('dashboard')
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
