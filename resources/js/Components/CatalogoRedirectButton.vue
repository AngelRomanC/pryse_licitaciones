<script setup>
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import BaseButton from '@/Components/BaseButton.vue'

const props = defineProps({
  mode: {
    type: String,
    default: 'create', // "create" | "edit"
    validator: (v) => ['create', 'edit'].includes(v),
  },
  catalogRouteName: {
    type: String,
    required: true, // ejemplo: "documento-legal"
  },
  returnRoute: {
    type: String,
    required: true, // ejemplo: "dashboard.vencidos"
  },
  returnId: {
    type: [String, Number],
    default: null, // para documentos o entidades
  },
  label: {
    type: String,
    default: 'Ir al formulario',
  },
  icon: String,
  color: {
    type: String,
    default: 'info',
  },
  small: {
    type: Boolean,
    default: true,
  },
  outline: {
    type: Boolean,
    default: false,
  },
})

/**
 * Genera la URL a la que debe volver el usuario
 */
const redirectUrl = computed(() => {
  if (props.returnId) {
    return route(props.returnRoute, props.returnId)
  }
  return route(props.returnRoute)
})

/**
 * Redirige a la ruta según el modo (create/edit)
 */
function handleRedirect() {
  let routeName = props.catalogRouteName

  if (props.mode === 'edit') {
    if (!props.returnId) {
      console.error('⚠️ Se requiere returnId para usar modo "edit".')
      return
    }
    router.get(route(`${routeName}.edit`, props.returnId), {
      redirect: redirectUrl.value,
    })
  } else {
    router.get(route(`${routeName}.create`, {
      redirect: redirectUrl.value,
    }))
  }
}
</script>

<template>
  <BaseButton
    :icon="icon"
    :color="color"
    :small="small"
    :outline="outline"
    :title="label"
    @click="handleRedirect"
  />
</template>
