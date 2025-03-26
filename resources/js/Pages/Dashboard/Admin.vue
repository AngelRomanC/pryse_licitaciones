<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch } from "vue";
import { useMainStore } from "@/stores/main";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import LineChart from "@/Components/LineChart.vue"; // Asegúrate de que este componente existe y renderiza el gráfico

// Declaramos y desestructuramos las props para tener acceso a ellas en todo el script
const { 
  users, 
  documentos, 
  documentosLegal, 
  titulo, 
  titulo2, 
  latestUsers 
} = defineProps({
    users: Number,
    documentos: Object,
    documentosLegal: Object,
    titulo: String,
    titulo2: String,
    latestUsers: Object,
});

// Variable reactiva para almacenar los datos del gráfico
const chartData = ref([]);

// Función para llenar los datos del gráfico basados en los documentos
const fillChartData = () => {
  // Accedemos de forma segura a la propiedad 'data'
  const docs = documentos?.data || [];
  if (docs.length === 0) {
    console.warn('No hay datos de documentos disponibles.');
    return;
  }
  
  // Definimos los tipos de documento que vamos a mostrar
  const documentTypes = ['Documento Técnico', 'Documento Legal'];
  
  // Para cada tipo, filtramos los documentos y construimos un objeto con:
  // - name: el nombre del tipo
  // - count: la cantidad de documentos
  // - data: en este ejemplo, un arreglo con un solo número (la cantidad) para generar un gráfico sencillo
  const chart = documentTypes.map(type => {
    const filteredDocs = docs.filter(doc => doc.nombre_documento === type);
    return {
      name: type,
      count: filteredDocs.length,
      data: [filteredDocs.length]  // Usamos el conteo para el gráfico
    };
  });
  
  // Actualizamos la variable reactiva
  chartData.value = chart;
};

// Si los documentos cambian de forma reactiva, actualizamos la gráfica
watch(() => documentos, () => {
  fillChartData();
}, { immediate: true });

// Ejecutamos la función cuando el componente se monta
onMounted(() => {
  console.log('Documentos...............:', documentos);
  fillChartData();
});

const mainStore = useMainStore();
const clientBarItems = computed(() => mainStore.clients.slice(0, 4));
const transactionBarItems = computed(() => mainStore.history);
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
        
        <!-- Gráfico de Actividad -->
        <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Gráfico de Actividad</h2>
          <!-- Renderizamos el gráfico solo si chartData tiene datos -->
          <div v-if="chartData.value && chartData.value.length > 0" class="w-full">
            <LineChart :chartData="chartData.value" />
          </div>
          <div v-else class="text-gray-500">No hay datos disponibles para mostrar.</div>
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
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Revalidación</th>
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Vigencia</th>
                  <th class="px-6 py-3 text-left text-gray-600">Días Restantes</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="documento in documentos.data.filter(doc => doc.nombre_documento === 'Documento Técnico')"
                  :key="documento.id"
                  class="border-b"
                >
                  <td class="px-6 py-4 text-gray-700">{{ documento.nombre_documento }}</td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.fecha_revalidacion }}</td>
                  <td
                    :class="{ 'bg-red-500 text-white': documento.dias_restantes <= 7 }"
                    class="px-6 py-4 text-gray-700"
                  >
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
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Revalidación</th>
                  <th class="px-6 py-3 text-left text-gray-600">Fecha Vigencia</th>
                  <th class="px-6 py-3 text-left text-gray-600">Días Restantes</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="documento in documentos.data.filter(doc => doc.nombre_documento === 'Documento Legal')"
                  :key="documento.id"
                  class="border-b"
                >
                  <td class="px-6 py-4 text-gray-700">{{ documento.nombre_documento }}</td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.fecha_revalidacion }}</td>
                  <td
                    :class="{ 'bg-red-500 text-white': documento.dias_restantes <= 7 }"
                    class="px-6 py-4 text-gray-700"
                  >
                    {{ documento.fecha_vigencia }}
                  </td>
                  <td class="px-6 py-4 text-gray-700">{{ documento.dias_restantes }} días</td>
                </tr>
              </tbody>
            </table>
          </section>
        </div>
      </div>
    </div>
  </LayoutDashboard>
</template>
