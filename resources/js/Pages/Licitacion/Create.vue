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
import Vista2 from '@/Components/vista2.vue'
import Swal from 'sweetalert2';
import Vista3 from '@/Components/vista3.vue'
import LoadingOverlay from '@/components/LoadingOverlay.vue';


const isUploading = ref(false)


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

// const handleSubmit = async () => {
//   try {
//     const response = await axios.post(route('licitacion.verificarModalidades'), {
//       empresa_id: form.empresa_id,
//       modalidades_id: form.modalidades_id,
//     });

//     const errores = response.data.errores;
//     const esPlural = errores.length > 1;

//     if (errores.length > 0) {
//       const { isConfirmed } = await Swal.fire({
//         title: `<strong>${esPlural ? 'Empresas con documentos faltantes' : 'Empresa con documentos faltantes'}</strong>`,
//         icon: 'warning',
//         html: `
//           <div class="text-left">
//             <p class="text-gray-700 mb-4">
//               ${esPlural ? 'Las siguientes empresas no tienen' : 'La siguiente empresa no tiene'} documentos técnicos para las modalidades seleccionadas:
//             </p>
            
//             <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 max-h-[300px] overflow-y-auto">
//               ${errores.map(e => `
//                 <div class="flex items-start mb-3">
//                   <svg class="h-5 w-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
//                     <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
//                   </svg>
//                   <span class="text-gray-800 break-words">${e}</span>
//                 </div>
//               `).join('')}
//             </div>
            
//             <p class="text-sm text-gray-600">
//               Puedes continuar con la licitación, pero ${esPlural ? 'estas empresas no podrán' : 'esta empresa no podrá'} participar en las modalidades mencionadas.
//             </p>
//           </div>
//         `,
//         showCancelButton: true,
//         confirmButtonText: 'Continuar',
//         cancelButtonText: 'Revisar documentos',
//         confirmButtonColor: '#4CAF50',
//         cancelButtonColor: '#F44336',
//         focusConfirm: false,
//         width: '650px',
//         scrollbarPadding: false,
//         customClass: {
//           popup: 'rounded-lg shadow-xl border-t-4 border-yellow-500',
//           title: 'text-xl text-gray-800 mb-2',
//           htmlContainer: 'text-left',
//           confirmButton: 'px-4 py-2 rounded-md font-medium hover:bg-green-600 transition-colors',
//           cancelButton: 'px-4 py-2 rounded-md font-medium hover:bg-red-600 transition-colors ml-3',
//           container: 'scrollbar-gutter-stable'
//         },
//         buttonsStyling: false
//       });

//       if (!isConfirmed) return;
//     }

//     form.post(route(`${props.routeName}store`), {
//       forceFormData: true,
//     });

//   } catch (error) {
//     console.error('Error al verificar modalidades', error);
//     Swal.fire({
//       title: 'Error',
//       text: 'Ocurrió un error al verificar las modalidades',
//       icon: 'error',
//       confirmButtonColor: '#2196F3',
//       confirmButtonText: 'Entendido'
//     });
//   }
// };

const handleSubmit = async () => {
  try {
    isUploading.value = true; // Mostrar overlay al iniciar

    // Verificar si solo está seleccionada "Ninguna"
    const soloNinguna = form.modalidades_id.length === 1 && 
      props.modalidades.some(mod => 
        form.modalidades_id.includes(mod.id) && 
        mod.name === 'Ninguna'
      );

    // Si no es solo "Ninguna", hacer la verificación normal
    if (!soloNinguna) {
      // Filtrar para excluir "Ninguna" de las verificaciones
      const modalidadesAVerificar = props.modalidades.filter(
        mod => form.modalidades_id.includes(mod.id) && 
        mod.name !== 'Ninguna'
      ).map(mod => mod.id);

      const response = await axios.post(route('licitacion.verificarModalidades'), {
        empresa_id: form.empresa_id,
        modalidades_id: modalidadesAVerificar, // Solo las modalidades que no son "Ninguna"
      });

      const errores = response.data.errores;
      const esPlural = errores.length > 1;

      if (errores.length > 0) {
        const { isConfirmed } = await Swal.fire({
          title: `<strong>${esPlural ? 'Empresas con documentos faltantes' : 'Empresa con documentos faltantes'}</strong>`,
          icon: 'warning',
          html: `
            <div class="text-left">
              <p class="text-gray-700 mb-4">
                ${esPlural ? 'Las siguientes empresas no tienen' : 'La siguiente empresa no tiene'} documentos técnicos para las modalidades seleccionadas:
              </p>
              
              <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 max-h-[300px] overflow-y-auto">
                ${errores.map(e => `
                  <div class="flex items-start mb-3">
                    <svg class="h-5 w-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-800 break-words">${e}</span>
                  </div>
                `).join('')}
              </div>
              
              <p class="text-sm text-gray-600">
                ${form.modalidades_id.length > modalidadesAVerificar.length 
                  ? 'Nota: La modalidad "Ninguna" no requiere documentos. ' 
                  : ''}
                Puedes continuar con la licitación, pero ${esPlural ? 'estas empresas no podrán' : 'esta empresa no podrá'} participar en las modalidades mencionadas.
              </p>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'Continuar',
          cancelButtonText: 'Revisar documentos',
          confirmButtonColor: '#4CAF50',
          cancelButtonColor: '#F44336',
          focusConfirm: false,
          width: '650px',
          scrollbarPadding: false,
          customClass: {
            popup: 'rounded-lg shadow-xl border-t-4 border-yellow-500',
            title: 'text-xl text-gray-800 mb-2',
            htmlContainer: 'text-left',
            confirmButton: 'px-4 py-2 rounded-md font-medium hover:bg-green-600 transition-colors',
            cancelButton: 'px-4 py-2 rounded-md font-medium hover:bg-red-600 transition-colors ml-3',
            container: 'scrollbar-gutter-stable'
          },
          buttonsStyling: false
        });

        //if (!isConfirmed) return;
        if (!isConfirmed) {
          isUploading.value = false;
          return;
        }
      }
    }

    // form.post(route(`${props.routeName}store`), {
    //   forceFormData: true,
    // });
    //form.post(route(`${props.routeName}store`));
    form.post(route(`${props.routeName}store`), {
      onFinish: () => {
        isUploading.value = false;
      },      
    });


  } catch (error) {
    isUploading.value = false; // Ocultar overlay si falla
    console.error('Error al verificar modalidades', error);
    Swal.fire({
      title: 'Error',
      text: 'Ocurrió un error al verificar las modalidades',
      icon: 'error',
      confirmButtonColor: '#2196F3',
      confirmButtonText: 'Entendido'
    });
  }
};

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
             <!-- Para documentos técnicos 
              
             <Vista2
                :empresas="empresas"
                :modelValueEmpresas="form.empresa_id"
                v-model="form.archivos_tecnicos"
                type="tecnico"
                baseUrl="/storage/"
              />

                <!-- Para documentos legales 
                <vista2
                  :empresas="empresas"
                  :modelValueEmpresas="form.empresa_id"
                  v-model="form.archivos_legales"
                  type="legal"
                  baseUrl="/storage/"
                /> -->



                  <Vista3
                    :empresas="empresas"
                    :modelValueEmpresas="form.empresa_id"
                    v-model="form.archivos_tecnicos"
                    type="tecnico"
                    baseUrl="/storage/"
                  />

                  <Vista3
                    :empresas="empresas"
                    :modelValueEmpresas="form.empresa_id"
                    v-model="form.archivos_legales"
                    type="legal"
                    baseUrl="/storage/"
                  />

      </div>

          <!--     <FormField label="Documentos Requeridos">
                    <DocumentSelector
                        :empresas="empresas"
                        :modelValueEmpresas="form.empresa_id"
                        v-model:modelValueTecnicos="form.archivos_tecnicos"
                        v-model:modelValueLegales="form.archivos_legales"
                        baseUrl="/storage/"
                      />
              </FormField> -->

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
 <LoadingOverlay :visible="isUploading" title="Cargando archivo(s)..." subtitle="Por favor, no cierres esta ventana." />
</template>