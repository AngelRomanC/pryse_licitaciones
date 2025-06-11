<script setup>
import { useForm,Link } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiFormatListChecks, mdiOfficeBuilding, mdiFileDocument, mdiMapMarker, mdiCalendar,mdiPlus } from "@mdi/js";
import FormControlV7 from '@/Components/FormControlV7.vue';
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
    nombre_documento: 'Documento Técnico',
    empresa_id: '',
    tipo_de_documento_id: '',
    estado_id: '',
    departamento_id: '',
    fecha_revalidacion: '',
    fecha_vigencia: '',
    modalidad_id: [],
    ruta_documento: [],
    ruta_documento_anexo: [],
    redirect: redirectParam,

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
                        disabled
                        class="bg-gray-100 cursor-not-allowed"
                        title="Campo no editable - Documento Técnico fijo"

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
                            return-route="documento.create"
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
                            return-route="documento.create"
                            :return-id="form.id"
                            label="Agregar nuevo Docuemento"
                            :icon="mdiPlus"
                        /> 
                    </div>
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
                            return-route="documento.create"
                            :return-id="form.id"
                            label="Agregar nueva empresa"
                            :icon="mdiPlus"
                          />                
                    </div>
                </FormField>

                <!-- Selector de Modalidad -->
                <FormField label="Modalidad" :error="form.errors.modalidad_id">
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                    <FormControlV7
                        v-model="form.modalidad_id"
                        :options="modalidades"
                        label-key="name"
                        value-key="id"
                    />
                        </div>
                        <CatalogoRedirectButton
                            catalog-route-name="modalidad"
                            return-route="documento.create"
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
                class="w-full border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors cursor-pointer"
                />
                

                <!-- Campo: Documentos Anexos -->
                <FileUploader 
                label="Documento(s) Anexo(s)" 
                v-model="form.ruta_documento_anexo"
                :error="form.errors.ruta_documento_anexo"
                accept="application/pdf"
                class="w-full border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition-colors cursor-pointer"
                                
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