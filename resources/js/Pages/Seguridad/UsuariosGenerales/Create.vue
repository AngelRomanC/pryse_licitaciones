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
  routeName: String,
  roles: Array,
  titulo: String,
})

const form = ref({
  name: '',
  apellido_paterno: '',
  apellido_materno: '',
  numero: '',
  email: '',
  password: '',
  role: 'Usuario',
})

const submit = () => {
  router.post(route(`${props.routeName}store`), form.value)
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

      <FormField label="Contraseña">
        <FormControl v-model="form.password" type="password" required />
      </FormField>

      <input type="hidden" v-model="form.role" />

      <BaseButton type="submit" color="success" label="Guardar" />
    </form>
  </LayoutMain>
</template>
