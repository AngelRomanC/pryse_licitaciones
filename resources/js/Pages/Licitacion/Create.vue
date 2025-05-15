<script setup>
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import { mdiBallotOutline, mdiOfficeBuilding, mdiFileDocument, mdiPlus,mdiMapMarker, mdiCalendar } from "@mdi/js";
import FileUploader from '@/Components/FileUploader.vue';
import CatalogoRedirectButton from '@/Components/CatalogoRedirectButton.vue';
import MultiSelectEstados from '@/Components/MultiSelectEstados.vue';


const props = defineProps({
    empresas: Array,
    estados: Array,
});

const form = useForm({
    nombre: '',
    fecha_licitacion: '',
    empresa_id: '',
    estados: [],
    documentos_legales: [],
    documentos_tecnicos: [],
});

const handleSubmit = () => {
    form.post(route('licitaciones.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <LayoutMain title="Crear Licitación">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" title="Crear Licitación" main />

        <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Nombre -->
                <FormField label="Nombre" :error="form.errors.nombre">
                    <FormControl
                        v-model="form.nombre"
                        type="text"
                        placeholder="Nombre de la licitación"
                        required
                    />
                </FormField>

                <!-- Fecha de Licitación -->
                <FormField label="Fecha de Licitación" :error="form.errors.fecha_licitacion">
                    <FormControl
                        v-model="form.fecha_licitacion"
                        type="date"
                        :icon="mdiCalendar"
                        required
                    />
                </FormField>

                <!-- Empresa -->
                <FormField label="Empresa" :error="form.errors.empresa_id">
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <FormControl
                                v-model="form.empresa_id"
                                :options="empresas"
                                type="select"
                                label-key="nombre"
                                value-key="id"
                                :icon="mdiOfficeBuilding"
                                placeholder="Seleccione una empresa"
                                required
                            />
                        </div>
                        <CatalogoRedirectButton
                            catalog-route-name="empresa"
                            return-route="empresa.create"
                            :return-id="form.id"
                            label="Agregar empresa"
                            :icon="mdiPlus"

                        />
                    </div>
                </FormField>

                <!-- Estados -->
                <FormField label="Estados" :error="form.errors.estados">
                    <FormControl
                        v-model="form.estados"
                        :options="estados"
                        type="multiselect"
                        label-key="nombre"
                        value-key="id"
                        :icon="mdiMapMarker"
                        placeholder="Seleccione estados"
                        required
                    />
                </FormField>



                  <MultiSelectEstados 
                    v-model="form.estados"
                    :estados="estados"
                    />

                <!-- Documentos Legales -->
                <FileUploader 
                    label="Documentos Legales" 
                    v-model="form.documentos_legales"
                    :error="form.errors.documentos_legales"
                    accept=".pdf,.doc,.docx"
                    multiple
                />

                <!-- Documentos Técnicos -->
                <FileUploader 
                    label="Documentos Técnicos" 
                    v-model="form.documentos_tecnicos"
                    :error="form.errors.documentos_tecnicos"
                    accept=".pdf,.doc,.docx"
                    multiple
                />
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton 
                        type="submit" 
                        color="info" 
                        label="Crear Licitación"
                        :disabled="form.processing"
                        :loading="form.processing"
                    />
                    <BaseButton 
                        :href="route('licitaciones.index')" 
                        type="button" 
                        color="danger" 
                        outline 
                        label="Cancelar"
                    />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>