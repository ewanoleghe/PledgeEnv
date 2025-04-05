<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import VisualAssesment from "./Reports/VisualAssesment.vue";
import Certificate from "./Reports/Certificate.vue";
import ReportXrf from "./Reports/ReportXrf.vue";
import DustAssesment from "./Reports/DustAssesment.vue";
import CertificateFree from "./Reports/CertificateFree.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    record: Object,
    signatureUrl: String,
    xrfData: Array,
    appSettings: String,
    pageName: String,
    message: String,
    error: String,
});

</script>

<template>
    <Head title="- Select a payment method" />
    <Container class="w-full max-w-4xl mx-auto">
        <div class="mb-8 text-center w-full">
            <Title>Completed Inspection Services - <span v-if="props.pageName" >{{ pageName }}</span></Title>
        </div>

        <!-- Display Error Message if any -->
        <div v-if="message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ message }}</span>
        </div>
        
        <!-- Pass Props to Child and handle emit -->
        <VisualAssesment v-if="record.methodology === 'Visual Inspection' && !record.includeXrf"
            :record="record" 
            :signatureUrl="signatureUrl"
            :appSettings="appSettings"
        />
         <DustAssesment v-if="record.methodology === 'Dust Wipe Sampling' && !record.includeXrf"
            :record="record" 
            :signatureUrl="signatureUrl"
            :appSettings="appSettings"
        />
        <Certificate v-if="record.pass_fail === 'pass' && !record.includeXrf"
            :record="record" 
            :signatureUrl="signatureUrl"
            :appSettings="appSettings"
        />
        <ReportXrf v-if="record.includeXrf"
            :record="record" 
            :signatureUrl="signatureUrl"
            :appSettings="appSettings"
            :xrfData="xrfData"
        />
        <CertificateFree v-if="record.xrf_pass_fail === 'pass' && record.includeXrf"
            :record="record" 
            :signatureUrl="signatureUrl"
            :appSettings="appSettings"
        />

    </Container>
</template>
