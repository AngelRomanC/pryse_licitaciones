<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import { mdiBallotOutline, mdiFileDocument, mdiMapMarker, mdiOfficeBuilding, mdiCalendar } from "@mdi/js"; // Íconos adicionales
import FormControlV7 from '@/Components/FormControlV7.vue';


const props = defineProps({
    titulo: String,
    documento: Object,
    routeName: String,
    empresas: Array,
    tipos_documento: Array,
    estados: Array,
    departamentos: Array,
    modalidades: Array
});

const form = useForm({
    nombre_documento: props.documento.nombre_documento,
    empresa_id: props.documento.empresa_id,
    tipo_de_documento_id: props.documento.tipo_de_documento_id,
    estado_id: props.documento.estado_id,
    departamento_id: props.documento.departamento_id,
    fecha_revalidacion: props.documento.fecha_revalidacion,
    fecha_vigencia: props.documento.fecha_vigencia,
    modalidad_id: props.documento.modalidades.map(mod => mod.id), // Para manejar múltiples modalidades
    ruta_documento: null,
    ruta_documento_anexo: null
});

const guardar = () => {
    form.put(route(`${props.routeName}update`, props.documento.id));
};
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main />
        <CardBox form @submit.prevent="guardar">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Nombre del Documento -->
                <FormField label="Nombre del documento" :error="form.errors.nombre_documento">
                    <FormControl
                        v-model="form.nombre_documento"
                        type="text"
                        placeholder="Nombre del documento"
                        :icon="mdiFileDocument"
                        required
                    />
                </FormField>

                <!-- Selector de Empresa -->
                <FormField label="Empresa" :error="form.errors.empresa_id">
                    <FormControl
                        v-model="form.empresa_id"
                        :options="empresas"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiOfficeBuilding"
                        placeholder="Selecciona una empresa"
                        required
                    />
                </FormField>

                <!-- Selector de Tipo de Documento -->
                <FormField label="Tipo de documento" :error="form.errors.tipo_de_documento_id">
                    <FormControl
                        v-model="form.tipo_de_documento_id"
                        :options="tipos_documento"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiFileDocument"
                        required
                    />
                </FormField>

                <!-- Selector de Estado -->
                <FormField label="Estado" :error="form.errors.estado_id">
                    <FormControl
                        v-model="form.estado_id"
                        :options="estados"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiMapMarker"
                        required
                    />
                </FormField>

                <!-- Selector de Departamento -->
                <FormField label="Departamento" :error="form.errors.departamento_id">
                    <FormControl
                        v-model="form.departamento_id"
                        :options="departamentos"
                        type="select"
                        label-key="name"
                        value-key="id"
                        :icon="mdiMapMarker"
                        required
                    />
                </FormField>

                   <!-- Selector de Modalidad -->
                   <FormField label="Modalidad" :error="form.errors.modalidad_id">
                    <FormControlV7
                        v-model="form.modalidad_id"
                        :options="modalidades"
                        label-key="name"
                        value-key="id"
                    />
                </FormField>

                <!-- Fecha de Revalidación -->
                <FormField label="Fecha de Revalidación" :error="form.errors.fecha_revalidacion">
                    <FormControl
                        v-model="form.fecha_revalidacion"
                        type="date"
                        :icon="mdiCalendar"
                        required
                    />
                </FormField>

                <!-- Fecha de Vigencia -->
                <FormField label="Fecha de Vigencia" :error="form.errors.fecha_vigencia">
                    <FormControl
                        v-model="form.fecha_vigencia"
                        type="date"
                        :icon="mdiCalendar"
                        required
                    />
                </FormField>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton @click="guardar" type="submit" color="info" label="Actualizar" />
                    <BaseButton :href="route(`${props.routeName}index`)" type="reset" color="danger" outline label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
