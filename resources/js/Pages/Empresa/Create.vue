<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiAccount, mdiText, mdiMapMarker,mdiPhone ,mdiEmail, mdiBriefcasePlus} from "@mdi/js"; //agregado

const props = defineProps({
    titulo: String, // Título enviado desde el backend
    routeName: String, // Nombre de la ruta base

});
// Captura el parámetro `redirect` de la URL
const urlParams = new URLSearchParams(window.location.search);
const redirectParam = urlParams.get('redirect'); // Obtiene el valor de ?redirect=...
// Datos del formulario
const form = useForm({
    nombre: '',
    descripcion: '',
    direccion: '',
    telefono: '',
    email: '',
    redirect: redirectParam, // ¡Pásalo al formulario!

});
// Función para enviar el formulario
const handleSubmit = () => {
    // form.post(route(${props.routeName}store'));
    form.post(route(`${props.routeName}store`));

};
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBriefcasePlus" :title="titulo" main />    
         

        <CardBox form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Campo: Nombre -->
                <FormField label="Nombre" :error="form.errors.nombre">
                    <FormControl
                        v-model="form.nombre"
                        type="text"
                        placeholder="Nombre de la empresa"
                        :icon="mdiAccount"
                        required
                    />
                </FormField>


                <!-- Campo: Descripción -->
                <FormField label="Descripción" :error="form.errors.descripcion">
                    <FormControl
                        v-model="form.descripcion"
                        type="text"
                        placeholder="Breve descripción"
                        :icon="mdiText"
                    />
                </FormField>

                <!-- Campo: Dirección -->
                <FormField label="Dirección" :error="form.errors.direccion">
                    <FormControl
                        v-model="form.direccion"
                        type="text"
                        placeholder="Dirección completa"
                        :icon="mdiMapMarker"
                    />
                </FormField>

                <!-- Campo: Teléfono -->
                <FormField label="Teléfono" :error="form.errors.telefono">
                    <FormControl
                        v-model="form.telefono"
                        placeholder="Teléfono"
                        :icon="mdiPhone"
                         type="tel"
                        maxlength="10"
                        pattern="^\d{10}$"
                        title="El número debe contener exactamente 10 dígitos"
                    />
                </FormField>

                <!-- Campo: Correo Electrónico -->
                <FormField label="Correo Electrónico" :error="form.errors.email">
                    <FormControl
                        v-model="form.email"
                        type="email"
                        placeholder="Email"
                        :icon="mdiEmail"
                    />
                </FormField>
            </div>
     
            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Crear" />
                    <BaseButton :href="redirectParam || route(`empresa.index`)" type="reset" color="danger" outline label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
