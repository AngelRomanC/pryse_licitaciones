<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiFileDocumentCheckOutline, mdiText, mdiMapMarker,mdiPhone ,mdiEmail} from "@mdi/js"; //agregado

const props = defineProps({
    titulo: String, // Título enviado desde el backend
    routeName: String, // Nombre de la ruta base
});
// Datos del formulario
const form = useForm({
    nombre_documento: '',
  
});
// Función para enviar el formulario
const handleSubmit = () => {
    // form.post(route(${props.routeName}store'));
    form.post(route(`${props.routeName}store`));

};
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main>

        </SectionTitleLineWithButton>

        <CardBox form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Campo: Nombre -->
                <FormField label="Nombre" :error="form.errors.nombre_documento">
                    <FormControl
                        v-model="form.nombre_documento"
                        type="text"
                        placeholder="Nombre del documento"
                        :icon="mdiFileDocumentCheckOutline"
                        required
                    />
                </FormField>              
            </div>
     
            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Crear" />
                    <BaseButton :href="route(`${props.routeName}index`)" type="reset" color="danger" outline label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
