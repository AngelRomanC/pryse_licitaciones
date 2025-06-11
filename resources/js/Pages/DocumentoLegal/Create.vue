<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiFormatListChecks, mdiOfficeBuilding, mdiFileDocument, mdiMapMarker, mdiCalendar,mdiPlus } from "@mdi/js";
import FileUploader from '@/Components/FileUploader.vue';
import CatalogoRedirectButton from '@/Components/CatalogoRedirectButton.vue';


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

const urlParams = new URLSearchParams(window.location.search);
const redirectParam = urlParams.get('redirect');

const form = useForm({
    nombre_documento: 'Documento Legal',
    empresa_id: '',
    tipo_de_documento_id: '',
    //estado_id: '',
    departamento_id: '',
    fecha_revalidacion: '',
    fecha_vigencia: '',
    //modalidad_id: [],
    ruta_documento: [],
    ruta_documento_anexo: [],
    redirect: redirectParam, 
});

const handleSubmit = () => {    
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
                        disabled
                        class="bg-gray-100 cursor-not-allowed"
                        title="Campo no editable - Documento Legal fijo"

                    />
                </FormField>

                <!-- Selector de Empresa -->
                <FormField label="Empresa" :error="form.errors.empresa_id">
                    <div class="flex items-center gap-2">
                    <div class="flex-1">
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
                    </div>                    
                    <!-- Botón reutilizable -->
                    <CatalogoRedirectButton
                            catalog-route-name="empresa"
                            return-route="documento-legal.create"
                            :return-id="form.id"
                            label="Agregar nueva empresa"
                            :icon="mdiPlus"
                          />                
                    </div>
                </FormField>
                

                <!-- Selector de Tipo de Documento -->
                <FormField label="Documento" :error="form.errors.tipo_de_documento_id">                 
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <FormControl
                                v-model="form.tipo_de_documento_id"
                                :options="tipos_documento"
                                type="select"
                                label-key="nombre_documento"
                                value-key="id"
                                :icon="mdiFileDocument"
                                required
                            />
                        </div>            
                        <CatalogoRedirectButton
                            catalog-route-name="tipo-de-documento"
                            return-route="documento-legal.create"
                            :return-id="form.id"
                            label="Agregar nuevo Docuemento"
                            :icon="mdiPlus"
                        /> 
                    </div>
                </FormField>

                <!-- Selector de Departamento -->
                <FormField label="Departamento" :error="form.errors.departamento_id">
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <FormControl
                                v-model="form.departamento_id"
                                :options="departamentos"
                                type="select"
                                label-key="nombre_departamento"
                                value-key="id"
                                :icon="mdiOfficeBuilding"
                                required
                            />
                        </div>
                        <CatalogoRedirectButton
                            catalog-route-name="departamento"
                            return-route="documento-legal.create"
                            :return-id="form.id"
                            label="Agregar nueva empresa"
                            :icon="mdiPlus"
                          />                
                    </div>
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
                        :href="redirectParam || route(`${routeName}index`)" 
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