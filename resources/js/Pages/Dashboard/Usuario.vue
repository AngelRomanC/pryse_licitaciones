<script setup>
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import Pagination from '@/Shared/Pagination.vue';
import { Link } from '@inertiajs/vue3';


const props = defineProps({
    users: Number,
    documentos: Object,
    documentosLegal: Object,
    titulo: String,
    titulo2: String,
    latestUsers: Object,
});
// Contar total de documentos
const totalDocumentosTecnicos = computed(() => props.documentos.data.length);
const totalDocumentosLegales = computed(() => props.documentosLegal.data.length);

const documentosTecnicosVencidos = computed(() => {
    return props.documentos.data.filter(documento => documento.dias_restantes === 0).length;
});

const documentosLegalesVencidos = computed(() => {
    return props.documentosLegal.data.filter(documento => documento.dias_restantes === 0).length;
});

</script>

<template>

    <Head title="Dashboard Usuario" />
    <LayoutDashboard>
        <SectionMain>
            <SectionTitleLineWithButton title="Bienvenido al Dashboard de Usuario" main />
        </SectionMain>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Tarjeta de Documentos Técnicos -->
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                <h2 class="text-xl font-semibold text-gray-800">Documentos Técnicos</h2>
                <p class="text-4xl font-bold text-indigo-600">{{ totalDocumentosTecnicos }}</p>
            </div>

            <!-- Mostramos los conteos de documentos vencidos -->
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                <h2 class="text-xl font-semibold text-gray-800">Licitaciones Técnicos</h2>
                <p class="text-4xl font-bold text-indigo-600">{{ documentosTecnicosVencidos }}</p>
            </div>

            <!-- Tarjeta de Documentos Legales -->
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                <h2 class="text-xl font-semibold text-gray-800">Documentos Legales</h2>
                <p class="text-4xl font-bold text-indigo-600">{{ totalDocumentosLegales }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                <h2 class="text-xl font-semibold text-gray-800">Licitaciones Legales</h2>
                <p class="text-4xl font-bold text-indigo-600">{{ documentosLegalesVencidos }}</p>
            </div>
        </div>


        <!-- Secciones de Documentos -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <!-- Documentos Técnicos -->
  <div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
      <h2 class="text-xl font-semibold text-gray-800 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />
        </svg>
        {{ titulo }}
      </h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <!-- Cabecera de la tabla (igual que antes) -->
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revalidación</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
          </tr>
        </thead>
        <!-- Cuerpo de la tabla (igual que antes) -->
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="documento in documentos.data" :key="documento.id" class="hover:bg-gray-50 transition-colors duration-150">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ documento.tipo_de_documento.nombre_documento }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
              {{ documento.departamento.nombre_departamento }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
              {{ documento.fecha_revalidacion }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
              {{ documento.fecha_vigencia }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span 
                :class="{
                  'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                  'bg-green-100 text-green-800': documento.dias_restantes > 7
                }"
                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ documento.dias_restantes === 0 ? 'Vencido' : `${documento.dias_restantes} días` }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Paginación para Documentos Técnicos -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
      <div class="flex-1 flex justify-between sm:hidden">
        <Link 
          :href="documentos.prev_page_url" 
          :class="{'opacity-50 cursor-not-allowed': !documentos.prev_page_url}"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Anterior
        </Link>
        <Link 
          :href="documentos.next_page_url" 
          :class="{'opacity-50 cursor-not-allowed': !documentos.next_page_url}"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Siguiente
        </Link>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Mostrando
            <span class="font-medium">{{ documentos.from }}</span>
            a
            <span class="font-medium">{{ documentos.to }}</span>
            de
            <span class="font-medium">{{ documentos.total }}</span>
            resultados
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <Link 
              v-for="(link, index) in documentos.links" 
              :key="index"
              :href="link.url"
              :class="{
                'bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
                'opacity-50 cursor-not-allowed': !link.url
              }"
              class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              v-html="link.label"
            />
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Documentos Legales -->
  <div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-teal-50">
      <h2 class="text-xl font-semibold text-gray-800 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-teal-600" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
          <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
        </svg>
        {{ titulo2 }}
      </h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <!-- Cabecera de la tabla (igual que antes) -->
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revalidación</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
          </tr>
        </thead>
        <!-- Cuerpo de la tabla (igual que antes) -->
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="documento in documentosLegal.data" :key="documento.id" class="hover:bg-gray-50 transition-colors duration-150">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ documento.tipo_de_documento.nombre_documento }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
              {{ documento.departamento.nombre_departamento }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
              {{ documento.fecha_revalidacion }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
              {{ documento.fecha_vigencia }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span 
                :class="{
                  'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                  'bg-green-100 text-green-800': documento.dias_restantes > 7
                }"
                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ documento.dias_restantes === 0 ? 'Vencido' : `${documento.dias_restantes} días` }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Paginación para Documentos Legales -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
      <div class="flex-1 flex justify-between sm:hidden">
        <Link 
          :href="documentosLegal.prev_page_url" 
          :class="{'opacity-50 cursor-not-allowed': !documentosLegal.prev_page_url}"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Anterior
        </Link>
        <Link 
          :href="documentosLegal.next_page_url" 
          :class="{'opacity-50 cursor-not-allowed': !documentosLegal.next_page_url}"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Siguiente
        </Link>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Mostrando
            <span class="font-medium">{{ documentosLegal.from }}</span>
            a
            <span class="font-medium">{{ documentosLegal.to }}</span>
            de
            <span class="font-medium">{{ documentosLegal.total }}</span>
            resultados
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <Link 
              v-for="(link, index) in documentosLegal.links" 
              :key="index"
              :href="link.url"
              :class="{
                'bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
                'opacity-50 cursor-not-allowed': !link.url
              }"
              class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              v-html="link.label"
            />
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>


    </LayoutDashboard>
</template>
