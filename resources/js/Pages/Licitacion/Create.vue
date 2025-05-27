<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiOfficeBuilding, mdiFileDocument, mdiPlus,mdiMapMarker, mdiCalendar } from "@mdi/js";
import FileUploader from '@/Components/FileUploader.vue';
import CatalogoRedirectButton from '@/Components/CatalogoRedirectButton.vue';

import MultiSelectEstados from '@/Components/MultiSelectEstados.vue';
import TransferList  from '@/Components/TransferList.vue';
import { ref, watch } from 'vue'
import axios from 'axios'
import MultiSelectEmpresas  from '@/Components/MultiSelectEmpresas.vue';




const props = defineProps({
    empresas: Array,
    estados: Array,
    routeName: String,
    documentos_tecnicos: Array,
    documentos_legales: Array,

});

const form = useForm({
    nombre: '',
    fecha_licitacion: '',
    empresa_id: [],
    estados: [],   
    archivos_legales: [],    // Para el select multiple
    archivos_tecnicos: []    // Para el select multiple
});

const handleSubmit = () => {
    form.post(route('licitacion.store'), {
        forceFormData: true,
    });
};

const archivosTecnicos = ref([])
const archivosLegales = ref([])



// Escuchar cambios en empresa_id
// watch(() => form.empresa_id, async (nuevoId) => {
//     if (!nuevoId) return;

//     const response = await axios.get(`/empresa/${nuevoId}/documentos`);
//     const documentos = response.data.documentos_tecnicos;
//     const documentosLegales = response.data.documentos_legales;


//     // Recolectar todos los archivos de esos documentos
//     archivosTecnicos.value = documentos.flatMap(doc => 
//         doc.archivos.map(archivo => ({
//             id: archivo.id,
//             nombre: archivo.nombre_original,
//             url: archivo.ruta_archivo,
//             documento: doc.tipo_de_documento.nombre_documento

//         }))
//     );

//     archivosLegales.value = documentosLegales.flatMap(doc => 
//         doc.archivos.map(archivo => ({
//             id: archivo.id,
//             nombre: archivo.nombre_original,
//             url: archivo.ruta_archivo,
//             documento: doc.tipo_de_documento.nombre_documento

//         }))
//     );
// });
watch(() => form.empresa_id, async (nuevasEmpresas) => {
  //if (!nuevasEmpresas || nuevasEmpresas.length === 0) return;
  if (!nuevasEmpresas || nuevasEmpresas.length === 0) {
    archivosTecnicos.value = [];
    archivosLegales.value = [];
    return;
  }

  archivosTecnicos.value = [];
  archivosLegales.value = [];

  for (const empresaId of nuevasEmpresas) {
    const response = await axios.get(`/empresa/${empresaId}/documentos`);
    const documentos = response.data.documentos_tecnicos;
    const documentosLegales = response.data.documentos_legales;

    // Agregar archivos técnicos
    const nuevosTecnicos = documentos.flatMap(doc =>
      doc.archivos.map(archivo => ({
        id: archivo.id,
        nombre: archivo.nombre_original,
        url: archivo.ruta_archivo,
        documento: doc.tipo_de_documento.nombre_documento
      }))
    );

    // Agregar archivos legales
    const nuevosLegales = documentosLegales.flatMap(doc =>
      doc.archivos.map(archivo => ({
        id: archivo.id,
        nombre: archivo.nombre_original,
        url: archivo.ruta_archivo,
        documento: doc.tipo_de_documento.nombre_documento
      }))
    );

    archivosTecnicos.value.push(...nuevosTecnicos);
    archivosLegales.value.push(...nuevosLegales);
  }
});


</script>

<template>
    <LayoutMain title="Crear Licitación">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" title="Crear Licitación" main />

        <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Nombre -->
                <FormField label="Nombre" :error="form.errors.nombre">
                    <FormControl
                        v-model="form.nombre"
                        type="text"
                        placeholder="Nombre de la licitación"
                        required
                    />
                </FormField>

                <!-- Fecha de Licitación -->
                <FormField label="Fecha de Licitación" :error="form.errors.fecha_licitacion">
                    <FormControl
                        v-model="form.fecha_licitacion"
                        type="date"
                        :icon="mdiCalendar"
                        required
                    />
                </FormField>

                <!-- Empresa -->
                <FormField label="Empresas" :error="form.errors.empresa_id">
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                          <MultiSelectEmpresas
                                :empresas="empresas"
                                v-model="form.empresa_id"
                                :icon="mdiOfficeBuilding"
                                placeholder="Seleccione empresas participantes"
                                search-placeholder="Buscar por nombre"
                                no-options-text="No se encontraron empresas"
                            />
                        </div>
                        <CatalogoRedirectButton
                            catalog-route-name="empresa"
                            return-route="empresa.create"
                            :return-id="form.id"
                            label="Agregar empresa"
                            :icon="mdiPlus"

                        />
                    </div>
                </FormField>              

                <FormField label="Estados" :error="form.errors.estados">
                  <MultiSelectEstados 
                    v-model="form.estados"
                    :estados="estados"
                    />
                </FormField>

                <TransferList :estados="estados" />

               




              

<select v-model="form.archivos_legales" multiple>
  <option v-for="archivo in archivosLegales" :key="archivo.id" :value="archivo.id">
    {{ archivo.documento }} - {{ archivo.nombre }}
  </option>
</select>

<select v-model="form.archivos_tecnicos" multiple>
  <option v-for="archivo in archivosTecnicos" :key="archivo.id" :value="archivo.id">
    {{ archivo.documento }} - {{ archivo.nombre }}
  </option>
</select>









                

            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton 
                        type="submit" 
                        color="info" 
                        label="Crear Licitación"
                        :disabled="form.processing"
                        :loading="form.processing"
                    />
                    <BaseButton 
                        :href="route('licitacion.index')" 
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