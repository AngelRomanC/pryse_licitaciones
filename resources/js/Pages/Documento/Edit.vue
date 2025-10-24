<script setup>
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import { mdiFileEdit , mdiFileDocument, mdiMapMarker, mdiOfficeBuilding, mdiCalendar, mdiEye, mdiTrashCan, mdiPlus } from "@mdi/js";
import FormControlV7 from '@/Components/FormControlV7.vue';
import FileUploader from '@/Components/FileUploader.vue';
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';
import LoadingOverlay from '@/components/LoadingOverlay.vue';

const isUploading = ref(false);

const props = defineProps({
    titulo: String,
    documento: Object,
    routeName: String,
    empresas: Array,
    tipos_documento: Array,
    estados: Array,
    departamentos: Array,
    modalidades: Array,
    archivosPrincipales: Array,
    archivosAnexos: Array,

});
// Preparar archivos existentes
const archivosPrincipales = ref(props.archivosPrincipales || []);
const archivosAnexos = ref(props.archivosAnexos || []);

const urlParams = new URLSearchParams(window.location.search);
const redirectParam = urlParams.get('redirect');
const form = useForm({
    nombre_documento: props.documento.nombre_documento,
    empresa_id: props.documento.empresa_id,
    tipo_de_documento_id: props.documento.tipo_de_documento_id,
    estado_id: props.documento.estado_id,
    departamento_id: props.documento.departamento_id,
    fecha_revalidacion: props.documento.fecha_revalidacion,
    fecha_vigencia: props.documento.fecha_vigencia,
    modalidad_id: props.documento.modalidades.map(mod => mod.id),
    nuevos_documentos_principales: [],
    nuevos_documentos_anexos: [],
    archivos_a_eliminar: [],

});

// Función para mostrar el PDF
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
                <iframe src="/storage/${ruta}" style="width: 100%; height: 100%;" frameborder="0"></iframe>
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

// Marcar archivo para eliminación
const marcarParaEliminar = (id, tipo) => {
    form.archivos_a_eliminar.push(id);
    
    if (tipo === 'principal') {
        archivosPrincipales.value = archivosPrincipales.value.filter(a => a.id !== id);
    } else {
        archivosAnexos.value = archivosAnexos.value.filter(a => a.id !== id);
    }
};

// Función para guardar
const guardar2 = () => {
    // Crear FormData para enviar archivos
    const formData = new FormData();
    
    // Agregar campos normales
    Object.keys(form.data()).forEach(key => {
        if (key !== 'nuevos_documentos_principales' && key !== 'nuevos_documentos_anexos') {
            formData.append(key, form[key]);
        }
    });
    
    // Agregar nuevos archivos principales
    form.nuevos_documentos_principales.forEach((file, index) => {
        formData.append(`nuevos_documentos_principales[${index}]`, file);
    });
    
    // Agregar nuevos archivos anexos
    form.nuevos_documentos_anexos.forEach((file, index) => {
        formData.append(`nuevos_documentos_anexos[${index}]`, file);
    });
    
    // // Enviar el formulario
    // router.post(route(`${props.routeName}update`, props.documento.id), formData, {
    //     forceFormData: true,
    //     onSuccess: () => {
        router.post(route(`${props.routeName}update`, props.documento.id), {
        _method: 'PATCH',
        ...form.data(),
    }, {
        forceFormData: true,
      
    });
};
const guardar = () => {

    isUploading.value = true;

    const formData = new FormData();

    // Serializar todos los campos excepto los archivos
    Object.entries(form.data()).forEach(([key, value]) => {
        if (key === 'nuevos_documentos_principales' || key === 'nuevos_documentos_anexos') return;

        if (Array.isArray(value)) {
            value.forEach(v => formData.append(`${key}[]`, v));
        } else {
            formData.append(key, value);
        }
    });

    // Archivos principales
    form.nuevos_documentos_principales.forEach((file, index) => {
        formData.append(`nuevos_documentos_principales[${index}]`, file);
    });

    // Archivos anexos
    form.nuevos_documentos_anexos.forEach((file, index) => {
        formData.append(`nuevos_documentos_anexos[${index}]`, file);
    });

    // PATCH override
    formData.append('_method', 'PATCH');

    // Usar router.post pero enviando FormData REAL
    router.post(route(`${props.routeName}update`, props.documento.id), formData, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            isUploading.value = false;
        },
        onError: (errors) => {
                isUploading.value = false;
                form.setError(errors); // <- ASÍ se vinculan manualmente

        }
    });
};

// Manejar nuevos archivos principales
const agregarDocumentosPrincipales = (files) => {
    form.nuevos_documentos_principales = [...form.nuevos_documentos_principales, ...files];
};

// Manejar nuevos archivos anexos
const agregarDocumentosAnexos = (files) => {
    form.nuevos_documentos_anexos = [...form.nuevos_documentos_anexos, ...files];
};
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiFileEdit " :title="titulo" main />
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
                        disabled
                        class="bg-gray-100 cursor-not-allowed"
                        title="Campo no editable - Documento Técnico fijo"
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
                <FormField label="Documento" :error="form.errors.tipo_de_documento_id">
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
                        :disabled="$page.props.auth.active_role !== 'Admin'"
                        required
                        :title="!($page.props.auth.active_role === 'Admin') && !!form.departamento_id ? 'Campo bloqueado: solo Admin puede modificarlo' : ''"

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

                <!-- Documentos Principales Existentes -->
                <div>
                    <div v-if="archivosPrincipales.length > 0" class="mb-4">
                        <h3 class="text-lg font-medium mb-2">Documentos Principales</h3>
                        <div v-for="archivo in archivosPrincipales" :key="archivo.id" class="flex items-center justify-between p-2 border rounded mb-2">
                            <span class="truncate">{{ archivo.nombre_original }}</span>
                            <div class="flex space-x-2">
                                <BaseButton 
                                    @click="mostrarArchivo(archivo.ruta_archivo)" 
                                    :icon="mdiEye" 
                                    color="info" 
                                    small
                                    title="Ver"
                                />
                                <BaseButton 
                                    @click="marcarParaEliminar(archivo.id, 'principal')" 
                                    :icon="mdiTrashCan" 
                                    color="danger" 
                                    small
                                    title="Eliminar"
                                />
                            </div>
                        </div>
                      
                    </div>
                    <FileUploader 
                        label="Agregar nuevos documentos principales" 
                        v-model="form.nuevos_documentos_principales"
                        :error="form.errors.nuevos_documentos_principales"
                        accept="application/pdf"
                        multiple
                        @files-selected="agregarDocumentosPrincipales"
                        class="w-full border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors cursor-pointer"

                        />
                   </div>

                      
               
                <!-- Documentos Anexos Existentes -->
                 <div>
                    <div v-if="archivosAnexos.length > 0" class="mb-4">
                        <h3 class="text-lg font-medium mb-2">Documentos Anexos</h3>
                        <div v-for="archivo in archivosAnexos" :key="archivo.id" class="flex items-center justify-between p-2 border rounded mb-2">
                            <span class="truncate">{{ archivo.nombre_original }}</span>
                            <div class="flex space-x-2">
                                <BaseButton 
                                    @click="mostrarArchivo(archivo.ruta_archivo)" 
                                    :icon="mdiEye" 
                                    color="info" 
                                    small
                                    title="Ver"
                                />
                                <BaseButton 
                                    @click="marcarParaEliminar(archivo.id, 'anexo')" 
                                    :icon="mdiTrashCan" 
                                    color="danger" 
                                    small
                                    title="Eliminar"
                                />
                            </div>
                        </div>
                     
                    </div>  
                    <FileUploader 
                        label="Agregar nuevos documentos anexos" 
                        v-model="form.nuevos_documentos_anexos"
                        :error="form.errors.nuevos_documentos_anexos"
                        accept="application/pdf"
                        multiple
                        @files-selected="agregarDocumentosAnexos"
                        class="w-full border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors cursor-pointer"

                    />
                </div>               
                
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton  @click="guardar" type="submit" color="info" outline label="Actualizar" :disabled="form.processing" />
                    <BaseButton :href="redirectParam || route(`${routeName}index`)" type="button" color="danger" outline label="Cancelar" />
                    
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
 <LoadingOverlay :visible="isUploading" title="Actualizando dato(s)..." subtitle="Por favor, no cierres esta ventana." />

</template>