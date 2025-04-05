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
            <Title class="text-center">Lead Safe Certificate</Title>
        </div>

        <!-- Template -->
        <div class="bg-white shadow-lg rounded-lg p-4">
            <table class="w-full border-4 border-black">
                <tbody>  <!-- Add tbody here -->
                    <tr>
                        <td class="p-8">
                            <!-- Right side (CERT NO) -->
                        <div class="md:text-right mt-1 md:mt-0">
                            <span class="text-2xl font-semibold">{{ record.certificate_number }}</span>
                        </div>
                            <div class="text-center mb-6 flex flex-col items-center">
                                <img :src="stateLogo" alt="NJ State Logo" class="h-36 w-36 object-contain" />
                                <h1 class="text-3xl font-bold text-blue-900 underline mt-5">LEAD - SAFE CERTIFICATE</h1>
                            </div>
                            <div class="mb-1">
                                <p class="text-sm">
                                    It is hereby certified that a lead-based paint visual inspection and/or dust wipe sampling has been performed in accordance with the protocols referenced in N.J.A.C. 5:17, and the results of which indicate that no lead-based paint hazards have been found in the dwelling unit listed below. It shall be the owner’s responsibility to perform any required ongoing evaluation and maintenance to ensure that the dwelling unit remains in a Lead-Safe condition. PURSUANT TO P.L.2003, c.311 (C.52:27D-437.1 et. seq.)
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg">
                                    This certificate is 
                                    <span class="underline font-bold text-blue-800">Valid For Three Years</span>
                                </p>
                            </div>
                            <div class="mb-2">
                                <table class="table-auto w-full mt-4 mb-2 border-separate border-spacing-2">
                                    <thead>
                                        <tr class="-mt-3">
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight">{{ record.address }} <span v-if="record.designation">Unit: {{ record.designation }}, </span> {{ record.municipality }}, NJ {{ record.city }}</th>
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight">{{ record.block }}</th>
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight">{{ record.lot }}</th>
                                        </tr>
                                        <tr class="-mt-3">
                                            <td class="border-t-2 border-b-2 border-gray-900 p-0 m-0 -mt-3 leading-tight"></td>
                                            <td class="border-t-2 border-b-2 border-gray-900 p-0 m-0 -mt-3 leading-tight"></td>
                                            <td class="border-t-2 border-b-2 border-gray-900 p-0 m-0 -mt-3 leading-tight"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="-mt-3">
                                            <td class="text-sm  -mt-3">Site Address</td>
                                            <td class="text-sm text-left -mt-3">Block</td>
                                            <td class="text-sm text-left -mt-3">Lot</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="text-sm text-center mb-2">
                                    Applicable Dwelling Unit: <span class="font-bold underline"><span v-if="record.designation">Unit: {{ record.designation }}</span></span>
                                </p>
                                <p class="text-3sm font-bold text-center mb-5">
                                    (CERTIFICATE IS VALID FOR A DWELLING UNIT AND SHALL BE AFFIXED TO LEASE)
                                </p>
                            </div>
                            <div class="mb-2">
                                <table class="table-auto w-full mt-4 mb-2 border-separate border-spacing-2">
                                    <thead>
                                        <tr class="-mt-3">
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight"><span class="text-red-600">Insp/RA Name: </span>{{ cOwner }}</th>
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight"><span class="text-red-600">Evaluation Contractor #: </span>{{ njdca }}</th>
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight"><span class="text-red-600">Phone: </span>{{ companyPhone }}</th>
                                        </tr>
                                        <tr class="-mt-3">
                                            <td class="border-t-2 border-b-2 border-red-600 p-0 m-0 -mt-3 leading-tight"></td>
                                            <td class="border-t-2 border-b-2 border-red-600 p-0 m-0 -mt-3 leading-tight"></td>
                                            <td class="border-t-2 border-b-2 border-red-600 p-0 m-0 -mt-3 leading-tight"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="-mt-3">
                                            <td class=" -mt-3"> </td>
                                            <td class="text-left -mt-3"> </td>
                                            <td class="text-left -mt-3"> </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                            <div class="flex justify-between mb-4">
                                <div class="mb-2">
                                    <table class="table-auto w-full mt-8 mb-8 border-separate border-spacing-x-4 border-spacing-y-0">
                                        <thead>
                                            <tr class="-mt-3">
                                                <th class="text-left">
                                                    <img :src="signatureUrl" 
                                                    alt="Inspector Signature" 
                                                    @error="handleImageError" 
                                                    class="w-auto h-auto max-h-16 object-contain"/>
                                                </th>
                                                <th class="text-left p-0 m-0 -mt-3 leading-tight">{{ record.license_number }}</th>
                                            </tr>
                                            <tr class="-mt-3">
                                                <td class="border-b-2 border-gray-900 p-0 m-0"></td>
                                                <td class="border-b-2 border-gray-900 p-0 m-0"></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-sm -mt-3">
                                                <td>Signature</td>
                                                <td class="text-left p-0 m-0">NJDOH ID #</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="flex justify-end mb-4 w-3/5">
                                    <table class="w-full border-collapse">
                                        <tbody>
                                            <tr>
                                                <td class="px-2 py-1 text-sm font-bold">Contractor Name:</td>
                                                <td class="px-2 py-1">
                                                    <div class="relative">
                                                        <span class="block">{{ companyName }}</span>
                                                        <div class="absolute left-0 bottom-0 h-0.5 bg-gray-500 w-full"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-2 py-1 text-sm font-bold">Contractor Address:</td>
                                                <td class="px-2 py-1">
                                                    <div class="relative">
                                                        <span class="block">{{ companyAddress }}</span>
                                                        <div class="absolute left-0 bottom-0 h-0.5 bg-gray-500 w-full"></div>
                                                    </div>
                                                    <div class="relative">
                                                        <span class="block mt-2">{{ companyCity }}, {{ companyState }} {{ companyZip }}</span>
                                                        <div class="absolute left-0 bottom-0 h-0.5 bg-gray-500 w-full"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-2 py-1 text-sm font-bold">Date Issued:</td>
                                                <td class="px-2 py-1">
                                                    <div class="relative">
                                                        <span class="block">{{ formattedDate }}</span>
                                                        <div class="absolute left-0 bottom-0 h-0.5 bg-gray-500 w-full"></div>
                                                    </div>
                                                    <span class="block font-bold text-sm">Date Issued (mm/dd/yyyy)</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-2">
                                <table class="table-auto w-3/5 mt-8 mb-8 border-separate border-spacing-x-4 border-spacing-y-0">
                                    <thead>
                                        <tr class="-mt-3">
                                            <th class="text-left">
                                                <img :src="signatureUrl" 
                                                alt="Inspector Signature" 
                                                @error="handleImageError" 
                                                class="w-auto h-auto max-h-16 object-contain"/>
                                            </th>
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight">{{ record.inspector_name }}</th>
                                            <th class="text-left p-0 m-0 -mt-3 leading-tight">{{ record.license_number }}</th>
                                        </tr>
                                        <tr class="-mt-3">
                                            <td class="border-b-2 border-gray-900 p-0 m-0"></td>
                                            <td class="border-b-2 border-gray-900 p-0 m-0"></td>
                                            <td class="border-b-2 border-gray-900 p-0 m-0"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-sm -mt-3">
                                            <td>Technician Signature</td>
                                            <td class="text-left p-0 m-0">Lead Inspector/Risk Assessor</td>
                                            <td class="text-left p-0 m-0">Permit #</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>  <!-- Closing tbody -->
            </table>
        </div>
        <!-- End of Table -->
    </Container>
               
</template>