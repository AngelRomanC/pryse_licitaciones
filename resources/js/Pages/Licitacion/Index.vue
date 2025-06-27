<script setup>
import { router } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import Swal from "sweetalert2";
import { mdiTagEdit, mdiDeleteOutline,mdiInformation,mdiDownload } from "@mdi/js";
import Pagination from '@/Shared/Pagination.vue';
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import moment from "moment";
import { ref } from 'vue'
import SearchBar from '@/components/SearchBar.vue'


const props = defineProps({
    licitaciones: Object,
    titulo: String, //
    routeName: String,
    filters: Object,

});

const filters = ref({ ...props.filters })

// Si quieres que se dispare al escribir automáticamente con un delay
let timeout = null
function buscar() {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get(route(`${props.routeName}index`), filters.value, {
      preserveState: true,
      replace: true,
    })
  }, 300) // espera 300ms antes de hacer la petición
}
function limpiar() {
  filters.value.search = ''
  router.get(route(`${props.routeName}index`), {}, {
    preserveState: false,
    replace: true,
  })
}

const destroy = (id) => {
    Swal.fire({
        title: "¿Está seguro?",
        text: "Esta acción no se puede revertir",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar registro!",
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route(`${props.routeName}destroy`, id));

        }
    });
};

</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :title="props.titulo" main>
            <BaseButton :href="route(`${props.routeName}create`)" color="warning" label="Crear" />
        </SectionTitleLineWithButton>

        <SearchBar   v-model="filters.search"  :routeName="routeName"  placeholder="Buscar licitación por nombre..."  />

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>

        <CardBox v-if="licitaciones.data.length < 1">
            <CardBoxComponentEmpty  title="Sin resultados"
            description="No se encontraron licitaciones que coincidan con tu búsqueda." />
       
        </CardBox>

        <CardBox v-else class="mb-6" has-table>
              

           <table class="w-full border-collapse border mt-4">
                <thead>
                    <tr>
                        <th />
                        <th class="border p-2">Nombre de Licitación</th>
                        <th class="border p-2">Fecha</th>
                     
                        <th class="border p-2">Acciones</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="documento in licitaciones.data" :key="documento.id">
                        <td class="align-items-center">
                        </td>
                        <td data-label="Nombre Licitación" class="border p-2">{{ documento.nombre }}</td>
                        <td data-label="Fecha" class="border p-2">{{ documento.fecha}}</td>

                       
                        <td class="before:hidden lg:w-1 whitespace-nowrap">
                            <BaseButtons type="justify-start lg:justify-end" no-wrap>
                                <BaseButton color="info" :icon="mdiTagEdit" small
                                    :href="route(`${props.routeName}edit`, documento.id)" />
                                <BaseButton color="danger" :icon="mdiDeleteOutline" small
                                    @click="destroy(documento.id)" />
                                 

                                        <BaseButton color="success" :icon="mdiDownload" small
                                        :href="route('licitaciones.descargar', documento.nombre)"
                                        class="btn btn-success"
                                        target="_blank"
                                        title="Descargar expediente"
                                        />
                                        
                                    

                            </BaseButtons>
                            
                        </td>
                    </tr>
                </tbody>                
            </table>        
            
            <Pagination :currentPage="licitaciones.current_page" :links="licitaciones.links"
                :total="licitaciones.links.length - 2" />
        </CardBox>
    </LayoutMain>
</template>