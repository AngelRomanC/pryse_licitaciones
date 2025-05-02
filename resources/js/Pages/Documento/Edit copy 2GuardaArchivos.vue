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
import { mdiBallotOutline, mdiFileDocument, mdiMapMarker, mdiOfficeBuilding, mdiCalendar, mdiEye, mdiTrashCan } from "@mdi/js";
import FormControlV7 from '@/Components/FormControlV7.vue';
import FileUploader from '@/Components/FileUploader.vue';
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
    modalidades: Array,
    archivosPrincipales: Array,
    archivosAnexos: Array
});

// Archivos existentes
const archivosPrincipales = ref(props.archivosPrincipales || []);
const archivosAnexos = ref(props.archivosAnexos || []);

// Formulario principal
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
    archivos_a_eliminar: []
});

// Mostrar PDF
const mostrarArchivo = (ruta) => {
    if (!ruta) {
        Swal.fire('Error', 'No hay archivo para mostrar', 'error');
        return;
    }
    window.open(`/storage/${ruta}`, '_blank');
};

// Eliminar archivo
const eliminarArchivo = (id, tipo) => {
    Swal.fire({
        title: '¿Eliminar archivo?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            form.archivos_a_eliminar.push(id);
            if (tipo === 'principal') {
                archivosPrincipales.value = archivosPrincipales.value.filter(a => a.id !== id);
            } else {
                archivosAnexos.value = archivosAnexos.value.filter(a => a.id !== id);
            }
            Swal.fire('Eliminado', 'El archivo ha sido marcado para eliminación', 'success');
        }
    });
};

// Guardar cambios
const guardar = () => {
    const formData = new FormData();
    
    // Agregar campos normales
    Object.keys(form.data()).forEach(key => {
        if (key !== 'nuevos_documentos_principales' && key !== 'nuevos_documentos_anexos') {
            if (Array.isArray(form[key])) {
                form[key].forEach((item, index) => {
                    formData.append(`${key}[${index}]`, item);
                });
            } else {
                formData.append(key, form[key]);
            }
        }
    });
    
    // Agregar archivos principales
    form.nuevos_documentos_principales.forEach((file, index) => {
        formData.append(`nuevos_documentos_principales[${index}]`, file);
    });
    
    // Agregar archivos anexos
    form.nuevos_documentos_anexos.forEach((file, index) => {
        formData.append(`nuevos_documentos_anexos[${index}]`, file);
    });
    
    // Enviar con método PATCH
    router.post(route(`${props.routeName}update`, props.documento.id), {
        _method: 'PATCH',
        ...form.data(),
    }, {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire('Éxito', 'Documento actualizado correctamente', 'success');
        },
        onError: (errors) => {
            Swal.fire('Error', 'Hubo un problema al actualizar', 'error');
        }
    });
};

// Manejar nuevos archivos
const agregarArchivos = (files, tipo) => {
    if (tipo === 'principal') {
        form.nuevos_documentos_principales = [...form.nuevos_documentos_principales, ...files];
    } else {
        form.nuevos_documentos_anexos = [...form.nuevos_documentos_anexos, ...files];
    }
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

                <!-- Documentos Principales -->
                <div class="col-span-2">
                    <h3 class="text-lg font-medium mb-2">Documentos Principales</h3>
                    <div v-if="archivosPrincipales.length > 0" class="mb-4 space-y-2">
                        <div v-for="archivo in archivosPrincipales" :key="archivo.id" 
                             class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium truncate flex-1">{{ archivo.nombre_original }}</span>
                            <div class="flex space-x-2">
                                <BaseButton 
                                    @click="mostrarArchivo(archivo.ruta_archivo)"
                                    :icon="mdiEye"
                                    color="info"
                                    small
                                    label="Ver"
                                />
                                <BaseButton 
                                    @click="eliminarArchivo(archivo.id, 'principal')"
                                    :icon="mdiTrashCan"
                                    color="danger"
                                    small
                                    label="Eliminar"
                                />
                            </div>
                        </div>
                    </div>
                    <FileUploader 
                        label="Agregar documentos principales"
                        accept="application/pdf"
                        multiple
                        @files-selected="(files) => agregarArchivos(files, 'principal')"
                    />
                    <div v-if="form.nuevos_documentos_principales.length > 0" class="mt-2">
                        <p class="text-sm text-gray-600">Nuevos archivos a subir:</p>
                        <ul class="list-disc pl-5 text-sm">
                            <li v-for="(file, index) in form.nuevos_documentos_principales" :key="index">
                                {{ file.name }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Documentos Anexos -->
                <div class="col-span-2">
                    <h3 class="text-lg font-medium mb-2">Documentos Anexos</h3>
                    <div v-if="archivosAnexos.length > 0" class="mb-4 space-y-2">
                        <div v-for="archivo in archivosAnexos" :key="archivo.id" 
                             class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium truncate flex-1">{{ archivo.nombre_original }}</span>
                            <div class="flex space-x-2">
                                <BaseButton 
                                    @click="mostrarArchivo(archivo.ruta_archivo)"
                                    :icon="mdiEye"
                                    color="info"
                                    small
                                    label="Ver"
                                />
                                <BaseButton 
                                    @click="eliminarArchivo(archivo.id, 'anexo')"
                                    :icon="mdiTrashCan"
                                    color="danger"
                                    small
                                    label="Eliminar"
                                />
                            </div>
                        </div>
                    </div>
                    <FileUploader 
                        label="Agregar documentos anexos"
                        accept="application/pdf"
                        multiple
                        @files-selected="(files) => agregarArchivos(files, 'anexo')"
                    />
                    <div v-if="form.nuevos_documentos_anexos.length > 0" class="mt-2">
                        <p class="text-sm text-gray-600">Nuevos archivos a subir:</p>
                        <ul class="list-disc pl-5 text-sm">
                            <li v-for="(file, index) in form.nuevos_documentos_anexos" :key="index">
                                {{ file.name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton 
                        type="submit" 
                        color="info" 
                        outline 
                        label="Actualizar" 
                        :disabled="form.processing" 
                        :loading="form.processing"
                        @click="guardar"
                    />
                    <BaseButton 
                        :href="route(`${props.routeName}index`)" 
                        type="button" 
                        color="danger" 
                        outline 
                        label="Cancelar" 
                    />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>