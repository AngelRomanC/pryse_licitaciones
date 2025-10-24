<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import PaginationDashboard from '@/Shared/PaginationDashboard.vue';
import moment from "moment";
import UsersCard from '@/Components/UsersCard.vue';
import TechnicalDocumentsCard from '@/Components/TechnicalDocumentsCard.vue';
import LegalDocumentsCard from '@/Components/LegalDocumentsCard.vue';
import NotificationBar from "@/Components/NotificationBar.vue";
import DocumentDetailsModal from "@/Components/DocumentDetailsModal.vue";
import LicitacionDocumentsCard from '@/Components/LicitacionDocumentsCard.vue';
import { mdiInformation, mdiFileDocumentOutline, mdiArrowRight, mdiCheckCircle, mdiAlert } from "@mdi/js";
import CatalogoRedirectButton from '@/Components/CatalogoRedirectButton.vue';
import BaseIcon from "@/Components/BaseIcon.vue";

const props = defineProps({
  users: Number,
  documentos: Object,
  documentosLegal: Object,
  titulo: String,
  titulo2: String,
  latestUsers: Object,
  licitaciones: Number,
  documentosVencidos: Object,
  documentosLegalVencidos: Object,
});

// Estado para controlar la sección activa
const activeSection = ref('vigentes'); // 'vigentes' o 'vencidos'

// Contar total de documentos
const totalDocumentosTecnicos = computed(() => props.documentos.total + (props.documentosVencidos?.total || 0));
const totalDocumentosLegales = computed(() => props.documentosLegal.total + (props.documentosLegalVencidos?.total || 0));

// Métricas para las cards de navegación
const documentosVigentesCount = computed(() => {
  return (props.documentos.data?.length || 0) + (props.documentosLegal.data?.length || 0);
});

const documentosVencidosCount = computed(() => {
  return (props.documentosVencidos?.data?.length || 0) + (props.documentosLegalVencidos?.data?.length || 0);
});
</script>

<template>

  <Head title="Dashboard Admin" />
  <LayoutDashboard>
    <SectionMain>
      <SectionTitleLineWithButton title="Bienvenido al Dashboard de Administrador" main class="mb-8"
        :icon="mdiFileDocumentOutline" />
    </SectionMain>

    <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
      {{ $page.props.flash.success }}
    </NotificationBar>

    <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
      {{ $page.props.flash.error }}
    </NotificationBar>

    <!-- Estadísticas Principales CON NAVEGACIÓN -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
      <UsersCard :latest-users="latestUsers" :users="users" title="Detalles de usuarios registrados" />

      <TechnicalDocumentsCard :count="totalDocumentosTecnicos" label="Total de Documento Técnicos"
        tooltip="Haz clic para crear un nuevo documento técnico" />

      <LegalDocumentsCard :count="totalDocumentosLegales" label="Total de Documento Legales"
        tooltip="Haz clic para crear un nuevo documento legal" />

      <LicitacionDocumentsCard :count="licitaciones" tooltip="Haz clic para crear una licitación" />

      <!-- Card de Documentos Vigentes -->
      <div @click="activeSection = 'vigentes'" :class="[
        'bg-white rounded-lg p-4 border-2 cursor-pointer transition-all duration-200 hover:shadow-md',
        activeSection === 'vigentes'
          ? 'border-green-500 bg-green-50'
          : 'border-gray-200 hover:border-green-300'
      ]">
        <div class="flex items-center">
          <BaseIcon :path="mdiCheckCircle" :class="[
            'mr-3',
            activeSection === 'vigentes' ? 'text-green-600' : 'text-green-500'
          ]" size="24" />
          <div>
            <div class="text-lg font-semibold"
              :class="activeSection === 'vigentes' ? 'text-green-800' : 'text-gray-800'">
              {{ documentosVigentesCount }}
            </div>
            <div :class="[
              'text-sm',
              activeSection === 'vigentes' ? 'text-green-600' : 'text-gray-600'
            ]">
              Doc. Vigentes
            </div>
          </div>
        </div>
        <div v-if="activeSection === 'vigentes'" class="mt-2 text-xs text-green-600 font-medium">
          ← Sección activa
        </div>
      </div>

      <!-- Card de Documentos Vencidos -->
      <div @click="activeSection = 'vencidos'" :class="[
        'bg-white rounded-lg p-4 border-2 cursor-pointer transition-all duration-200 hover:shadow-md',
        activeSection === 'vencidos'
          ? 'border-red-500 bg-red-50'
          : 'border-gray-200 hover:border-red-300'
      ]">
        <div class="flex items-center">
          <BaseIcon :path="mdiAlert" :class="[
            'mr-3',
            activeSection === 'vencidos' ? 'text-red-600' : 'text-red-500'
          ]" size="24" />
          <div>
            <div class="text-lg font-semibold" :class="activeSection === 'vencidos' ? 'text-red-800' : 'text-gray-800'">
              {{ documentosVencidosCount }}
            </div>
            <div :class="[
              'text-sm',
              activeSection === 'vencidos' ? 'text-red-600' : 'text-gray-600'
            ]">
              Doc. Vencidos
            </div>
          </div>
        </div>
        <div v-if="activeSection === 'vencidos'" class="mt-2 text-xs text-red-600 font-medium">
          ← Sección activa
        </div>
      </div>
    </div>

    <!-- SECCIÓN DE DOCUMENTOS VIGENTES -->
    <div v-if="activeSection === 'vigentes'">
      <!-- Documentos Técnicos y Legales VIGENTES -->
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
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Documento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Departamento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Revalidación</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vigencia</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ver</th>
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
                    <div class="flex items-center gap-2">

                      <DocumentDetailsModal :documento="documento" :tipo-documento="'documento'" />
                      <CatalogoRedirectButton catalog-route-name="documento-legal" return-route="dashboard"
                        :return-id="documento.id" label="Editar Documento" :icon="mdiArrowRight" outline mode="edit"
                        color="success" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <PaginationDashboard :currentPage="documentos.current_page" :links="documentos.links"
            :total="documentos.last_page" pageParam="page_tecnico" />
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
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Documento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Departamento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Revalidación</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vigencia</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ver</th>
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
                    <div class="flex items-center gap-2">
                      <DocumentDetailsModal :documento="documento" :tipo-documento="'documento-legal'" />
                      <CatalogoRedirectButton catalog-route-name="documento-legal" return-route="dashboard"
                        :return-id="documento.id" label="Editar Documento" :icon="mdiArrowRight" outline mode="edit"
                        color="success" />
                    </div>

                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <PaginationDashboard :currentPage="documentosLegal.current_page" :links="documentosLegal.links"
            :total="documentosLegal.last_page" pageParam="page_legal" />
        </CardBox>
      </div>
    </div>

    <!-- SECCIÓN DE DOCUMENTOS VENCIDOS -->
    <div v-if="activeSection === 'vencidos'">
      <!-- Documentos Técnicos y Legales VENCIDOS -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Documentos Técnicos Vencidos -->
        <CardBox>
          <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                  d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z"
                  clip-rule="evenodd" />
              </svg>
              {{ titulo }} Vencidos
            </h2>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Documento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Departamento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Revalidación</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vigencia</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ver</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="documento in documentosVencidos.data" :key="documento.id"
                  class="hover:bg-gray-50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-normal text-sm font-medium text-gray-900" data-label="Documento">
                    {{ documento.tipo_de_documento.nombre_documento }}
                  </td>
                  <td class="px-6 py-4 whitespace-normal text-sm text-gray-500" data-label="Departamento">
                    {{ documento.departamento.nombre_departamento }}
                  </td>
                  <td data-label="Fecha Revalidación" class="px-6 py-4 text-sm text-gray-500">
                    <div class="flex flex-col">
                      <div>{{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}</div>
                      <span :class="{
                        'bg-red-600 text-white': documento.dias_restantes_revalidacion < 0,
                        'bg-red-200 text-red-800': documento.dias_restantes_revalidacion === 0,
                        'bg-blue-100 text-blue-800': documento.dias_restantes_revalidacion > 0
                      }"
                        class="px-3 py-1 mt-1 inline-flex justify-center text-xs leading-5 font-semibold rounded-full truncate max-w-[120px]">
                        {{
                          documento.dias_restantes_revalidacion < 0 ? `Vencido hace
                          ${Math.abs(documento.dias_restantes_revalidacion)} días` :
                            documento.dias_restantes_revalidacion === 0 ? 'Vence hoy' : `Faltan
                          ${documento.dias_restantes_revalidacion} días` }} </span>
                    </div>
                  </td>
                  <td data-label="Fecha Vigencia" class="px-6 py-4 text-sm text-gray-500">
                    <div class="flex flex-col">
                      <div>{{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}</div>
                      <span :class="{
                        'bg-red-500 text-white': documento.dias_restantes <= 0,
                        'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                        'bg-green-100 text-green-800': documento.dias_restantes > 7
                      }"
                        class="px-3 py-1 inline-flex justify-center text-xs leading-5 font-semibold rounded-full truncate max-w-[120px]">
                        {{
                          documento.dias_restantes < 0 ? `Vencido hace ${Math.abs(documento.dias_restantes)} días` :
                            documento.dias_restantes === 0 ? 'Vence hoy' : `Faltan ${documento.dias_restantes} días` }}
                          </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <DocumentDetailsModal :documento="documento" :tipo-documento="'documento'" />
                      <CatalogoRedirectButton catalog-route-name="documento-legal" return-route="dashboard"
                        :return-id="documento.id" label="Editar Documento" :icon="mdiArrowRight" outline mode="edit"
                        color="success" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <PaginationDashboard :currentPage="documentosVencidos.current_page" :links="documentosVencidos.links"
            :total="documentosVencidos.last_page" pageParam="page_tecnico_vencidos" />
        </CardBox>

        <!-- Documentos Legales Vencidos-->
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
              {{ titulo2 }} Vencidos
            </h2>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Documento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Departamento</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Revalidación</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vigencia</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ver</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="documento in documentosLegalVencidos.data" :key="documento.id"
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
                        {{
                          documento.dias_restantes_revalidacion < 0 ? `Vencido hace
                          ${Math.abs(documento.dias_restantes_revalidacion)} días` :
                            documento.dias_restantes_revalidacion === 0 ? 'Vence hoy' : `Faltan
                          ${documento.dias_restantes_revalidacion} días` }} </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Fecha Vigencia">
                    <div class="flex flex-col">
                      <div>{{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}</div>
                      <span :class="{
                        'bg-red-500 text-white': documento.dias_restantes <= 0,
                        'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                        'bg-green-100 text-green-800': documento.dias_restantes > 7
                      }"
                        class="px-3 py-1 inline-flex justify-center text-xs leading-5 font-semibold rounded-full truncate max-w-[120px]">
                        {{
                          documento.dias_restantes < 0 ? `Vencido hace ${Math.abs(documento.dias_restantes)} días` :
                            documento.dias_restantes === 0 ? 'Vence hoy' : `Faltan ${documento.dias_restantes} días` }}
                          </span>
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">

                    <div class="flex items-center gap-2">
                      <DocumentDetailsModal :documento="documento" :tipo-documento="'documento-legal'" />
                      <CatalogoRedirectButton catalog-route-name="documento-legal" return-route="dashboard"
                        :return-id="documento.id" label="Editar Documento" :icon="mdiArrowRight" outline mode="edit"
                        color="success" />
                    </div>

                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <PaginationDashboard :currentPage="documentosLegalVencidos.current_page"
            :links="documentosLegalVencidos.links" :total="documentosLegalVencidos.last_page"
            pageParam="page_legal_vencidos" />
        </CardBox>
      </div>
    </div>
  </LayoutDashboard>
</template>