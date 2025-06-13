<template>
  <div v-if="total > 1" class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
    <BaseLevel>
      <BaseButtons>
        <BaseButton
          v-for="page in total"
          :key="page"
          :active="page === currentPage"
          :label="page"
          :href="links[page]?.url"
          @click.prevent="handleClick(page)"
          :color="page === currentPage ? 'lightDark' : 'whiteDark'"
          small
        />
      </BaseButtons>
      <small>Página {{ currentPage }} de {{ total }}</small>
    </BaseLevel>
  </div>

  <div class="vl-parent">
    <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="false" />
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3'
import BaseLevel from "@/components/BaseLevel.vue"
import BaseButtons from "@/components/BaseButtons.vue"
import BaseButton from "@/components/BaseButton.vue"
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/css/index.css'
import { ref } from "vue"

export default {
  props: {
    links: Array,
    total: Number,
    currentPage: Number,
    pageParam: {
      type: String,
      default: 'page' // para que pueda ser 'page' o 'page_legal'
    },
    routeName: {
      type: String,
      default: 'dashboard'
    }
  },
  components: {
    BaseLevel,
    BaseButtons,
    BaseButton,
    Loading
  },
  setup(props) {
    const isLoading = ref(false)

    function handleClick(page) {
      const url = props.links[page]?.url
      if (!url) return

      const selectedPage = new URL(url).searchParams.get(props.pageParam)

      // Obtener todos los parámetros actuales de la URL
      const currentParams = Object.fromEntries(new URLSearchParams(window.location.search).entries())

      // Actualizar solo el parámetro correspondiente (page o page_legal)
      currentParams[props.pageParam] = selectedPage

      isLoading.value = true

      router.get(
        route(props.routeName),
        currentParams,
        {
          preserveState: true,
          preserveScroll: true,
          onFinish: () => {
            isLoading.value = false
          }
        }
      )
    }

    return { isLoading, handleClick }
  }
}
</script>
