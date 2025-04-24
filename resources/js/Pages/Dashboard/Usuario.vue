<script setup>
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import Pagination from '@/Shared/Pagination.vue';
import { Chart as ChartJS, CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement, ArcElement, PointElement, LineElement } from 'chart.js';
import { Bar, Doughnut } from 'vue-chartjs';
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import CardBox from "@/components/CardBox.vue";
import moment from "moment";
import Swal from 'sweetalert2';
import BaseButton from "@/components/BaseButton.vue";
import { mdiEye } from "@mdi/js";




// Registrar los componentes necesarios de Chart.js
ChartJS.register(CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement, ArcElement, PointElement, LineElement);

const props = defineProps({
  users: Number,
  documentos: Object,
  documentosLegal: Object,
  titulo: String,
  titulo2: String,
  d1: Array,
  d2: Array,
});

// Contar total de documentos
const totalDocumentosTecnicos = computed(() => props.documentos.total);
const totalDocumentosLegales = computed(() => props.documentosLegal.total);

const documentosTecnicosVencidos = computed(() => {
  return props.d1.filter(documento => documento.dias_restantes <= 0).length; // Mando las licitaciones vencidas total 
});

const documentosLegalesVencidos = computed(() => {
  return props.d2.filter(documento => documento.dias_restantes <= 0).length;
});

// Datos para el gráfico de doughnut
const chartData2 = {
  labels: ['Documentos Técnicos', 'Documentos Legales'],
  datasets: [{
    data: [totalDocumentosTecnicos.value, totalDocumentosLegales.value],
    backgroundColor: [
      'rgba(79, 70, 229, 0.8)',
      'rgba(16, 185, 129, 0.8)',
    ],
    borderColor: [
      'rgba(79, 70, 229, 1)',
      'rgba(16, 185, 129, 1)',
    ],
    borderWidth: 1,
    hoverOffset: 10
  }]
};

const chartOptions2 = {
  responsive: true,
  plugins: {
    legend: {
      position: 'right',
    },
    title: {
      display: true,
      text: 'Proporción de Documentos Técnicos vs Legales',
      font: {
        size: 16
      }
    },
    tooltip: {
      callbacks: {
        label: function (context) {
          const label = context.label || '';
          const value = context.raw || 0;
          const total = context.dataset.data.reduce((a, b) => a + b, 0);
          const percentage = Math.round((value / total) * 100);
          return `${label}: ${value} (${percentage}%)`;
        }
      }
    }
  },
  cutout: '70%',
};

// Procesar documentos por departamento
const procesarDocumentosPorDepartamento = () => {
  const todosDocumentosTecnicos = props.d1;
  const todosDocumentosLegales = props.d2;

  const tecnicosPorDepto = todosDocumentosTecnicos.reduce((acc, doc) => {
    if (!doc.departamento || !doc.departamento.nombre_departamento) return acc;
    const depto = doc.departamento.nombre_departamento;
    acc[depto] = acc[depto] || { total: 0, vencidos: 0 };
    acc[depto].total++;
    if (doc.dias_restantes <= 0) acc[depto].vencidos++;
    return acc;
  }, {});

  const legalesPorDepto = todosDocumentosLegales.reduce((acc, doc) => {
    if (!doc.departamento || !doc.departamento.nombre_departamento) return acc;
    const depto = doc.departamento.nombre_departamento;
    acc[depto] = acc[depto] || { total: 0, vencidos: 0 };
    acc[depto].total++;
    if (doc.dias_restantes <= 0) acc[depto].vencidos++;
    return acc;
  }, {});

  const departamentos = [...new Set([
    ...Object.keys(tecnicosPorDepto),
    ...Object.keys(legalesPorDepto)
  ])];

  return {
    labels: departamentos,
    datasets: [
      {
        label: 'Técnicos (Total)',
        data: departamentos.map(depto => tecnicosPorDepto[depto]?.total || 0),
        backgroundColor: 'rgba(79, 70, 229, 0.6)',
        borderColor: 'rgba(79, 70, 229, 1)',
        borderWidth: 1
      },
      {
        label: 'Técnicos (Vencidos)',
        data: departamentos.map(depto => tecnicosPorDepto[depto]?.vencidos || 0),
        backgroundColor: 'rgba(220, 38, 38, 0.6)',
        borderColor: 'rgba(220, 38, 38, 1)',
        borderWidth: 1
      },
      {
        label: 'Legales (Total)',
        data: departamentos.map(depto => legalesPorDepto[depto]?.total || 0),
        backgroundColor: 'rgba(16, 185, 129, 0.6)',
        borderColor: 'rgba(16, 185, 129, 1)',
        borderWidth: 1
      },
      {
        label: 'Legales (Vencidos)',
        data: departamentos.map(depto => legalesPorDepto[depto]?.vencidos || 0),
        backgroundColor: 'rgba(245, 158, 11, 0.6)',
        borderColor: 'rgba(245, 158, 11, 1)',
        borderWidth: 1
      }
    ]
  };
};

const horizontalBarData = procesarDocumentosPorDepartamento();

const horizontalBarOptions = {
  indexAxis: 'y',
  responsive: true,
  plugins: {
    title: {
      display: true,
      text: 'Documentos por Departamento (Totales vs Vencidos)',
      font: { size: 16 }
    },
    tooltip: {
      callbacks: {
        label: (context) => {
          const label = context.dataset.label || '';
          const value = context.raw || 0;
          return `${label}: ${value} documentos`;
        }
      }
    }
  },
  scales: {
    x: { stacked: false, title: { display: true, text: 'Cantidad de Documentos' } },
    y: { stacked: true }
  }
};

// Detalle de documento
const mostrarDetalles = (documento) => {
  const tienePrincipal = documento.ruta_documento;
  const tieneAnexo = documento.ruta_documento_anexo;

  Swal.fire({
    title: `<div class="text-2xl font-bold text-gray-800">${documento.tipo_de_documento.nombre_documento}</div>`,
    html: `
      <div class="text-left space-y-6">
        <!-- Header con icono -->
        <div class="flex items-start space-x-4">
          <div class="bg-blue-100 p-3 rounded-full flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-800">${documento.nombre_documento}</h2>
            <p class="text-sm text-gray-500">${documento.departamento.nombre_departamento}</p>
          </div>
        </div>

        <!-- Tarjetas de información -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-gray-500">Revalidación</p>
                <p class="text-gray-800 font-medium">${moment(documento.fecha_revalidacion).format("DD/MM/YYYY")}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-gray-500">Vigencia</p>
                <p class="text-gray-800 font-medium">${moment(documento.fecha_vigencia).format("DD/MM/YYYY")}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <div>
                <p class="text-sm font-medium text-gray-500">Estado</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${documento.dias_restantes <= 5 ? 'bg-red-300 text-red-800' :
        documento.dias_restantes <= 7 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
      }">
                  ${documento.dias_restantes <= 0 ? 'Vencido' : documento.dias_restantes + ' días restantes'}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="border-t border-gray-200 pt-4">
          <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Visualizar documentos</h4>
          <div class="flex flex-wrap gap-3">
            <button onclick="mostrarArchivoEnModal('/storage/${documento.ruta_documento}')" 
              class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ${!tienePrincipal ? 'opacity-50 cursor-not-allowed' : ''
      }" 
              ${!tienePrincipal ? 'disabled' : ''}>
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              Ver Documento Principal
            </button>
            
            ${tieneAnexo ? `
              <button onclick="mostrarArchivoEnModal('/storage/${documento.ruta_documento_anexo}')" 
                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Ver Documento Anexo
              </button>
            ` : ''}
          </div>
        </div>

        <!-- Visor de PDF con diseño premium -->
        <div id="pdf-viewer-container" class="mt-4 hidden rounded-lg overflow-hidden border border-gray-300 shadow-lg">
          <div class="bg-gray-800 px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span class="text-sm font-medium text-gray-200">Vista previa del documento</span>
            </div>
            <button onclick="cerrarVisorPDF()" class="text-gray-300 hover:text-white focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <iframe id="pdf-iframe" src="" class="w-full h-[500px]" frameborder="0"></iframe>
        </div>
      </div>
    `,
    width: '900px',
    showConfirmButton: false,
    showCloseButton: true,
    customClass: {
      popup: 'rounded-xl shadow-2xl border border-gray-200',
      closeButton: 'text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-full p-1'
    },
    didOpen: () => {
      window.mostrarArchivoEnModal = (ruta) => {
        if (!ruta) return;
        const iframe = document.getElementById('pdf-iframe');
        const container = document.getElementById('pdf-viewer-container');
        iframe.src = ruta;
        container.classList.remove('hidden');
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      };

      window.cerrarVisorPDF = () => {
        const container = document.getElementById('pdf-viewer-container');
        const iframe = document.getElementById('pdf-iframe');
        container.classList.add('hidden');
        iframe.src = '';
      };
    },
    willClose: () => {
      delete window.mostrarArchivoEnModal;
      delete window.cerrarVisorPDF;
    }
  });
};

</script>

<template>

  <Head title="Dashboard Usuario" />
  <LayoutDashboard>
    <SectionMain>
      <SectionTitleLineWithButton title="Bienvenido al Dashboard de Usuario" main class="mb-8" />
    </SectionMain>

    <!-- Sección de Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Tarjeta de Documentos Técnicos -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Documentos Técnicos</h3>
            <p class="text-2xl font-bold text-indigo-600">{{ totalDocumentosTecnicos }}</p>
          </div>
        </div>
      </CardBox>

      <!-- Tarjeta de Licitaciones Técnicas -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Licitaciones Técnicas</h3>
            <p class="text-2xl font-bold text-red-600">{{ documentosTecnicosVencidos }}</p>
            <p class="text-xs text-gray-500 mt-1" v-if="documentosTecnicosVencidos > 0">¡Requieren atención!</p>
          </div>
        </div>
      </CardBox>

      <!-- Tarjeta de Documentos Legales -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Documentos Legales</h3>
            <p class="text-2xl font-bold text-green-600">{{ totalDocumentosLegales }}</p>
          </div>
        </div>
      </CardBox>

      <!-- Tarjeta de Licitaciones Legales -->
      <CardBox class="hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
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

    <!-- Sección de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Gráfico de Doughnut -->
      <CardBox class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" viewBox="0 0 20 20"
            fill="currentColor">
            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
          </svg>
          Distribución de Documentos
        </h2>
        <div class="h-80">
          <Doughnut :data="chartData2" :options="chartOptions2" />
        </div>
      </CardBox>

      <!-- Gráfico de Barras Horizontales -->
      <CardBox class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" viewBox="0 0 20 20"
            fill="currentColor">
            <path
              d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
          </svg>
          Documentos por Departamento
        </h2>
        <div class="h-80">
          <Bar :data="horizontalBarData" :options="horizontalBarOptions" />
        </div>
      </CardBox>
    </div>

    <!-- Secciones de Documentos -->
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
                class="hover:bg-gray-50 transition-colors duration-150"
                :class="{ 'bg-red-50': documento.dias_restantes <= 7 }">
               
                <td data-label="Nombre de Documento" class="px-6 py-4  whitespace-normal text-sm font-medium text-gray-900">
                  {{ documento.tipo_de_documento.nombre_documento }}
                </td>
                <td data-label="Departamento" class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                  {{ documento.departamento.nombre_departamento }}
                </td>
                <td data-label="Fecha Revalidación" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}


                </td>
                <td data-label="Fecha Vigencia" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}

                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-2">
                    <span :class="{
                      'bg-red-500 text-white': documento.dias_restantes <= 0,
                      'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                      'bg-green-100 text-green-800': documento.dias_restantes > 7
                    }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }} </span>
                        <BaseButton @click="mostrarDetalles(documento)" :icon="mdiEye" color="lightDark"
                          class="!p-1.5 !rounded-full hover:bg-gray-200 transition-colors"
                          title="Ver detalles del documento" />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
          <Pagination :currentPage="documentos.current_page" :links="documentos.links"
            :total="documentos.links.length - 2" />
        </div>
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
                class="hover:bg-gray-50 transition-colors duration-150"
                :class="{ 'bg-red-50': documento.dias_restantes <= 7 }">
                
                <td data-label="Nombre de Documento" class="px-6 py-4 whitespace-normal text-sm font-medium text-gray-900">
                  {{ documento.tipo_de_documento.nombre_documento }}
                </td>
                
                <td data-label="Departamento" class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                  {{ documento.departamento.nombre_departamento }}
                </td>
                <td data-label="Fecha Revalidación" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}

                </td>
                <td data-label="Fecha Vigencia" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}

                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="{
                    'bg-red-500 text-white': documento.dias_restantes <= 0,
                    'bg-red-200 text-red-800': documento.dias_restantes > 0 && documento.dias_restantes <= 7,
                    'bg-green-100 text-green-800': documento.dias_restantes > 7
                  }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ documento.dias_restantes <= 0 ? 'Vencido' : `${documento.dias_restantes} días` }} </span>
                      <BaseButton @click="mostrarDetalles(documento)" :icon="mdiEye" color="lightDark"
                        class="!p-1.5 !rounded-full hover:bg-gray-200 transition-colors"
                        title="Ver detalles del documento" />
                </td>

              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
          <Pagination :currentPage="documentosLegal.current_page" :links="documentosLegal.links"
            :total="documentosLegal.links.length - 2" />
        </div>
      </CardBox>
    </div>
  </LayoutDashboard>
</template>
