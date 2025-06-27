<script setup>
import { router } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import Swal from "sweetalert2";
import { mdiTagEdit, mdiDeleteOutline ,mdiInformation} from "@mdi/js";
import Pagination from '@/Shared/Pagination.vue';
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import SearchBar from '@/components/SearchBar.vue'
import { ref } from 'vue'

const props = defineProps({
    empresas: Object,
    titulo: String, //
    filters: Object,
    routeName: String,


});
const filters = ref({
  search: props.filters?.search ?? '',
})

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
            router.delete(route("empresa.destroy", id));
        }
    });
};

</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :title="props.titulo" main>
            <BaseButton :href="route('empresa.create')" color="warning" label="Crear" />
        </SectionTitleLineWithButton>
        
        <SearchBar   v-model="filters.search"  :routeName="routeName"  placeholder="Buscar empresa por nombre..."  />

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>

        <CardBox v-if="empresas.data.length < 1">
            <CardBoxComponentEmpty />
        </CardBox>

        <CardBox v-else class="mb-6" has-table>
            <table class="w-full border-collapse border mt-4">
                <thead>
                    <tr>
                        <th />
                        <th class="border p-2">Nombre </th>
                        <th class="border p-2">Descripción</th>
                        <th class="border p-2">Dirección</th>
                        <th class="border p-2">Teléfono</th>
                        <th class="border p-2">Email</th>
                        <th class="border p-2">Acciones</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="empresa in empresas.data" :key="empresa.id">
                        <td class="align-items-center"></td>

                        <td data-label="Nombre de Empresa" class="border p-2">{{ empresa.nombre }}</td>
                        <td data-label="Descripción" class="border p-2">{{ empresa.descripcion }}</td>
                        <td data-label="Dirección" class="border p-2">{{ empresa.direccion }}</td>
                        <td data-label="Teléfono" class="border p-2">{{ empresa.telefono }}</td>
                        <td data-label="Email" class="border p-2">{{ empresa.email }}</td>
                        <td class="before:hidden lg:w-1 whitespace-nowrap" data-label="Acciones">
                            <BaseButtons type="justify-start lg:justify-end" no-wrap>
                                <BaseButton color="info" :icon="mdiTagEdit" small
                                    :href="route('empresa.edit', empresa.id)" />
                                <BaseButton color="danger" :icon="mdiDeleteOutline" small
                                    @click="destroy(empresa.id)" />
                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :currentPage="empresas.current_page" :links="empresas.links"
                :total="empresas.links.length - 2" />
        </CardBox>
    </LayoutMain>
</template>