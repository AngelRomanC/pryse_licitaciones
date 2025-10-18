<script setup>
import { ref, defineProps, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import FormControlCheckbox from "@/Components/FormControlCheckbox.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import {
  mdiAccount,
  mdiAccountCircle,
  mdiAccountTie,
  mdiPhone,
  mdiMail,
  mdiLock,
  mdiOfficeBuilding
} from "@mdi/js";

const props = defineProps({
  titulo: String,
  routeName: String,
  roles: Array,
  departamentos: Array,
});
console.log('Departamentos prop:', props.roles);
const safeRoles = computed(() => props.roles || []);

const safeDepartamentos = computed(() => props.departamentos || []);
// Encuentra el ID del rol "usuario"
const usuarioRoleId = computed(() => {
  const usuarioRole = safeRoles.value.find(role => 
    role.name.toLowerCase() === 'usuario'
  );
  return usuarioRole ? usuarioRole.id : null;
});

const form = useForm({
  name: '',
  apellido_paterno: '',
  apellido_materno: '',
  numero: '',
  email: '',
  password: '',
  departamento_id: null,
  roles: [],
});
// Computed para saber si el rol "usuario" está seleccionado
const isUsuarioRoleSelected = computed(() => {
  return form.roles.includes(usuarioRoleId.value);
});

// Watch para limpiar departamento_id cuando se deselecciona el rol usuario
watch(isUsuarioRoleSelected, (newValue) => {
  if (!newValue) {
    form.departamento_id = null; // Limpiar departamento si se quita el rol usuario
  }
});
const guardar = () => {
  console.log('Submitting form with data:', form.roles);
  form.post(route(`${props.routeName}store`));
};
</script>

<template>
  <LayoutMain :title="titulo">
    <SectionTitleLineWithButton :title="props.titulo" main />

    <NotificationBar 
      v-if="$page.props.flash.message" 
      color="success" 
      :icon="'mdi-information'" 
      :outline="false"
    >
      {{ $page.props.flash.message }}
    </NotificationBar>

    <CardBox form @submit.prevent="guardar">
      <FormField :error="form.errors.name" label="Nombre">
        <FormControl v-model="form.name" type="text" required :icon="mdiAccount" />
      </FormField>

      <FormField :error="form.errors.apellido_paterno" label="Apellido Paterno">
        <FormControl v-model="form.apellido_paterno" type="text" required :icon="mdiAccountCircle" />
      </FormField>

      <FormField :error="form.errors.apellido_materno" label="Apellido Materno">
        <FormControl v-model="form.apellido_materno" type="text" required :icon="mdiAccountTie" />
      </FormField>

      <FormField :error="form.errors.numero" label="Número Telefónico">
        <FormControl
          v-model="form.numero"
          type="tel"
          required
          placeholder="Teléfono"
          maxlength="10"
          pattern="^\d{10}$"
          title="El número debe contener exactamente 10 dígitos"
          :icon="mdiPhone"
        />
      </FormField>

      <FormField :error="form.errors.email" label="Correo Electrónico">
        <FormControl v-model="form.email" type="email" required :icon="mdiMail" />
      </FormField>
      
      <FormField :error="form.errors.password" label="Password">
        <FormControl
          v-model="form.password"
          type="password"
          required
          placeholder="Contraseña"
          :icon="mdiLock"
        />
      </FormField>  

      <BaseDivider />

   

      <BaseDivider />

      <!-- Selección de Roles -->
      <FormField label="Asignar Roles" :error="form.errors.roles">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
          <label v-for="role in safeRoles" :key="role.id" class="flex items-center space-x-2 cursor-pointer">
            <input
              type="checkbox"
              :value="role.id"
              v-model="form.roles"
              class="form-checkbox text-blue-600"
            />
            <span>{{ role.name }}</span>
          </label>
        </div>

        <!-- Mostrar roles seleccionados -->
        <div v-if="form.roles.length > 0" class="mt-4 p-3 bg-blue-50 rounded-lg">
          <p class="text-sm font-medium text-blue-800">Roles seleccionados:</p>
          <div class="flex flex-wrap gap-2 mt-2">
            <span
              v-for="roleId in form.roles"
              :key="roleId"
              class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full"
            >
              {{ safeRoles.find(r => r.id === roleId)?.name }}
            </span>
          </div>
        </div>
      </FormField>

      <!-- Departamentos solo si se elige el rol usuario -->
      <FormField 
        v-if="isUsuarioRoleSelected && safeDepartamentos.length > 0" 
        label="Departamento" 
        :error="form.errors.departamento_id"
      >
        <FormControl
          v-model="form.departamento_id"
          :options="safeDepartamentos"
          type="select"
          label-key="name"
          value-key="id"
          :icon="mdiOfficeBuilding"
          required
        />
        <p class="text-xs text-gray-500 mt-1">
          * Campo requerido para el rol "Usuario"
        </p>
      </FormField>

      <template #footer>
        <BaseButtons>
          <BaseButton @click="guardar" type="submit" color="info" outline label="Guardar" />
          <BaseButton :href="route(`${props.routeName}index`)" type="reset" color="danger" outline label="Cancelar" />
        </BaseButtons>
      </template>
    </CardBox>
  </LayoutMain>
</template>
