<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiFormatListChecks, mdiOfficeBuilding, mdiFileDocument, mdiMapMarker, mdiCalendar } from "@mdi/js";
import FormControlV7 from '@/Components/FormControlV7.vue';
import FileUploader from '@/Components/FileUploader.vue';

const props = defineProps({
    titulo: String,
    documento: Object,
    routeName: String,
    empresas: Array,       // Debe ser Array
    tipos_documento: Array, // Debe ser Array
    estados: Array,         // Debe ser Array
    departamentos: Array,   // Debe ser Array
    modalidades: Array      // Debe ser Array
});

const form = useForm({
    nombre_documento: 'Documento Técnico',
    empresa_id: '',
    tipo_de_documento_id: '',
    estado_id: '',
    departamento_id: '',
    fecha_revalidacion: '',
    fecha_vigencia: '',
    modalidad_id: [],
    ruta_documento: [],
    ruta_documento_anexo: []
});

const handleSubmit = () => {    
    //form.post(route(`${props.routeName}store`)); // Corregida sintaxis de ruta
    form.post(route(`${props.routeName}store`));

};
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main/>

        <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Nombre del Documento -->
                <FormField label="Nombre del documento" :error="form.errors.nombre_documento">
                    <FormControl
                        v-model="form.nombre_documento"
                        type="text"
                        placeholder="Nombre del documento"
                        :icon="mdiFormatListChecks"
                        required
                    />
                </FormField>

                <!-- Selector de Empresa -->
                <FormField label="Empresa" :error="form.errors.empresa_id">
                    <FormControl
                        v-model="form.empresa_id"
                        :options="empresas"
                        type="select"
                        label-key="nombre"
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
                        label-key="nombre_documento"
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
                        label-key="nombre"
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
                        label-key="nombre_departamento"
                        value-key="id"
                        :icon="mdiOfficeBuilding"
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
                        @change="(e)=>form.ruta_documento = e.target.files[0]"
                        accept="application/pdf"
                        required
                    />
                </FormField>

                <!-- Campo: Ruta Documento Anexo -->
                <FormField label="Documento Anexo" :error="form.errors.ruta_documento_anexo">
                    <FormControl
                        type="file"
                        @change="(e)=>form.ruta_documento_anexo = e.target.files[0]"
                        accept="application/pdf"
                        required
                        
                    />         
                </FormField>   
                
                <!-- Campo: Documentos Principales -->
        <FileUploader 
          label="Documento(s) Principal(es)" 
          v-model="form.ruta_documento"
          :error="form.errors.ruta_documento"
          accept="application/pdf"
          multiple
        />

        <!-- Campo: Documentos Anexos -->
        <FileUploader 
          label="Documento(s) Anexo(s)" 
          v-model="form.ruta_documento_anexo"
          :error="form.errors.ruta_documento_anexo"
          accept="application/pdf"
        />
               
                        
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Crear" />

                    <BaseButton 
                        :href="route(`${routeName}index`)" 
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