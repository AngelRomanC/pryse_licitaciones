<!-- components/MultiSelectEstados.vue -->
<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
  estados: {
    type: Array,
    required: true
  },
  modelValue: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue']);
const isOpen = ref(false);
const searchTerm = ref('');

const filteredEstados = computed(() => {
  return props.estados.filter(estado => 
    estado.name.toLowerCase().includes(searchTerm.value.toLowerCase())
  );
});

const toggleEstado = (estadoId) => {
  const newValue = [...props.modelValue];
  const index = newValue.indexOf(estadoId);
  
  if (index === -1) {
    newValue.push(estadoId);
  } else {
    newValue.splice(index, 1);
  }
  
  emit('update:modelValue', newValue);
};

const selectedEstadosNames = computed(() => {
  return props.estados
    .filter(estado => props.modelValue.includes(estado.id))
    .map(estado => estado.name)
    .join(', ');
});
</script>

<template>
  <div class="relative">
    <label class="block text-sm font-medium text-gray-700 mb-1">Estados</label>
    <div 
      @click="isOpen = !isOpen"
      class="cursor-pointer w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm"
    >
      <span class="block truncate">
        {{ selectedEstadosNames || 'Seleccione estados...' }}
      </span>
    </div>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div 
        v-show="isOpen"
        class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg"
      >
        <div class="p-2 border-b">
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar estado..."
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div class="max-h-60 overflow-y-auto">
          <div
            v-for="estado in filteredEstados"
            :key="estado.id"
            @click.stop="toggleEstado(estado.id)"
            class="cursor-pointer hover:bg-indigo-50 p-2 flex items-center"
          >
            <input
              type="checkbox"
              :checked="modelValue.includes(estado.id)"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              readonly
            />
            <span class="ml-2">{{ estado.name }}</span>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>