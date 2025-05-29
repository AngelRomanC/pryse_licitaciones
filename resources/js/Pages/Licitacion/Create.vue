<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiOfficeBuilding, mdiFileDocument, mdiPlus, mdiMapMarker, mdiCalendar } from "@mdi/js";
import FileUploader from '@/Components/FileUploader.vue';
import CatalogoRedirectButton from '@/Components/CatalogoRedirectButton.vue';

import MultiSelectEstados from '@/Components/MultiSelectEstados.vue';
import TransferList from '@/Components/TransferList.vue';
import { ref, watch } from 'vue'
import axios from 'axios'
import MultiSelectEmpresas from '@/Components/MultiSelectEmpresas.vue';

import TransferFiles from '@/Components/TransferFiles.vue'
import DocumentSelector from '@/Components/DocumentSelector.vue'






const props = defineProps({
  empresas: Array,
  estados: Array,
  routeName: String,
  documentos_tecnicos: Array,
  documentos_legales: Array,

});

const form = useForm({
  nombre: '',
  fecha: '',
  empresa_id: [],
  estados: [],
  archivos_legales: [],    // Para el select multiple
  archivos_tecnicos: []    // Para el select multiple
});

const handleSubmit = () => {
  form.post(route(`${props.routeName}store`), {
    forceFormData: true,
  });
};

// const handleSubmit = () => {    
//     //form.post(route(`${props.routeName}store`)); // Corregida sintaxis de ruta
//         form.post(route(`${props.routeName}store`));



// };

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
watch(() => form.empresa_id, async (nuevasEmpresas, oldEmpresas) => {
  // Si no hay empresas seleccionadas, limpiar todo
  if (!nuevasEmpresas || nuevasEmpresas.length === 0) {
    archivosTecnicos.value = [];
    archivosLegales.value = [];
    form.archivos_tecnicos = [];
    form.archivos_legales = [];
    return;
  }

  // Obtener empresas que se eliminaron
  const empresasRemovidas = oldEmpresas?.filter(e => !nuevasEmpresas.includes(e)) || [];
  
  // Si solo estamos agregando empresas (no quitando), no limpiar selecciones
  if (empresasRemovidas.length === 0) {
    // Solo cargar documentos de las nuevas empresas (las que no estaban antes)
    const nuevasEmpresasACargar = oldEmpresas 
      ? nuevasEmpresas.filter(e => !oldEmpresas.includes(e))
      : nuevasEmpresas;
    
    for (const empresaId of nuevasEmpresasACargar) {
      const response = await axios.get(`/empresa/${empresaId}/documentos`);
      const documentos = response.data.documentos_tecnicos;
      const documentosLegales = response.data.documentos_legales;

      const nuevosTecnicos = documentos.flatMap(doc =>
        doc.archivos.map(archivo => ({
          id: archivo.id,
          nombre: archivo.nombre_original,
          url: archivo.ruta_archivo,
          documento: doc.tipo_de_documento.nombre_documento,
          empresa_id: empresaId // Agregar referencia a la empresa
        }))
      );

      const nuevosLegales = documentosLegales.flatMap(doc =>
        doc.archivos.map(archivo => ({
          id: archivo.id,
          nombre: archivo.nombre_original,
          url: archivo.ruta_archivo,
          documento: doc.tipo_de_documento.nombre_documento,
          empresa_id: empresaId // Agregar referencia a la empresa
        }))
      );

      archivosTecnicos.value.push(...nuevosTecnicos);
      archivosLegales.value.push(...nuevosLegales);
    }
    return;
  }

  // Si estamos quitando empresas:
  
  // 1. Primero cargar todos los documentos de las empresas actuales
  archivosTecnicos.value = [];
  archivosLegales.value = [];
  
  for (const empresaId of nuevasEmpresas) {
    const response = await axios.get(`/empresa/${empresaId}/documentos`);
    const documentos = response.data.documentos_tecnicos;
    const documentosLegales = response.data.documentos_legales;

    const nuevosTecnicos = documentos.flatMap(doc =>
      doc.archivos.map(archivo => ({
        id: archivo.id,
        nombre: archivo.nombre_original,
        url: archivo.ruta_archivo,
        documento: doc.tipo_de_documento.nombre_documento,
        empresa_id: empresaId
      }))
    );

    const nuevosLegales = documentosLegales.flatMap(doc =>
      doc.archivos.map(archivo => ({
        id: archivo.id,
        nombre: archivo.nombre_original,
        url: archivo.ruta_archivo,
        documento: doc.tipo_de_documento.nombre_documento,
        empresa_id: empresaId
      }))
    );

    archivosTecnicos.value.push(...nuevosTecnicos);
    archivosLegales.value.push(...nuevosLegales);
  }

  // 2. Filtrar las selecciones para mantener solo las de empresas que siguen seleccionadas
  if (form.archivos_tecnicos.length > 0) {
    const archivosTecnicosEmpresasActuales = archivosTecnicos.value.map(a => a.id);
    form.archivos_tecnicos = form.archivos_tecnicos.filter(
      id => archivosTecnicosEmpresasActuales.includes(id)
    );
  }

  if (form.archivos_legales.length > 0) {
    const archivosLegalesEmpresasActuales = archivosLegales.value.map(a => a.id);
    form.archivos_legales = form.archivos_legales.filter(
      id => archivosLegalesEmpresasActuales.includes(id)
    );
  }
}, { immediate: true });

</script>

<template>
  <LayoutMain title="Crear Licitación">
    <SectionTitleLineWithButton :icon="mdiBallotOutline" title="Crear Licitación" main />

    <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Nombre -->
        <FormField label="Nombre" :error="form.errors.nombre">
          <FormControl v-model="form.nombre" type="text" placeholder="Nombre de la licitación" required />
        </FormField>

        <!-- Fecha de Licitación -->
        <FormField label="Fecha de Licitación" :error="form.errors.fecha">
          <FormControl v-model="form.fecha" type="date" :icon="mdiCalendar" required />
        </FormField>

        <!-- Empresa -->
        <FormField label="Empresas" :error="form.errors.empresa_id">
          <div class="flex items-center gap-2">
            <div class="flex-1">
              <MultiSelectEmpresas :empresas="empresas" v-model="form.empresa_id" :icon="mdiOfficeBuilding"
                placeholder="Seleccione empresas participantes" search-placeholder="Buscar por nombre"
                no-options-text="No se encontraron empresas" />
            </div>
            <CatalogoRedirectButton catalog-route-name="empresa" return-route="empresa.create" :return-id="form.id"
              label="Agregar empresa" :icon="mdiPlus" />
          </div>
        </FormField>

        <FormField label="Estados" :error="form.errors.estados">
          <MultiSelectEstados v-model="form.estados" :estados="estados" />
        </FormField>

        <!--     <TransferList :estados="estados" /> 

               
          


              



<!-- Transferencias de archivos legales
<FormField label="Documentos Legales" :error="form.errors.archivos_legales">
  <TransferFiles
    :archivos="archivosLegales"
    v-model="form.archivos_legales"
  />
</FormField>

<!-- Transferencias de archivos técnicos 
<FormField label="Documentos Técnicos" :error="form.errors.archivos_tecnicos">
  <TransferFiles
    :archivos="archivosTecnicos"
    v-model="form.archivos_tecnicos"
  />
</FormField>  -->




      </div>



      <FormField label="Documentos Requeridos">
        <DocumentSelector 
        :documentosTecnicos="archivosTecnicos" 
        :documentosLegales="archivosLegales"
        v-model:modelValueTecnicos="form.archivos_tecnicos" 
        v-model:modelValueLegales="form.archivos_legales" />
      </FormField>





      <template #footer>
        <BaseButtons>
          <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Crear" />
          <BaseButton :href="route('licitacion.index')" type="button" color="danger" outline label="Cancelar" />
        </BaseButtons>
      </template>
    </CardBox>
  </LayoutMain>
</template>