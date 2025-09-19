<script setup>
import { useForm} from '@inertiajs/vue3';
import LayoutMain from '@/layouts/LayoutMain.vue';
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import CardBox from "@/components/CardBox.vue";

import { mdiBallotOutline, mdiHomeCity, mdiFormatListChecks, mdiMapMarker,mdiPhone ,mdiEmail} from "@mdi/js"; //agregado

//const props = defineProps(['titulo', 'empresa', 'routeName']); //Recibir la persona por id

 const props = defineProps({
     titulo: String,       
     routeName: String,    
     modalidad: Object       
 });

const form = useForm({ ...props.modalidad});

const guardar = () => {
    form.put(route(`${props.routeName}update`, props.modalidad.id));
};

</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="titulo" main>
           
        </SectionTitleLineWithButton>

        <CardBox form @submit.prevent="guardar">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Campo: Nombre -->
                <FormField label="Nombre" :error="form.errors.nombre_modalidad">
                    <FormControl
                        v-model="form.nombre_modalidad"
                        type="text"
                        placeholder="Nombre de modalidad"
                        :icon="mdiFormatListChecks"
                        required
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
