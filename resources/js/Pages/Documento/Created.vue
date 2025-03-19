<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiFormatListChecks } from "@mdi/js";

const props = defineProps({
    titulo: String,
    routeName: String,
    empresas: Array,
    tipos_documento: Array,
    estados: Array,
    departamentos: Array,
    modalidades: Array
});

const form = useForm({
    nombre_documento: 'Documento Técnico',
    empresa_id: '',
    tipo_de_documento_id: '',
    estado_id: '',
    departamento_id: '',
    fecha_revalidacion: '',
    fecha_vigencia: '',
    modalidad_id: '',
    ruta_documento: '',
    ruta_documento_anexo: ''
});
const handleSubmit = () => {
   // form.post(route(`${props.routeName}store`));
    form.post(route(`${props.routeName}store`));

};
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main />

        <CardBox form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Campo: Nombre Documento -->
                <FormField label="Nombre del Documento" :error="form.errors.nombre_documento">
                    <FormControl
                        v-model="form.nombre_documento"
                        type="text"
                        placeholder="Nombre del documento"
                        :icon="mdiFormatListChecks"
                        required
                    />
                </FormField>

                <!-- Campo: Empresa -->
                <FormField label="Empresa" :error="form.errors.empresa_id">
                    <select v-model="form.empresa_id" class="form-select">
                        <option value="" disabled>Selecciona una empresa</option>
                        <option v-for="empresa in props.empresas" :key="empresa.id" :value="empresa.id">
                            {{ empresa.nombre }}
                        </option>
                    </select>
                </FormField>

                <!-- Campo: Tipo de Documento -->
                <FormField label="Tipo de Documento" :error="form.errors.tipo_de_documento_id">
                    <select v-model="form.tipo_de_documento_id" class="form-select">
                        <option value="" disabled>Selecciona un tipo de documento</option>
                        <option v-for="tipo in props.tipos_documento" :key="tipo.id" :value="tipo.id">
                            {{ tipo.nombre_documento }}
                        </option>
                    </select>
                </FormField>

                <!-- Campo: Estado -->
                <FormField label="Estado" :error="form.errors.estado_id">
                    <select v-model="form.estado_id" class="form-select">
                        <option value="" disabled>Selecciona un estado</option>
                        <option v-for="estado in props.estados" :key="estado.id" :value="estado.id">
                            {{ estado.nombre }}
                        </option>
                    </select>
                </FormField>

                <!-- Campo: Departamento -->
                <FormField label="Departamento" :error="form.errors.departamento_id">
                    <select v-model="form.departamento_id" class="form-select">
                        <option value="" disabled>Selecciona un departamento</option>
                        <option v-for="departamento in props.departamentos" :key="departamento.id" :value="departamento.id">
                            {{ departamento.nombre_departamento }}
                        </option>
                    </select>
                </FormField>

                <!-- Campo: Fecha de Revalidación -->
                <FormField label="Fecha de Revalidación" :error="form.errors.fecha_revalidacion">
                    <FormControl
                        v-model="form.fecha_revalidacion"
                        type="date"
                        required
                    />
                </FormField>

                <!-- Campo: Fecha de Vigencia -->
                <FormField label="Fecha de Vigencia" :error="form.errors.fecha_vigencia">
                    <FormControl
                        v-model="form.fecha_vigencia"
                        type="date"
                        required
                    />
                </FormField>

                <!-- Campo: Modalidad -->
                <FormField label="Modalidad" :error="form.errors.modalidad_id">
                    <select v-model="form.modalidad_id" class="form-select">
                        <option value="" disabled>Selecciona una modalidad</option>
                        <option v-for="modalidad in props.modalidades" :key="modalidad.id" :value="modalidad.id">
                            {{ modalidad.nombre_modalidad }}
                        </option>
                    </select>
                </FormField>

                <!-- Campo: Ruta Documento -->
                <FormField label="Documento Principal" :error="form.errors.ruta_documento">
                    <FormControl
                        v-model="form.ruta_documento"
                        type="file"
                        accept="application/pdf"
                        required
                    />
                </FormField>

                <!-- Campo: Ruta Documento Anexo -->
                <FormField label="Documento Anexo" :error="form.errors.ruta_documento_anexo">
                    <FormControl
                        v-model="form.ruta_documento_anexo"
                        type="file"
                        accept="application/pdf"
                        required
                    />
                </FormField>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Crear" />
                    <BaseButton :href="route(`${routeName}index`)" type="reset" color="danger" outline label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>

<style scoped>
/* Personalizar estilos para el select */
.form-select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: white;
    color: #111827;
}
</style>