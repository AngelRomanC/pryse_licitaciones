<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch } from "vue";
import { useMainStore } from "@/stores/main";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import LineChart from "@/Components/LineChart.vue";
import { Chart as ChartJS, CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement } from 'chart.js';
import Pagination from '@/Shared/Pagination.vue';
import moment from "moment";



// Registrar los componentes necesarios de Chart.js
ChartJS.register(CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement);

const props = defineProps({
  users: Number,
  documentos: Object,
  documentosLegal: Object,
  titulo: String,
  titulo2: String,
  latestUsers: Object,
});


// Contar total de documentos
const totalDocumentosTecnicos = computed(() => props.documentos.total);
const totalDocumentosLegales = computed(() => props.documentosLegal.total);

const documentosTecnicosVencidos = computed(() => {
  return props.documentos.data.filter(documento => documento.dias_restantes <= 0).length;
});

const documentosLegalesVencidos = computed(() => {
  return props.documentosLegal.data.filter(documentoLegal => documentoLegal.dias_restantes <= 0).length;
});

</script>

<template>
  <Head title="Dashboard Admin" />
  <LayoutDashboard>
    <SectionMain>
      <SectionTitleLineWithButton 
        title="Bienvenido al Dashboard de Administrador" 
        main 
        class="mb-8"
      />
    </SectionMain>

    <!-- Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
      <!-- Usuarios Registrados -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Usuarios Registrados</h3>
            <p class="text-2xl font-bold text-purple-600">{{ users }}</p>
          </div>
        </div>
      </CardBox>

      <!-- Documentos Técnicos -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Documentos Técnicos</h3>
            <p class="text-2xl font-bold text-indigo-600">{{ totalDocumentosTecnicos }}</p>
          </div>
        </div>
      </CardBox>

      <!-- Licitaciones Técnicas -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Licitaciones Técnicas</h3>
            <p class="text-2xl font-bold text-red-600">{{ documentosTecnicosVencidos }}</p>
            <p class="text-xs text-gray-500 mt-1" v-if="documentosTecnicosVencidos > 0">¡Requieren atención!</p>
          </div>
        </div>
      </CardBox>

      <!-- Documentos Legales -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Documentos Legales</h3>
            <p class="text-2xl font-bold text-green-600">{{ totalDocumentosLegales }}</p>
          </div>
        </div>
      </CardBox>

      <!-- Licitaciones Legales -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Licitaciones Legales</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ documentosLegalesVencidos }}</p>
            <p class="text-xs text-gray-500 mt-1" v-if="documentosLegalesVencidos > 0">Revisión necesaria</p>
          </div>
        </div>
      </CardBox>
    </div>

    <!-- Últimos usuarios registrados -->
    <CardBox class="mb-8">
      <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
          </svg>
          Últimos usuarios registrados
        </h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo Electrónico</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Registro</th>

            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in latestUsers" :key="user.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.role }}</td>

              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ new Date(user.created_at).toLocaleString('es-MX', { hour12: true }) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>

    <!-- Documentos Técnicos y Legales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Documentos Técnicos -->
      <CardBox>
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
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revalidación</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="documento in documentos.data" :key="documento.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ documento.tipo_de_documento.nombre_documento }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ documento.departamento.nombre_departamento }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}

                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="{
                    'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                    'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                    'bg-green-100 text-green-800': documento.dias_restantes > 7
                  }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>          
        </div>
        <Pagination :currentPage="documentos.current_page" :links="documentos.links"
            :total="documentos.links.length - 2" />
      </CardBox>

      <!-- Documentos Legales -->
      <CardBox>
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
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revalidación</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="documento in documentosLegal.data" :key="documento.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ documento.tipo_de_documento.nombre_documento }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ documento.departamento.nombre_departamento }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}

                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="{
                    'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                    'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                    'bg-green-100 text-green-800': documento.dias_restantes > 7
                  }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <Pagination :currentPage="documentosLegal.current_page" :links="documentosLegal.links"
            :total="documentosLegal.links.length - 2" />
      </CardBox>
    </div>
  </LayoutDashboard>
</template>