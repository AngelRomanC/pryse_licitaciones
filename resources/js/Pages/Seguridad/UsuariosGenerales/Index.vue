<script setup>
import { router } from '@inertiajs/vue3'
import LayoutMain from '@/layouts/LayoutMain.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from "@/components/BaseButtons.vue"
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue"
import Swal from "sweetalert2"
import { mdiTagEdit, mdiDeleteOutline } from "@mdi/js"
import Pagination from '@/Shared/Pagination.vue'
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue"
import CardBox from "@/components/CardBox.vue"
import NotificationBar from "@/components/NotificationBar.vue"

const props = defineProps({
  usuarios: Object,
  titulo: String,
  routeName: String
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
      router.delete(route(`${props.routeName}destroy`, id))
    }
  })
}
</script>

<template>
  <LayoutMain>
    <SectionTitleLineWithButton :title="props.titulo" main>
      <BaseButton :href="route(`${props.routeName}create`)" color="warning" label="Crear" />
    </SectionTitleLineWithButton>

    <NotificationBar v-if="$page.props.flash.message" color="success" :icon="'mdi-information'" :outline="false">
      {{ $page.props.flash.message }}
    </NotificationBar>

    <CardBox v-if="usuarios.data.length < 1">
      <CardBoxComponentEmpty />
    </CardBox>

    <CardBox v-else class="mb-6" has-table>
      <table class="table-auto w-full">
        <thead>
          <tr>
            <th class="text-left p-2">Nombre</th>
            <th class="text-left p-2">Correo</th>
            <th class="text-left p-2">Rol</th>
            <th class="text-left p-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios.data" :key="usuario.id" class="border-t">
            <td class="p-2">{{ usuario.name }}</td>
            <td class="p-2">{{ usuario.email }}</td>
            <td class="p-2">
              <span v-if="usuario.roles.length > 0">{{ usuario.roles[0].name }}</span>
            </td>
            <td class="p-2">
              <BaseButtons type="justify-start">
                <BaseButton
                  :href="route(`${props.routeName}edit`, usuario.id)"
                  color="info"
                  :icon="mdiTagEdit"
                  small
                />
                <BaseButton
                  @click="destroy(usuario.id)"
                  color="danger"
                  :icon="mdiDeleteOutline"
                  small
                />
              </BaseButtons>
            </td>
          </tr>
        </tbody>
      </table>

      <Pagination
        :currentPage="usuarios.current_page"
        :links="usuarios.links"
        :total="usuarios.links.length - 2"
      />
    </CardBox>
  </LayoutMain>
</template>
