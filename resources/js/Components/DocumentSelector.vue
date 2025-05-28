<template>
  <div class="document-selector">
    <!-- Documentos Técnicos -->
    <div class="document-section">
      <h3 class="section-title">Documentos Técnicos</h3>
      <div class="document-list">
        <div 
          v-for="doc in groupedTecnicos" 
          :key="'tec-'+doc.tipo"
          class="document-group"
        >
          <h4 class="document-type">{{ doc.tipo }}</h4>
          <div class="document-items">
            <label 
              v-for="archivo in doc.archivos" 
              :key="'tecnico-'+archivo.id"
              class="document-item"
            >
              <input
                type="checkbox"
                :value="archivo.id"
                v-model="selectedTecnicos"
                class="document-checkbox"
              >
              <span class="document-name">{{ archivo.nombre }}</span>
              <button 
                @click.stop="openModal(archivo.url)"
                class="document-preview"
                type="button"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Documentos Legales -->
    <div class="document-section">
      <h3 class="section-title">Documentos Legales</h3>
      <div class="document-list">
        <div 
          v-for="doc in groupedLegales" 
          :key="'leg-'+doc.tipo"
          class="document-group"
        >
          <h4 class="document-type">{{ doc.tipo }}</h4>
          <div class="document-items">
            <label 
              v-for="archivo in doc.archivos" 
              :key="'legal-'+archivo.id"
              class="document-item"
            >
              <input
                type="checkbox"
                :value="archivo.id"
                v-model="selectedLegales"
                class="document-checkbox"
              >
              <span class="document-name">{{ archivo.nombre }}</span>
              <button 
                @click.stop="openModal(archivo.url)"
                class="document-preview"
                type="button"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para visualización de documentos -->
    <div v-if="showModal" class="document-modal">
      <div class="modal-overlay" @click="closeModal"></div>
      <div class="modal-content">
        <div class="modal-header">
          <h3>Visualizador de Documento</h3>
          <button @click="closeModal" class="modal-close">
            &times;
          </button>
        </div>
        <div class="modal-body">
          <iframe 
            v-if="currentDocument"
            :src="getDocumentUrl(currentDocument)"
            frameborder="0"
            class="document-iframe"
          ></iframe>
          <div v-else class="document-error">
            No se puede visualizar el documento
          </div>
        </div>
        <div class="modal-footer">
          <a 
            :href="getDocumentUrl(currentDocument)"
            download
            class="download-button"
          >
            Descargar Documento
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  documentosTecnicos: {
    type: Array,
    default: () => []
  },
  documentosLegales: {
    type: Array,
    default: () => []
  },
  modelValueTecnicos: {
    type: Array,
    default: () => []
  },
  modelValueLegales: {
    type: Array,
    default: () => []
  },
  baseUrl: {
    type: String,
    default: '/storage/' // Ruta base donde se almacenan los documentos
  }
});

const emit = defineEmits([
  'update:modelValueTecnicos',
  'update:modelValueLegales'
]);

// Estado del modal
const showModal = ref(false);
const currentDocument = ref(null);

// Agrupar documentos por tipo (igual que antes)
const groupedTecnicos = computed(() => {
  const groups = {};
  props.documentosTecnicos.forEach(archivo => {
    if (!groups[archivo.documento]) {
      groups[archivo.documento] = [];
    }
    groups[archivo.documento].push(archivo);
  });
  return Object.keys(groups).map(tipo => ({
    tipo,
    archivos: groups[tipo]
  }));
});

const groupedLegales = computed(() => {
  const groups = {};
  props.documentosLegales.forEach(archivo => {
    if (!groups[archivo.documento]) {
      groups[archivo.documento] = [];
    }
    groups[archivo.documento].push(archivo);
  });
  return Object.keys(groups).map(tipo => ({
    tipo,
    archivos: groups[tipo]
  }));
});

// Modelos para los checkboxes (igual que antes)
const selectedTecnicos = computed({
  get: () => props.modelValueTecnicos,
  set: (value) => emit('update:modelValueTecnicos', value)
});

const selectedLegales = computed({
  get: () => props.modelValueLegales,
  set: (value) => emit('update:modelValueLegales', value)
});

// Métodos para el modal
const openModal = (documentUrl) => {
  currentDocument.value = documentUrl;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  currentDocument.value = null;
};

const getDocumentUrl = (path) => {
  return props.baseUrl + path;
};
</script>

<style scoped>
/* Estilos anteriores del document-selector */
.document-selector {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-top: 1.5rem;
}

.document-section {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  background-color: #f9fafb;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #111827;
}

.document-group {
  margin-bottom: 1.5rem;
}

.document-type {
  font-size: 0.875rem;
  font-weight: 500;
  color: #4b5563;
  margin-bottom: 0.5rem;
  padding-bottom: 0.25rem;
  border-bottom: 1px dashed #d1d5db;
}

.document-items {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.document-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.document-item:hover {
  background-color: #e5e7eb;
}

.document-checkbox {
  width: 1rem;
  height: 1rem;
  border-color: #d1d5db;
}

.document-name {
  flex-grow: 1;
  font-size: 0.875rem;
  color: #374151;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.document-preview {
  color: #3b82f6;
  display: flex;
  align-items: center;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.document-preview:hover {
  color: #2563eb;
}

/* Estilos del modal */
.document-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  position: relative;
  background-color: white;
  border-radius: 0.5rem;
  width: 80%;
  max-width: 900px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  z-index: 10;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
}

.modal-close:hover {
  color: #111827;
}

.modal-body {
  flex: 1;
  padding: 1rem;
  overflow: auto;
}

.document-iframe {
  width: 100%;
  height: 65vh;
  min-height: 400px;
}

.document-error {
  padding: 2rem;
  text-align: center;
  color: #ef4444;
}

.modal-footer {
  padding: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
}

.download-button {
  background-color: #3b82f6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-weight: 500;
}

.download-button:hover {
  background-color: #2563eb;
}
</style>