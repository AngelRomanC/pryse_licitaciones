<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch } from "vue";
import { useMainStore } from "@/stores/main";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import { Chart as ChartJS, CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement } from 'chart.js';
import Pagination from '@/Shared/Pagination.vue';
import moment from "moment";
import UsersCard from '@/Components/UsersCard.vue';
import TechnicalDocumentsCard from '@/Components/TechnicalDocumentsCard.vue';
import LegalDocumentsCard from '@/Components/LegalDocumentsCard.vue';


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

</script>
<template>

  <Head title="Dashboard Admin" />
  <LayoutDashboard>
    <SectionMain>
      <SectionTitleLineWithButton title="Bienvenido al Dashboard de Administrador" main class="mb-8" />
    </SectionMain>

    <!-- Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
      <!-- Usuarios Registrados -->
        <UsersCard 
         :latest-users="latestUsers" 
         :users="users"         
         title="Detalles de usuarios registrados"
        />   
      

      <!-- Documentos Técnicos -->
      <TechnicalDocumentsCard 
      :count="totalDocumentosTecnicos" 
      tooltip="Haz clic para crear un nuevo documento técnico"
      />

      <LegalDocumentsCard 
      :count="totalDocumentosLegales" 
      tooltip="Haz clic para crear un nuevo documento legal"
    />


    </div>



    <!-- Documentos Técnicos y Legales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Documentos Técnicos -->
      <CardBox>
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
          <h2 class="text-xl font-semibold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20"
              fill="currentColor">
              <path fill-rule="evenodd"
                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z"
                clip-rule="evenodd" />
            </svg>
            {{ titulo }}
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Documento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Departamento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Revalidación</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Vigencia</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="documento in documentos.data" :key="documento.id"
                class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-normal text-sm font-medium text-gray-900" data-label="Documento">
                  {{ documento.tipo_de_documento.nombre_documento }}
                </td>
                <td class="px-6 py-4 whitespace-normal text-sm text-gray-500" data-label="Departamento">
                  {{ documento.departamento.nombre_departamento }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Fecha Revalidación">
                  <div class="flex flex-col">
                    <div>{{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}</div>
                    <span :class="{
                      'bg-red-500 text-white': documento.dias_restantes_revalidacion <= 0,
                      'bg-red-200 text-red-800': documento.dias_restantes_revalidacion > 0 && documento.dias_restantes_revalidacion <= 7,
                      'bg-green-100 text-green-800': documento.dias_restantes_revalidacion > 7
                    }" class="px-3 py-1 inline-flex justify-center text-xs leading-5 font-semibold rounded-full">
                      {{ documento.dias_restantes_revalidacion <= 0 ? 'Vencido' :
                        `${documento.dias_restantes_revalidacion} días` }} </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Fecha Vigencia">
                  <div class="flex flex-col">
                    <div>{{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}</div>
                    <span :class="{
                      'bg-red-500 text-white': documento.dias_restantes <= 0,
                      'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                      'bg-green-100 text-green-800': documento.dias_restantes > 7
                    }" class="px-3 py-1 inline-flex justify-center text-xs leading-5 font-semibold rounded-full">
                      {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }} </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="{
                    'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                    'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                    'bg-green-100 text-green-800': documento.dias_restantes > 7
                  }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }} </span>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-teal-600" viewBox="0 0 20 20"
              fill="currentColor">
              <path fill-rule="evenodd"
                d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                clip-rule="evenodd" />
              <path
                d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
            </svg>
            {{ titulo2 }}
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Documento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Departamento</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Revalidación</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Vigencia</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="documento in documentosLegal.data" :key="documento.id"
                class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-normal text-sm font-medium text-gray-900" data-label="Documento">
                  {{ documento.tipo_de_documento.nombre_documento }}
                </td>
                <td class="px-6 py-4 whitespace-normal text-sm text-gray-500" data-label="Departamento">
                  {{ documento.departamento.nombre_departamento }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Fecha Revalidación">
                  <div class="flex flex-col">
                    <div>{{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}</div>
                    <span :class="{
                      'bg-red-500 text-white': documento.dias_restantes_revalidacion <= 0,
                      'bg-red-200 text-red-800': documento.dias_restantes_revalidacion > 0 && documento.dias_restantes_revalidacion <= 7,
                      'bg-green-100 text-green-800': documento.dias_restantes_revalidacion > 7
                    }" class="px-3 py-1 inline-flex justify-center text-xs leading-5 font-semibold rounded-full">
                      {{ documento.dias_restantes_revalidacion <= 0 ? 'Vencido' :
                        `${documento.dias_restantes_revalidacion} días` }} </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Fecha Vigencia">
                  <div class="flex flex-col">
                    <div>{{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}</div>
                    <span :class="{
                      'bg-red-500 text-white': documento.dias_restantes <= 0,
                      'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                      'bg-green-100 text-green-800': documento.dias_restantes > 7
                    }" class="px-3 py-1 inline-flex justify-center text-xs leading-5 font-semibold rounded-full">
                      {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }} </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="{
                    'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                    'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                    'bg-green-100 text-green-800': documento.dias_restantes > 7
                  }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }} </span>
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