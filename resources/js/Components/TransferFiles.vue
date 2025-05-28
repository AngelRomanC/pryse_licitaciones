<template>
  <div class="space-y-4">
    <div v-for="(archivos, documento) in agrupados" :key="documento" class="border rounded-lg p-4">
      <h3 class="font-bold text-lg mb-2">{{ documento }}</h3>
      <ul class="space-y-2">
        <li v-for="archivo in archivos" :key="archivo.id" class="flex items-center justify-between gap-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              type="checkbox"
              :value="archivo.id"
              v-model="selected"
            />
            <span>{{ archivo.nombre }}</span>
          </label>
          <button @click="verPDF(archivo.url)" class="text-blue-600 hover:underline text-sm">
            Ver PDF
          </button>
        </li>
      </ul>
    </div>

    <!-- Modal de vista previa PDF -->
    <div v-if="pdfModalUrl" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-2xl h-[80vh] rounded shadow-lg relative">
        <iframe :src="pdfModalUrl" class="w-full h-full" />
        <button @click="pdfModalUrl = null" class="absolute top-2 right-2 text-red-600 font-bold text-xl">×</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, watch, ref, defineEmits } from 'vue'

const props = defineProps({
  archivos: {
    type: Array,
    required: true
  },
  modelValue: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])

const selected = ref([...props.modelValue])

watch(selected, (val) => {
  emit('update:modelValue', val)
})

watch(() => props.modelValue, (val) => {
  selected.value = [...val]
})

const agrupados = computed(() => {
  const grupos = {}
  props.archivos.forEach(archivo => {
    if (!grupos[archivo.documento]) {
      grupos[archivo.documento] = []
    }
    grupos[archivo.documento].push(archivo)
  })
  return grupos
})

const pdfModalUrl = ref(null)

// function verPDF(url) {
//   pdfModalUrl.value = url
// }
function verPDF(ruta_archivo) {
  pdfModalUrl.value = `/storage/${ruta_archivo}`
}

</script>

<style scoped>
/* Puedes mejorar este estilo para adaptarlo a tu diseño */
</style>
