<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch } from "vue";
import { useMainStore } from "@/stores/main";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import LineChart from "@/Components/LineChart.vue"; // Asegúrate de que este componente existe y renderiza el gráfico
import { Chart } from 'chart.js';
import { defineComponent } from 'vue';
import { Line } from 'vue-chartjs';  // O cualquier otro tipo de gráfico que estés usando
import { Chart as ChartJS, CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement } from 'chart.js';

// Registrar los componentes necesarios de Chart.js
ChartJS.register(CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement);

// Declaramos y desestructuramos las props para tener acceso a ellas en todo el script
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
  return props.documentos.data.filter(documento => documento.dias_restantes === 0).length;
});

const documentosLegalesVencidos = computed(() => {
  return props.documentosLegal.data.filter(documento => documento.dias_restantes === 0).length;
});


//--------------------------------------------




</script>

<template>


  <Head title="Dashboard Admin" />
  <LayoutDashboard>
    <SectionMain>
      <SectionTitleLineWithButton title="Bienvenido al Dashboard de Admin" main />
    </SectionMain>

    <div class="p-6 bg-gray-100 min-h-screen">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Admin</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Tarjeta de Usuarios Registrados -->
        <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
          <h2 class="text-xl font-semibold text-gray-800">Usuarios Registrados</h2>
          <p class="text-4xl font-bold text-indigo-600">{{ users }}</p>
        </div>   

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


      <!-- Últimos usuarios registrados -->
      <div class="mt-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Últimos usuarios registrados</h2>
          <table class="min-w-full bg-white rounded-lg shadow-lg border-separate border-spacing-0">
            <thead>
              <tr class="border-b bg-gray-100">
                <th class="px-6 py-3 text-left text-gray-600">Nombre</th>
                <th class="px-6 py-3 text-left text-gray-600">Correo Electrónico</th>
                <th class="px-6 py-3 text-left text-gray-600">Fecha de Registro</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in latestUsers" :key="user.id" class="border-b hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-700">{{ user.name }}</td>
                <td class="px-6 py-4 text-gray-700">{{ user.email }}</td>
                <td class="px-6 py-4 text-gray-700">
                  {{ new Date(user.created_at).toLocaleString('es-MX', { hour12: true }) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Secciones de Documentos -->
      <div class="mt-8 space-y-8 grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Documentos Técnicos -->
        <div class="min-h-full">
          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ titulo }}</h2>
            <table class="min-w-full bg-white rounded-lg shadow-lg">
              <thead>
                <tr class="border-b bg-gray-200">
                  <th class="px-6 py-3 text-left text-gray-600">Nombre Documento</th>
                  <th class="px-6 py-3 text-left text-gray-600">Departamento</th>
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Revalidación</th>
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Vigencia</th>
                  <th class="px-6 py-3 text-left text-gray-600">Días Restantes</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="documento in documentos.data" :key="documento.id" class="border-b">
                  <td class="px-6 py-4 text-gray-700">{{ documento.tipo_de_documento.nombre_documento }}</td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.departamento.nombre_departamento }}</td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.fecha_revalidacion }}</td>
                  <td :class="{ 'bg-red-500 text-white': documento.dias_restantes <= 7 }"
                    class="px-6 py-4 text-gray-700">
                    {{ documento.fecha_vigencia }}
                  </td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.dias_restantes }} días</td>
                </tr>
              </tbody>
            </table>
          </section>
        </div>

        <!-- Documentos Legales -->
        <div class="min-h-full">
          <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ titulo2 }}</h2>
            <table class="min-w-full bg-white rounded-lg shadow-lg">
              <thead>
                <tr class="border-b bg-gray-200">
                  <th class="px-6 py-3 text-left text-gray-600">Nombre Documento</th>
                  <th class="px-6 py-3 text-left text-gray-600">Departamento</th>
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Revalidación</th>
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Vigencia</th>
                  <th class="px-6 py-3 text-left text-gray-600">Días Restantes</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="documento in documentosLegal.data" :key="documento.id" class="border-b">
                  <td class="px-6 py-4 text-gray-700">{{ documento.tipo_de_documento.nombre_documento }}</td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.departamento.nombre_departamento }}</td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.fecha_revalidacion }}</td>
                  <td :class="{ 'bg-red-500 text-white': documento.dias_restantes <= 7 }"
                    class="px-6 py-4 text-gray-700">
                    {{ documento.fecha_vigencia }}
                  </td>
                  <td class="px-6 py-4 text-gray-700">
                    <span v-if="documento.dias_restantes === 0">Vencido</span>
                    <span v-else>{{ documento.dias_restantes }} días</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </section>
        </div>
      </div>
    </div>
  </LayoutDashboard>
</template>
