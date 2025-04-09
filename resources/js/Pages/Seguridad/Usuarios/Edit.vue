<script setup>
import { ref, defineProps, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import Swal from 'sweetalert2';
import NotificationBar from "@/components/NotificationBar.vue"


const props = defineProps(['titulo', 'usuario', 'routeName']);
const form = useForm({ ...props.usuario });

const guardar = () => {
    form.put(route("usuarios.update", props.usuario.id));
};

</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :title="props.titulo" main />

        <NotificationBar v-if="$page.props.flash.message" color="success" :icon="'mdi-information'" :outline="false">
            {{ $page.props.flash.message }}
        </NotificationBar>

        <CardBox form @submit.prevent="guardar">
            <FormField :error="form.errors.name" label="Nombre">
                <FormControl v-model="form.name" type="text" required />
            </FormField>

            <FormField :error="form.errors.apellido_paterno" label="Apellido Paterno">
                <FormControl v-model="form.apellido_paterno" type="text" required />
            </FormField>

            <FormField :error="form.errors.apellido_materno" label="Apellido Materno">
                <FormControl v-model="form.apellido_materno" type="text" required />
            </FormField>

            <FormField :error="form.errors.numero" label="Número Telefónico">
                <FormControl v-model="form.numero" type="text" required />
            </FormField>

            <FormField :error="form.errors.email" label="Correo Electrónico">
                <FormControl v-model="form.email" type="email" required />
            </FormField>



            <template #footer>
                <BaseButtons>
                    <BaseButton @click="guardar" type="submit" color="info" label="Actualizar" />
                    <BaseButton :href="route(`${routeName}index`)" type="reset" color="danger" outline
                        label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
