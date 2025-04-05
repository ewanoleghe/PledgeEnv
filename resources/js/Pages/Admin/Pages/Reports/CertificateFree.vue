<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../../Components/Container.vue";
import Title from "../../../../Components/Title.vue";
import { ref, computed } from 'vue';

// Emit event to the parent
const emit = defineEmits();

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    record: Object,
    signatureUrl: String,
    appSettings: String,
});

const companyLogo = '/images/logo.png';  
const stateLogo = '/images/stateLogo.png';  
// Use settings from props with fallbacks
const companyAddress = props.appSettings?.company_address || '648 Newark Ave.';
const companyCity = props.appSettings?.company_city || 'Elizabeth';
const companyState = props.appSettings?.company_state || 'NJ';
const companyZip = props.appSettings?.company_zip || '07208';
const companyEmail = props.appSettings?.company_email || 'info@pledgeenvironmental.com';
const companyPhone = props.appSettings?.company_phone || '(609) 208-5535';
const companyName = props.appSettings?.app_name || 'Pledge Environmental LLC';
const njdca = props.appSettings?.company_njdca || '00862-E';
const cOwner = props.appSettings?.company_owner || 'Saheed Alex Adeyeri';


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