<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import LayoutMain from '@/layouts/LayoutMain.vue'
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue"
import BaseButton from "@/components/BaseButton.vue"
import FormField from "@/components/FormField.vue"
import FormControl from "@/components/FormControl.vue"
import NotificationBar from "@/components/NotificationBar.vue"

const props = defineProps({
  usuario: Object,
  routeName: String,
  titulo: String,
})

const form = ref({
  name: props.usuario.name,
  apellido_paterno: props.usuario.apellido_paterno,
  apellido_materno: props.usuario.apellido_materno,
  numero: props.usuario.numero,
  email: props.usuario.email,
})

const submit = () => {
  router.put(route(`${props.routeName}update`, props.usuario.id), form.value)
}
</script>

<template>
  <LayoutMain>
    <SectionTitleLineWithButton :title="props.titulo" main />

    <NotificationBar v-if="$page.props.flash.message" color="success" :icon="'mdi-information'" :outline="false">
      {{ $page.props.flash.message }}
    </NotificationBar>

    <form @submit.prevent="submit" class="space-y-4">
      <FormField label="Nombre">
        <FormControl v-model="form.name" type="text" required />
      </FormField>

      <FormField label="Apellido Paterno">
        <FormControl v-model="form.apellido_paterno" type="text" required />
      </FormField>

      <FormField label="Apellido Materno">
        <FormControl v-model="form.apellido_materno" type="text" required />
      </FormField>

      <FormField label="Número Telefónico">
        <FormControl v-model="form.numero" type="text" required />
      </FormField>

      <FormField label="Correo Electrónico">
        <FormControl v-model="form.email" type="email" required />
      </FormField>

      <BaseButton type="submit" color="info" label="Actualizar" />
    </form>
  </LayoutMain>
</template>
