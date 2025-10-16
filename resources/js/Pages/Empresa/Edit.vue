<script setup>
import { useForm} from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";

import { mdiBriefcaseEdit, mdiAccount, mdiText, mdiMapMarker,mdiPhone ,mdiEmail} from "@mdi/js"; //agregado

//const props = defineProps(['titulo', 'empresa', 'routeName']); //Recibir la persona por id

 const props = defineProps({
     titulo: String,       // Título enviado desde el backend
     routeName: String,    // Nombre de la ruta base
     empresa: Object       // Datos de la empresa que se está editando
 });

const form = useForm({ ...props.empresa });

const guardar = () => {
    form.put(route(`${props.routeName}update`, props.empresa.id));
};

</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBriefcaseEdit" :title="titulo" main />
 

        <CardBox form @submit.prevent="guardar">

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
                        placeholder="Dirección"
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
                    <BaseButton @click="guardar" type="submit" color="info" outline label="Actualizar" />
                    <BaseButton :href="route(`${routeName}index`)" type="reset" color="danger" outline
                        label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
