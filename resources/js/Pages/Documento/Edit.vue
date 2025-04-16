<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import { mdiBallotOutline, mdiFileDocument, mdiMapMarker, mdiOfficeBuilding, mdiCalendar, mdiEye } from "@mdi/js"; // Íconos adicionales
import FormControlV7 from '@/Components/FormControlV7.vue';
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';





const props = defineProps({
    titulo: String,
    documento: Object,
    routeName: String,
    empresas: Array,
    tipos_documento: Array,
    estados: Array,
    departamentos: Array,
    modalidades: Array
});

const form = useForm({
    nombre_documento: props.documento.nombre_documento,
    empresa_id: props.documento.empresa_id,
    tipo_de_documento_id: props.documento.tipo_de_documento_id,
    estado_id: props.documento.estado_id,
    departamento_id: props.documento.departamento_id,
    fecha_revalidacion: props.documento.fecha_revalidacion,
    fecha_vigencia: props.documento.fecha_vigencia,
    modalidad_id: props.documento.modalidades.map(mod => mod.id), // Para manejar múltiples modalidades
    //modalidad_id: props.documento.modalidades ? props.documento.modalidades.map(mod => mod.id) : [], // Asegurar que no sea undefined

    ruta_documento: props.ruta_documento,
    ruta_documento_anexo: props.ruta_documento_anexo
});

// Función para mostrar el PDF en SweetAlert2
const mostrarArchivo = (ruta) => {
    if (!ruta) {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'No se ha proporcionado una ruta válida para el archivo PDF.',
        });
        return;
    }
    Swal.fire({
        html: `
            <div style="width: 100%; height: 500px;">
                <iframe src="${ruta}" style="width: 100%; height: 100%;" frameborder="0"></iframe>
            </div>
        `,
        width: "80%",
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: "Cerrar",
        allowOutsideClick: true, 
        allowEscapeKey: true,
    });
};

const guardar1 = () => {
    console.log('Documento a enviar------:', props.documento);
    form.put(route(`${props.routeName}update`, props.documento.id));
};
const guardar = () => {
    router.post(route(`${props.routeName}update`, props.documento.id), {
        _method: 'PATCH',
        ...form
    }, {
        forceFormData: true
    });
};

</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main />
        <CardBox form @submit.prevent="guardar" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Nombre del Documento -->
                <FormField label="Nombre del documento" :error="form.errors.nombre_documento">
                    <FormControl
                        v-model="form.nombre_documento"
                        type="text"
                        placeholder="Nombre del documento"
                        :icon="mdiFileDocument"
                        required
                    />
                </FormField>

                <!-- Selector de Empresa -->
                <FormField label="Empresa" :error="form.errors.empresa_id">
                    <FormControl
                        v-model="form.empresa_id"
                        :options="empresas"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiOfficeBuilding"
                        placeholder="Selecciona una empresa"
                        required
                    />
                </FormField>

                <!-- Selector de Tipo de Documento -->
                <FormField label="Tipo de documento" :error="form.errors.tipo_de_documento_id">
                    <FormControl
                        v-model="form.tipo_de_documento_id"
                        :options="tipos_documento"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiFileDocument"
                        required
                    />
                </FormField>

                <!-- Selector de Estado -->
                <FormField label="Estado" :error="form.errors.estado_id">
                    <FormControl
                        v-model="form.estado_id"
                        :options="estados"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiMapMarker"
                        required
                    />
                </FormField>

                <!-- Selector de Departamento -->
                <FormField label="Departamento" :error="form.errors.departamento_id">
                    <FormControl
                        v-model="form.departamento_id"
                        :options="departamentos"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiMapMarker"
                        required
                    />
                </FormField>

                   <!-- Selector de Modalidad -->
                   <FormField label="Modalidad" :error="form.errors.modalidad_id">
                    <FormControlV7
                        v-model="form.modalidad_id"
                        :options="modalidades"
                        label-key="name"
                        value-key="id"
                    />
                </FormField>
                
                

                <!-- Fecha de Revalidación -->
                <FormField label="Fecha de Revalidación" :error="form.errors.fecha_revalidacion">
                    <FormControl
                        v-model="form.fecha_revalidacion"
                        type="date"
                        :icon="mdiCalendar"
                        required
                    />
                </FormField>

                <!-- Fecha de Vigencia -->
                <FormField label="Fecha de Vigencia" :error="form.errors.fecha_vigencia">
                    <FormControl
                        v-model="form.fecha_vigencia"
                        type="date"
                        :icon="mdiCalendar"
                        required
                    />
                </FormField>
                     <!-- Campo: Ruta Documento -->
                     <FormField label="Documento Principal" :error="form.errors.ruta_documento">
                    <FormControl
                        type="file"
                        @change="(e) => form.ruta_documento = e.target.files[0] "
                        accept="application/pdf"
                        required
                    />
                   
                </FormField>

                <!-- Campo: Ruta Documento Anexo -->
                <FormField label="Documento Anexo" :error="form.errors.ruta_documento_anexo">
                    <FormControl
                        type="file"
                        @change="(e) => form.ruta_documento_anexo = e.target.files[0] "
                        accept="application/pdf"
                        required
                    />
         
                </FormField>                 
                <!-- Botón para visualizar los archivos -->                
                <BaseButton @click="mostrarArchivo(`/storage/${documento.ruta_documento}`)"  :icon="mdiEye"  color="lightDark" label="Ver Documento Principal" />
                <BaseButton @click="mostrarArchivo(`/storage/${documento.ruta_documento_anexo}`)" :icon="mdiEye" color="lightDark" label="Ver Documento Anexo" />             
            
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton @click="guardar" type="submit" color="info" outline label="Actualizar" />
                    <BaseButton :href="route(`${props.routeName}index`)" type="reset" color="danger" outline label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
