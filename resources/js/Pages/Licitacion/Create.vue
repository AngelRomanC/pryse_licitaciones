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
import CatalogoRedirectButton from '@/Components/CatalogoRedirectButton.vue';
import MultiSelectEstados from '@/Components/MultiSelectEstados.vue';
import TransferList from '@/Components/TransferList.vue';
import { ref, watch } from 'vue'
import axios from 'axios'
import MultiSelectEmpresas from '@/Components/MultiSelectEmpresas.vue';
import DocumentSelector from '@/Components/DocumentSelector.vue'
import FormControlV7 from '@/Components/FormControlV7.vue'


const props = defineProps({
  empresas: Array,
  estados: Array,
  routeName: String,
  modalidades: Array,

});

const form = useForm({
  nombre: '',
  fecha: '',
  empresa_id: [],
  estados: [],
  archivos_legales: [],    // Para el select multiple
  archivos_tecnicos: [],    // Para el select multiple
  modalidades_id: [] 

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

        <!--     <TransferList :estados="estados" />  -->


      </div>



      <FormField label="Documentos Requeridos">
             <DocumentSelector
                :empresas="empresas"
                :modelValueEmpresas="form.empresa_id"
                v-model:modelValueTecnicos="form.archivos_tecnicos"
                v-model:modelValueLegales="form.archivos_legales"
                baseUrl="/storage/"
              />
      </FormField>




              <!-- Selector de Modalidad -->
                <FormField label="Modalidades" :error="form.errors.modalidades_id">
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                    <FormControlV7
                        v-model="form.modalidades_id"
                        :options="modalidades"
                        label-key="name"
                        value-key="id"
                    />
                        </div>
                        <CatalogoRedirectButton
                            catalog-route-name="modalidad"
                            return-route="licitacion.create"
                            :return-id="form.id"
                            label="Agregar nueva empresa"
                            :icon="mdiPlus"
                          />                
                    </div>
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