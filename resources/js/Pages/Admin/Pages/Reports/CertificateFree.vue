<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../../Components/Container.vue";
import Title from "../../../../Components/Title.vue";
import { ref, computed } from 'vue';

const companyLogo = '/images/logo.png';  
const stateLogo = '/images/stateLogo.png';  
const companyAddress = import.meta.env.VITE_COMPANY_ADDRESS || 'Default Address';
const companyCity = import.meta.env.VITE_COMPANY_CITY || 'Default City';
const companyState = import.meta.env.VITE_COMPANY_STATE || 'Default State';
const companyZip = import.meta.env.VITE_COMPANY_ZIP || 'Default Zip';
const companyEmail = import.meta.env.VITE_COMPANY_EMAIL || 'Default Email';
const companyPhone = import.meta.env.VITE_COMPANY_PHONE || 'Default Phone';
const companyName = import.meta.env.VITE_APP_NAME || 'Default Name';
const njdca = import.meta.env.VITE_COMPANY_NJDCA || 'Default NJDCA';
// const njdca = import.meta.env.VITE_APP_NJDCA || 'Default NJDCA';
const cOwner = import.meta.env.VITE_COMPANY_OWNER || 'Default NJDCA';


// Emit event to the parent
const emit = defineEmits();

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    record: Object,
    signatureUrl: String,
});

// Method to format date from YYYY-MM-DD to MM/DD/YYYY
const formatDate = (dateString) => {
    const [year, month, day] = dateString.split('-');
    return `${month}/${day}/${year}`;
};

const currentDate = ref(new Date());

const formattedDate = computed(() => {
  const month = String(currentDate.value.getMonth() + 1).padStart(2, '0'); // Months are 0-based
  const day = String(currentDate.value.getDate()).padStart(2, '0');
  const year = String(currentDate.value.getFullYear()); // Get last two digits of the year
  return `${month}/${day}/${year}`;
});

</script>

<template>
    <Container class="w-full max-w-4xl mx-auto">
        <Head title="- Select a payment method" />
        <!-- Actions Section -->
        <div class="flex justify-center items-center mb-4 mt-8 w-full">
            <Title class="text-center">Lead Free Certificate</Title>
        </div>

        
        <!-- End of Table -->
    </Container>
               
</template>