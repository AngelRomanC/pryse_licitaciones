<script>
import { Link, useForm } from '@inertiajs/vue3';
import { mdiBallotOutline, mdiAccount, mdiMail, mdiGithub } from "@mdi/js";
import LayoutMain from '@/layouts/LayoutMain.vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";

export default {  //props son propiedades que el componente espera recibir desde su componente padre. Estas no necesitan ser retornadas
    props: {
        titulo: { type: String, required: true },
        routeName: { type: String, required: true }, 
    },
    components: {
        LayoutMain,
        FormField,
        FormControl,
        BaseDivider,
        BaseButton,
        BaseButtons,
        CardBox,
        SectionTitleLineWithButton
    },
    setup() {
        const handleSubmit = () => {
            form.post(route('materia.store'));
        };

        const form = useForm({ //Se usa para crear un formulario reactivo para manejar los datos de las materias (nombre, clave, etc.).
            nombre: '',
            clave: '',
            cuatrimestre: '',
            tipo:'',
            No_horas_presenciales:'',
            No_horas_no_presenciales:'',
            periodo:'',
            nivel:'',
            status: 1
        });
        //hace que estas variables y métodos sean accesibles dentro de la parte visual del component
        return { handleSubmit, form, mdiBallotOutline, mdiAccount, mdiMail, mdiGithub }
    }
}
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main>
            <a :href="`${route(routeName + 'index')}`">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-x" viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </a>
        </SectionTitleLineWithButton>

        <CardBox form @submit.prevent="handleSubmit">
            <FormField label="Nombre">
                <FormControl v-model="form.nombre"  placeholder="nombre"/>
                <FormControl v-model="form.clave" placeholder="clave" />
                <FormControl v-model="form.cuatrimestre" placeholder="cuatrimestre" />
                <FormControl v-model="form.tipo" placeholder="tipo" />
                <FormControl v-model="form.No_horas_presenciales" placeholder="No_horas_presenciales" />
                <FormControl v-model="form.No_horas_no_presenciales" placeholder="No_horas_no_presenciales" />
                <FormControl v-model="form.periodo" placeholder="periodo" />
                <FormControl v-model="form.nivel" placeholder="nivel" />
                <FormControl v-model="form.status" placeholder="Status" />
            </FormField>
           
            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" label="Crear" />
                    <BaseButton :href="route(`${routeName}index`)" type="reset" color="danger" outline
                        label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
