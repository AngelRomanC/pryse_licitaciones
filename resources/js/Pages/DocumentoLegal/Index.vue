<script setup>
import { router } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import Swal from "sweetalert2";
import { mdiTagEdit, mdiDeleteOutline,mdiInformation, mdiMagnify, mdiFilterVariant } from "@mdi/js";
import Pagination from '@/Shared/Pagination.vue';
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import moment from "moment";
import { ref, watch } from 'vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";




const props = defineProps({
    documentos: Object,
    titulo: String, //
    routeName: String,
    empresas: Array,       // Nuevo prop para las empresas
    tipos_documento: Array, // Nuevo prop para tipos de documento
    departamentos: Array,   // Nuevo prop para departamentos
    filters: Object, // Recibimos los filtros iniciales
});
import { onMounted } from 'vue';

// onMounted(() => {
//     console.log('empresas:', props.empresas);
//     console.log('tipos_documento:', props.tipos_documento);
//     console.log('departamentos:', props.departamentos);
// });
// Filtros reactivos
const filters = ref({
    empresa:  props.filters.empresa || '',
    tipo_de_documento: props.filters.tipo_de_documento || '',
    departamento: props.filters.departamento || '',
    search: props.filters.search || '',
});

// Observar cambios en los filtros
watch(filters.value, (newFilters) => {
    router.get(route(`${props.routeName}index`), newFilters, {
        preserveState: true,
        replace: true
    });
}, { deep: true });

const destroy = (id) => {
    Swal.fire({
        title: "¿Está seguro?",
        text: "Esta acción no se puede revertir",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar registro!",
    }).then((res) => {
        if (res.isConfirmed) {
            //router.delete(route("departamento.destroy", id));
            router.delete(route(`${props.routeName}destroy`, id));

        }
    });
};
const resetFilters = () => {
    filters.value = {
        empresa: '',
        tipo_documento: '',
        departamento: '',
        search: ''
    };
     router.get(route(`${props.routeName}index`), {}, {
    preserveState: false,
    replace: true,
  })
};
</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :title="props.titulo" main>
            <BaseButton :href="route(`${props.routeName}create`)" color="warning" label="Crear" />
        </SectionTitleLineWithButton>

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>
              <!-- Filtros de búsqueda -->
        <CardBox class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda general -->
                <FormField label="Búsqueda general">
                    <FormControl
                        v-model="filters.search"
                        type="text"
                        placeholder="Buscar..."
                        :icon="mdiMagnify"
                    />
                </FormField>

                <!-- Filtro por empresa -->
                <FormField label="Empresa">
                    <FormControl
                        v-model="filters.empresa"
                        :options="empresas"
                        type="select"
                        placeholder="Todas las empresas"
                        label-key="nombre"
                        value-key="id"
                    />
                </FormField>

                <!-- Filtro por tipo de documento -->
                <FormField label="Documento">
                    <FormControl
                        v-model="filters.tipo_de_documento"
                        :options="tipos_documento"
                        type="select"
                        placeholder="Todos los documentos"
                        label-key="nombre_documento"
                        value-key="id"
                    />
                </FormField>

                <!-- Filtro por departamento -->
                <FormField label="Departamento">
                    <FormControl
                        v-model="filters.departamento"
                        :options="departamentos"
                        type="select"
                        placeholder="Todos los departamentos"
                        label-key="nombre_departamento"
                        value-key="id"
                    />
                </FormField>
            </div>

            <div class="flex justify-end mt-4">
                <BaseButton 
                    @click="resetFilters" 
                    color="danger" 
                    outline 
                    label="Limpiar filtros" 
                    :icon="mdiFilterVariant"
                />
            </div>
        </CardBox>

        <CardBox v-if="documentos.data.length < 1">
            <CardBoxComponentEmpty />
        </CardBox>

        <CardBox v-else class="mb-6" has-table>
            <table class="w-full border-collapse border mt-4">
                <thead>
                    <tr>
                        <th />
                        <th class="border p-2">Nombre de Documento</th>
                        <th class="border p-2">Empresa</th>
                        <th class="border p-2">Documento</th>
                        <th class="border p-2">Departamento</th>
                        <th class="border p-2">Revalidación</th>
                        <th class="border p-2">Vigencia</th>
                        <th class="border p-2">Acciones</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="documento in documentos.data" :key="documento.id">
                        <td class="align-items-center">
                        </td>
                        <td data-label="Nombre de Documento" class="border p-2">{{ documento.nombre_documento }}</td>
                        <td data-label="Empresa" class="border p-2">{{ documento.empresa.nombre}}</td>
                        <td data-label="Documento" class="border p-2">{{ documento.tipo_de_documento.nombre_documento}}</td>
                        <td data-label="Departamento" class="border p-2">{{ documento.departamento.nombre_departamento}}</td>
                        <td data-label="Fecha Revalidación" class="border p-2">{{ moment(documento.fecha_revalidacion).format("DD/MM/YYYY") }}</td>
                        <td data-label="Fecha Vigencia" class="border p-2">{{ moment(documento.fecha_vigencia).format("DD/MM/YYYY") }}</td>               
                     
                        <td class="before:hidden lg:w-1 whitespace-nowrap">
                            <BaseButtons type="justify-start lg:justify-end" no-wrap>
                                <BaseButton color="info" :icon="mdiTagEdit" small
                                    :href="route(`${props.routeName}edit`, documento.id)" />
                                <BaseButton color="danger" :icon="mdiDeleteOutline" small
                                    @click="destroy(documento.id)" />
                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :currentPage="documentos.current_page" :links="documentos.links"
                :total="documentos.links.length - 2" />
        </CardBox>
    </LayoutMain>
</template>