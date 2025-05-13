<template>
  <div>
    <!-- Botón que activa el modal (puede ser usado en tablas) -->
    <BaseButton 
      @click="openModal" 
      :icon="mdiEye" 
      color="lightDark"
      class="!p-1.5 !rounded-full hover:bg-gray-200 transition-colors"
      title="Ver detalles del documento"
    />

    <!-- Modal de detalles -->
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Fondo oscuro -->
        <div class="fixed inset-0 transition-opacity" @click="closeModal">
          <div class="absolute inset-0 bg-black opacity-50"></div>
        </div>

        <!-- Contenido del modal -->
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <!-- Header -->
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
            <div class="flex justify-between items-start">
              <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-full flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-800">{{ document.tipo_de_documento.nombre_documento }}</h3>
                  <p class="text-sm text-gray-500">{{ document.departamento.nombre_departamento }}</p>
                </div>
              </div>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Cuerpo del modal -->
          <div class="px-6 py-4 space-y-6">
            <!-- Tarjetas de información -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InfoCard 
                icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                title="Revalidación"
                :value="formatDate(document.fecha_revalidacion)"
                :status="getStatus(document.dias_restantes_revalidacion)"
              />
              
              <InfoCard 
                icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                title="Vigencia"
                :value="formatDate(document.fecha_vigencia)"
                :status="getStatus(document.dias_restantes)"
              />
            </div>

            <!-- Archivos -->
            <div class="border-t border-gray-200 pt-4">
              <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Visualizar documentos</h4>

              <!-- Documentos Principales -->
              <FileSection 
                v-if="principalFiles.length > 0"
                title="Documentos Principales"
                :files="principalFiles"
                button-class="bg-blue-600 hover:bg-blue-700 text-white"
                @view-file="viewFile"
              />

              <!-- Documentos Anexos -->
              <FileSection 
                v-if="anexoFiles.length > 0"
                title="Documentos Anexos"
                :files="anexoFiles"
                button-class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300"
                @view-file="viewFile"
              />
            </div>

            <!-- Visor de PDF -->
            <PdfViewer 
              v-if="currentFile"
              :file-url="currentFile"
              @close="closePdfViewer"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { mdiEye } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import InfoCard from '@/Components/InfoCard.vue';
import FileSection from '@/Components/FileSection.vue';
import PdfViewer from '@/Components/PdfViewer.vue';

const props = defineProps({
  document: {
    type: Object,
    required: true
  }
});

const isOpen = ref(false);
const currentFile = ref(null);

const principalFiles = computed(() => 
  props.document.archivos?.filter(a => a.tipo === 'principal') || []
);

const anexoFiles = computed(() => 
  props.document.archivos?.filter(a => a.tipo === 'anexo') || []
);

const openModal = () => {
  isOpen.value = true;
};

const closeModal = () => {
  isOpen.value = false;
  currentFile.value = null;
};

const viewFile = (filePath) => {
  currentFile.value = `/storage/${filePath}`;
};

const closePdfViewer = () => {
  currentFile.value = null;
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-MX');
};

const getStatus = (days) => {
  if (days <= 0) return { text: 'Vencido', class: 'bg-red-500 text-white' };
  if (days <= 5) return { text: `${days} días restantes`, class: 'bg-red-300 text-red-800' };
  if (days <= 7) return { text: `${days} días restantes`, class: 'bg-red-100 text-red-800' };
  return { text: `${days} días restantes`, class: 'bg-green-100 text-green-800' };
};
</script>
