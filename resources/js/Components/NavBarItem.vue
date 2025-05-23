<script setup>
import { mdiChevronUp, mdiChevronDown } from "@mdi/js";
import { Link } from "@inertiajs/vue3";
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { useStyleStore } from "@/stores/style.js";
import { usePage } from "@inertiajs/vue3";
import { useMainStore } from "@/stores/main.js";
import BaseIcon from "@/components/BaseIcon.vue";
import UserAvatarCurrentUser from "@/components/UserAvatarCurrentUser.vue";
import NavBarMenuList from "@/components/NavBarMenuList.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import axios from "axios";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["menu-click"]);
const numberNotis = ref(null)
const fungllo = () => {
  axios
    .get(route("notifications.unreadCount"))
    .then((response) => {
      const NotificationCount = response.data.unread_count;
      numberNotis.value= NotificationCount
    })
    .catch((error) => {
      console.error(
        "Error al cargar el recuento de notificaciones no leídas:",
        error
      );
    });
};

const itemHref = computed(() =>
  props.item.route ? route(props.item.route) : props.item.href
);

const is = computed(() => {
  if (props.item.href) {
    return "a";
  }

  if (props.item.route) {
    return Link;
  }

  return "div";
});

const styleStore = useStyleStore();

const componentClass = computed(() => {
  const base = [
    isDropdownActive.value
      ? `${styleStore.navBarItemLabelActiveColorStyle} dark:text-slate-400`
      : `${styleStore.navBarItemLabelStyle} dark:text-white dark:hover:text-slate-400 ${styleStore.navBarItemLabelHoverStyle}`,
    props.item.menu ? "lg:py-2 lg:px-3" : "py-2 px-3",
  ];

  if (props.item.isDesktopNoLabel) {
    base.push("lg:w-16", "lg:justify-center");
  }

  return base;
});

const itemLabel = computed(() => {
  if (props.item.isCurrentUser) {
    const currentUser = usePage().props.auth.user;
    const nombreCompleto = `${currentUser.name} ${currentUser.apellido_paterno} ${currentUser.apellido_materno}`;
    return nombreCompleto;
  } else if (props.item.isRol) {
    const roles = usePage().props.auth.user.roles;
    if (roles.length > 0) {
      return roles[0].name;
    } else {
      return "Sin Rol"; // Cuando el user no tiene rol
    }
  } else {
    return props.item.label;
  }
});

const isDropdownActive = ref(false);

const menuClick = (event) => {
  emit("menu-click", event, props.item);

  if (props.item.menu) {
    isDropdownActive.value = !isDropdownActive.value;
  }
};

const menuClickDropdown = (event, item) => {
  emit("menu-click", event, item);
};

const root = ref(null);

const forceClose = (event) => {
  if (root.value && !root.value.contains(event.target)) {
    isDropdownActive.value = false;
  }
};

onMounted(() => {
  fungllo();
  if (props.item.menu) {
    window.addEventListener("click", forceClose);
  }
});

onBeforeUnmount(() => {
  if (props.item.menu) {
    window.removeEventListener("click", forceClose);
  }
});
</script>

<template>
  <BaseDivider v-if="item.isDivider" nav-bar />
  <component
    :is="is"
    v-else
    ref="root"
    class="block lg:flex items-center relative cursor-pointer"
    :class="componentClass"
    :to="item.to ?? null"
    :href="item.href ?? null"
    :target="item.target ?? null"
    @click="menuClick"
  >
    <div
      class="flex items-center"
      :class="{
        'bg-gray-100 dark:bg-slate-800 lg:bg-transparent lg:dark:bg-transparent p-3 lg:p-0':
          item.menu,
      }"
    >
      <BaseIcon v-if="item.icon" :path="item.icon" class="transition-colors" />
      <span
        class="px-2 transition-colors"
        :class="{ 'lg:hidden': item.isDesktopNoLabel && item.icon }"
        >{{ itemLabel }}</span
      >
      <span v-if="item.isNotification === true">{{ numberNotis }}</span>
      <BaseIcon
        v-if="item.menu"
        :path="isDropdownActive ? mdiChevronUp : mdiChevronDown"
        class="hidden lg:inline-flex transition-colors"
      />
    </div>
    <div
      v-if="item.menu"
      class="text-sm border-b border-gray-100 lg:border lg:bg-white lg:absolute lg:top-full lg:left-0 lg:min-w-full lg:z-20 lg:rounded-lg lg:shadow-lg lg:dark:bg-slate-800 dark:border-slate-700"
      :class="{ 'lg:hidden': !isDropdownActive }"
    >
      <NavBarMenuList :menu="item.menu" @menu-click="menuClickDropdown" />
    </div>
  </component>
</template>
