<script setup>
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import Pagination from '@/Shared/Pagination.vue';
import { Link } from '@inertiajs/vue3';
import { Chart as ChartJS, CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement, ArcElement, PointElement, LineElement } from 'chart.js';
import { Bar, Doughnut } from 'vue-chartjs'; // Importa el componente Bar
import LineChart from "@/Components/LineChart.vue";
import { Line } from 'vue-chartjs'; // Añade esta importación


import { onMounted } from 'vue';

onMounted(() => {
  console.log("El gráfico se montó correctamente");
});
onMounted(() => {
  console.log("El gráfico se montó correctamente");
  console.log("Datos del gráfico:", lineChartData);
});

// Registrar los componentes necesarios de Chart.js
ChartJS.register(CategoryScale, LinearScale, Title, Tooltip, Legend, BarElement, ArcElement // Necesario para Doughnut
  , PointElement, LineElement);


const props = defineProps({
  users: Number,
  documentos: Object,
  documentosLegal: Object,
  titulo: String,
  titulo2: String,
  latestUsers: Object,
});


console.log('Doc Tecnicos:', props.documentos);
console.log('Doc Tecnicos:', props.documentosLegal);


// Contar total de documentos
const totalDocumentosTecnicos = computed(() => props.documentos.total);
const totalDocumentosLegales = computed(() => props.documentosLegal.total);


const documentosTecnicosVencidos = computed(() => {
  return props.documentos.data.filter(documento => documento.dias_restantes === 0).length;
});

const documentosLegalesVencidos = computed(() => {
  return props.documentosLegal.data.filter(documento => documento.dias_restantes === 0).length;
});

//------------------------
// Datos para el gráfico de barras
const chartData = {
  labels: ['Documentos Técnicos', 'Documentos Legales'],
  datasets: [
    {
      label: 'Total Documentos',
      data: [totalDocumentosTecnicos.value, totalDocumentosLegales.value],
      backgroundColor: 'rgba(79, 70, 229, 0.5)', // Color indigo-500 con transparencia
      borderColor: 'rgba(79, 70, 229, 1)',
      borderWidth: 1,
    },
    {
      label: 'Documentos Vencidos',
      data: [documentosTecnicosVencidos.value, documentosLegalesVencidos.value],
      backgroundColor: 'rgba(220, 38, 38, 0.5)', // Color red-600 con transparencia
      borderColor: 'rgba(220, 38, 38, 1)',
      borderWidth: 1,
    }
  ]
};

const chartOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: 'top',
    },
    title: {
      display: true,
      text: 'Comparación de Documentos Técnicos vs Legales',
    },
  },
};
//-------------------------
// Datos para el gráfico de doughnut
const chartData2 = {
  labels: ['Documentos Técnicos', 'Documentos Legales'],
  datasets: [{
    data: [totalDocumentosTecnicos.value, totalDocumentosLegales.value],
    backgroundColor: [
      'rgba(79, 70, 229, 0.8)', // Indigo-600
      'rgba(16, 185, 129, 0.8)', // Emerald-500
    ],
    borderColor: [
      'rgba(79, 70, 229, 1)',
      'rgba(16, 185, 129, 1)',
    ],
    borderWidth: 1,
    hoverOffset: 10 // Efecto al pasar el mouse
  }]
};

const chartOptions2 = {
  responsive: true,
  plugins: {
    legend: {
      position: 'right', // Ubica la leyenda a la derecha
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
  cutout: '70%', // Para doughnut (usa 0% para pie chart)
};

//-------------------tercer grafica 
// Agrupar documentos por rango de días restantes
const agruparPorDiasRestantes = (documentos) => {
  return {
    vencidos: documentos.filter(d => d.dias_restantes === 0).length,
    porVencer: documentos.filter(d => d.dias_restantes > 0 && d.dias_restantes <= 7).length,
    vigentes: documentos.filter(d => d.dias_restantes > 7 && d.dias_restantes <= 30).length,
    lejanos: documentos.filter(d => d.dias_restantes > 30).length
  };
};

const lineChartData = {
  labels: ['Vencidos (0 días)', 'Por vencer (1-7 días)', 'Vigentes (8-30 días)', 'Lejanos (30+ días)'],
  datasets: [
    {
      label: 'Documentos Técnicos',
      data: Object.values(agruparPorDiasRestantes(props.documentos.data)),
      borderColor: 'rgba(79, 70, 229, 1)', // Indigo
      backgroundColor: 'rgba(79, 70, 229, 0.2)',
      tension: 0.3, // Suaviza la línea
      fill: true
    },
    {
      label: 'Documentos Legales',
      data: Object.values(agruparPorDiasRestantes(props.documentosLegal.data)),
      borderColor: 'rgba(16, 185, 129, 1)', // Emerald
      backgroundColor: 'rgba(16, 185, 129, 0.2)',
      tension: 0.3,
      fill: true
    }
  ]
};

const lineChartOptions = {
  responsive: true,
  plugins: {
    title: {
      display: true,
      text: 'Tendencia de Vencimiento de Documentos',
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
    y: {
      beginAtZero: true,
      title: { display: true, text: 'Cantidad de Documentos' }
    }
  }
};






///----------------4 grafica 
// ===== DATOS PARA GRÁFICA HORIZONTAL (Documentos por Departamento) =====
// ===== DATOS PARA GRÁFICA HORIZONTAL (USANDO TODOS LOS DOCUMENTOS) =====



const procesarDocumentosPorDepartamento = () => {
  // Asegúrate de que tus props incluyan todos los documentos (no solo los paginados)

  const todosDocumentosTecnicos = props.documentos.all_data || props.documentos.data; // Adapta según tu API
  const todosDocumentosLegales = props.documentosLegal.all_data || props.documentosLegal.data;
  // Verifica si tienes los datos completos
  console.log('Técnicos sin paginar:', todosDocumentosTecnicos.length);
  console.log('Legales sin paginar:', todosDocumentosLegales.length);

  // Agrupar documentos técnicos por departamento (usando todos los registros)
  const tecnicosPorDepto = todosDocumentosTecnicos.reduce((acc, doc) => {
    const depto = doc.departamento.nombre_departamento;
    acc[depto] = acc[depto] || { total: 0, vencidos: 0 };
    acc[depto].total++;
    if (doc.dias_restantes === 0) acc[depto].vencidos++;
    return acc;
  }, {});

  // Agrupar documentos legales por departamento (usando todos los registros)
  const legalesPorDepto = todosDocumentosLegales.reduce((acc, doc) => {
    const depto = doc.departamento.nombre_departamento;
    acc[depto] = acc[depto] || { total: 0, vencidos: 0 };
    acc[depto].total++;
    if (doc.dias_restantes === 0) acc[depto].vencidos++;
    return acc;
  }, {});

  // Obtener departamentos únicos (combina técnicos + legales)
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
  indexAxis: 'y', // Hace las barras horizontales
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
</script>

<template>

  <Head title="Dashboard Usuario" />
  <LayoutDashboard>
    <SectionMain>
      <SectionTitleLineWithButton title="Bienvenido al Dashboard de Usuario" main />
    </SectionMain>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Sección del Gráfico de Barras -->
      <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Resumen de Documentos</h2>
        <Bar :data="chartData" :options="chartOptions" />
      </div>
      <!-- Sección del Gráfico de Doughnut -->
      <div class="bg-white p-6 rounded-lg shadow-lg mb-6 mx-auto max-w-2xl">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Distribución de Documentos</h2>
        <div class="h-80"> <!-- Altura fija para mejor visualización -->
          <Doughnut :data="chartData2" :options="chartOptions2" />
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-lg mb-6 col-span-1 md:col-span-2 lg:col-span-3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Tendencia de Vencimientos</h2>
        <div class="h-96"> <!-- Altura más grande para mejor visualización -->
          <Line :data="lineChartData" :options="lineChartOptions" />
        </div>
      </div>

      <!-- Sección de la CUARTA GRÁFICA -->
      <div class="bg-white p-6 rounded-lg shadow-lg mb-6 col-span-1 md:col-span-2 lg:col-span-3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Distribución por Departamento</h2>
        <div class="h-[32rem]"> <!-- Altura ajustable -->
          <Bar :data="horizontalBarData" :options="horizontalBarOptions" />
        </div>
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

    <!-- Secciones de Documentos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Documentos Técnicos -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
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
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Documento</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Departamento</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Revalidación</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Vigencia</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="documento in documentos.data" :key="documento.id"
                class="hover:bg-gray-50 transition-colors duration-150">
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
                  <span :class="{
                    'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                    'bg-green-100 text-green-800': documento.dias_restantes > 7
                  }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ documento.dias_restantes === 0 ? 'Vencido' : `${documento.dias_restantes} días ` }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
          <Pagination :currentPage="documentos.current_page" :links="documentos.links"
            :total="documentos.links.length - 2" />
        </div>
      </div>

      <!-- Documentos Legales -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
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
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Documento</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Departamento</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Revalidación</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Vigencia</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="documento in documentosLegal.data" :key="documento.id"
                class="hover:bg-gray-50 transition-colors duration-150">
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
                  <span :class="{
                    'bg-red-100 text-red-800': documento.dias_restantes <= 7,
                    'bg-green-100 text-green-800': documento.dias_restantes > 7
                  }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ documento.dias_restantes === 0 ? 'Vencido' : `${documento.dias_restantes} días ` }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
          <Pagination :currentPage="documentosLegal.current_page" :links="documentosLegal.links"
            :total="documentosLegal.links.length - 2" />
        </div>
      </div>
    </div>


  </LayoutDashboard>
</template>
