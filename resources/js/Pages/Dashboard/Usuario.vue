<script setup>
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import LayoutDashboard from "@/Layouts/LayoutDashboard.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";

const props = defineProps({
    message: String,
    documentos: Object,
    documentosLegal: Object,
    titulo: String,
    titulo2: String, //
});
</script>

<template>

    <Head title="Dashboard Usuario" />
    <LayoutDashboard>
        <SectionMain>
            <SectionTitleLineWithButton title="Bienvenido al Dashboard de Usuario" main />
        </SectionMain>

        <div class="p-6 bg-gray-100 min-h-screen">
            <h1 class="text-2xl font-bold text-gray-700">Dashboard Usuario</h1>

            <div class="bg-white p-4 rounded-lg shadow mt-4">
                <h2 class="text-lg font-semibold text-gray-800">Mensaje de Bienvenida</h2>
                <p class="text-gray-600">{{ message }}</p>
            </div>

            <h1>{{ titulo }}</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nombre Documento</th>
                        <th>Fecha Revalidación</th>
                        <th>Fecha Vigencia</th>
                        <th>Días Restantes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="documento in documentos.data" :key="documento.id">
                        <td>{{ documento.nombre_documento }}</td>
                        <td>{{ documento.fecha_revalidacion }}</td>
                        <td :class="{ 'bg-red-500 text-white': documento.dias_restantes <= 7 }">
                            {{ documento.fecha_vigencia }}
                        </td>
                        <td>{{ documento.dias_restantes }} días</td>
                    </tr>
                </tbody>

                <h1>{{ titulo2 }}</h1>

                <thead>
                    <tr>
                        <th>Nombre Documento</th>
                        <th>Fecha Revalidación</th>
                        <th>Fecha Vigencia</th>
                        <th>Días Restantes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="documento in documentosLegal.data" :key="documento.id">
                        <td>{{ documento.nombre_documento }}</td>
                        <td>{{ documento.fecha_revalidacion }}</td>
                        <td :class="{ 'bg-red-500 text-white': documento.dias_restantes <= 7 }">
                            {{ documento.fecha_vigencia }}
                        </td>
                        <td>{{ documento.dias_restantes }} días</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </LayoutDashboard>
</template>
