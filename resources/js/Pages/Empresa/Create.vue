<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import CardBox from "@/components/CardBox.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
const props = defineProps({
    titulo: String, // Título enviado desde el backend
    routeName: String, // Nombre de la ruta base
});
// Datos del formulario
const form = useForm({
    nombre: '',
    descripcion: '',
    direccion: '',
    telefono: '',
    email: '',
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
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input v-model="form.nombre" type="text" class="w-full p-2 border rounded"
                        placeholder="Nombre de la empresa" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input v-model="form.descripcion" type="text" class="w-full p-2 border rounded"
                        placeholder="Breve descripción" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <input v-model="form.direccion" type="text" class="w-full p-2 border rounded"
                        placeholder="Dirección" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input v-model="form.telefono" type="text" class="w-full p-2 border rounded"
                        placeholder="Teléfono" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input v-model="form.email" type="email" class="w-full p-2 border rounded" placeholder="Email" />
                </div>
            </div>

         



            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Crear" />
                    <BaseButton :href="route(`empresa.index`)" type="reset" color="danger" outline label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
