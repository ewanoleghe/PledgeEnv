<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../../Components/Container.vue";
import PrimaryBtn from "../../../../Components/PrimaryBtn.vue"; // Your button component
import Title from "../../../../Components/Title.vue";
import { useForm, router } from '@inertiajs/vue3'

const companyLogo = '/images/logo.png';  
const companyAddress = import.meta.env.VITE_COMPANY_ADDRESS || 'Default Address';
const companyCity = import.meta.env.VITE_COMPANY_CITY || 'Default City';
const companyState = import.meta.env.VITE_COMPANY_STATE || 'Default State';
const companyZip = import.meta.env.VITE_COMPANY_ZIP || 'Default Zip';
const companyEmail = import.meta.env.VITE_COMPANY_EMAIL || 'Default Email';
const companyPhone = import.meta.env.VITE_COMPANY_PHONE || 'Default Phone';
const companyName = import.meta.env.VITE_APP_NAME || 'Default Name';
const njdca = import.meta.env.VITE_COMPANY_NJDCA || 'Default NJDCA';

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

// Method to check if the file is a PDF
const isPdf = (filePath) => {
    return filePath.toLowerCase().endsWith('.pdf');
};

// Method to render the floor plan, lab report, chain of custody, or XRF report
const renderFile = (filePath, canvasId) => {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    
    // Set the canvas height dynamically based on the window's height
    // const windowHeight = window.innerHeight;
    // canvas.height = windowHeight - 100;  // Adjust this value based on the UI structure (e.g., margins, headers)
    // Set the canvas width and height to A4 dimensions in pixels (595px x 842px)
    canvas.width = 595;  // A4 width in pixels
    canvas.height = 942; // A4 height in pixels

    if (isPdf(filePath)) {
        // Render PDF (use native embedding with <embed> or <iframe>)
        const embed = document.createElement('embed');
        embed.src = filePath;
        embed.type = 'application/pdf';
        embed.width = '100%';
        embed.height = `${canvas.height}`;  // Set height dynamically
        canvas.replaceWith(embed);
    } else {
        // If it's an image, display it using <img>
        const img = new Image();
        img.src = filePath;
        img.alt = 'Report Image';
        img.width = canvas.width;
        img.height = canvas.height;
        canvas.replaceWith(img);
    }
};

// Method to handle Reset action
const handleReset = () => {
    // Send 'reset' action and the record ID to the Laravel controller using Inertia.js
    router.post(route('admin.report.submit'), { 
        action: 'reset', 
        id: props.record.id // Pass the ID from the props
    });
};

// Method to handle Approve action
const handleApprove = () => {
    // Send 'approve' action and the record ID to the Laravel controller using Inertia.js
    router.post(route('admin.report.submit'), { 
        action: 'approve', 
        id: props.record.id // Pass the ID from the props
    });
};

</script>

<template>
    <Head title="- Select a payment method" />
    <Container class="w-full max-w-4xl mx-auto">
        <!-- Actions Section -->
          <p class="text-2xl">Report Template</p>
      <hr v-if="!props.record.date_issued" class="border-t-2 border-black w-full"> <!-- Added w-full here -->        

        <div v-if="!props.record.date_issued" class="flex justify-between p-5">
            <!-- Reset/Cancel Button -->
            <button 
                type="button" 
                :disabled="loading" 
                @click="handleReset" 
                class="py-3 px-6 mt-0 mx-2 w-full text-center rounded font-bold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:bg-gray-300">
                Reset/Cancel <br>[Error! Remove all Documents & photos]
            </button>
            
            <!-- Approve & Send Report Button -->
            <PrimaryBtn 
                type="button" 
                :disabled="loading" 
                @click="handleApprove" 
                class="w-full py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-300">
                Approve & Send Report <br>[A report will be emailed to the Customer]
            </PrimaryBtn>
        </div>
        
        <hr class="border-t-2 border-black w-full mb-20"> <!-- Added w-full here -->

        <div class="flex flex-col items-end mb-4">
            <img :src="companyLogo" alt="Company Logo" class="w-52 h-auto mb-2">
            <p class="text-right">
                {{ companyAddress }}<br>
                {{ companyCity }}, {{ companyState }} {{ companyZip }}<br>
                Email: {{ companyEmail }}<br>
                Phone: {{ companyPhone }}
            </p>
        </div>

        <div v-if="record.methodology === 'Visual Inspection'" class="mb-2 mt-8 text-center w-full">
            <p class="centered-text font-bold text-2xl mb-2">
                Visual Paint Assessment Report
            </p>
            <p class="centered-text text-2sm mb-2">
                {{ record.address }}, 
                <span v-if="record.designation">Unit: {{ record.designation }}, </span>
                {{ record.municipality }}, NJ {{ record.city }}
            </p>
            <p class="centered-text font-bold text-2sm mb-4">
                {{ formatDate(record.selected_date) }}
            </p>           

            <p class="text-left text-2sm leading-relaxed mb-2">
                On {{ formatDate(record.selected_date) }}, {{ companyName }} [{{ njdca }}] performed a visual paint condition assessment for the presence of deteriorated paint, chips, or dust from painting activities and paint residue on floors at {{ record.address }}, <span v-if="record.designation">Unit: {{ record.designation }}</span>,  {{ record.municipality }}, NJ {{ record.city }}; (the “property”) in compliance with NJAC 5:28A. Only the dwelling and common area interiors were included. Exteriors were not part of the assessment. The visual assessment followed the protocols found in HUD’s Visual Assessment Training course. 
            </p>
            <p v-if="record.pass_fail === 'pass'" class="text-left text-2sm leading-relaxed">
                No deteriorated paint, chips, or dust from painting activities or paint residue on floors were observed in the dwelling unit. The property is eligible for a lead-safe certificate per NJAC 5:28A. Certificate enclosed.  
            </p>
            <p v-if="record.pass_fail === 'fail'" class="text-left text-2sm leading-relaxed">
                Deteriorated paint, chips or dust from painting activities and paint residue on floors were observed
                on the following components/surfaces.  
            </p>
            
            <div v-if="record.pass_fail === 'fail'" class="mb-8 mt-8 text-center w-full">
                <!-- Table Goes Here -->
                <table class="min-w-full border-collapse">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="text-black text-center text-x border border-black">Room</th>
                            <th class="text-black text-x text-center border border-black">Component</th>
                            <th class="text-black text-center text-x border border-black">Hazard</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- <tr v-for="eData in bookings.data" :key="eData.id"> -->
                        <tr v-for="eData in record.photos" :key="eData.id" >
                            <td class="text-black text-center border border-black">
                                <span class="text-xs text-black-900 font-bold">{{ eData.room }}</span><br>
                            </td>
                            <td class="text-black text-center border border-black">
                                <span class="text-xs text-black-900 font-bold">{{ eData.component }}</span><br>
                            </td>
                            <td class="text-black text-center border border-black">
                                <span class="text-xs text-black-900 font-bold">{{ eData.hazard }}</span><br>
                            </td>
                        </tr>
                            <!-- Additional rows as needed -->
                    </tbody>
                </table>
            </div>

            <p v-if="record.pass_fail === 'fail'" class="text-left text-2sm leading-relaxed">
                Identified hazards shall be remediated using either abatement (permanent) or interim controls
                (temporary). Abatement is permanent but has a higher cost, as Interim controls are temporary but
                can be much more affordable. Abatement shall be performed by a NJ Licensed lead abatement firm
                using licensed lead abatement supervisors and workers. You can find the list of NJ licensed lead
                abatement contractors at 
                <a href="https://www.nj.gov/dca/codes/publications/pdf_lead/ld_eval_contrs.pdf" target="_blank" class="text-blue-500 underline">
                    NJ Lead Abatement Contractors
                </a>.
                Interim control work shall be performed by EPA RRP certified firms using an EPA certified RRP
                Renovator. You can find certified EPA RRP contractors at 
                <a href="https://cdxapps.epa.gov/ocspp-oppt-lead/firm-location-search" target="_blank" class="text-blue-500 underline">
                    EPA RRP Certified Firms
                </a>.
                Select RRP Contractor, then NJ, then enter your zip code to search for contractors. After completion of interim control work, a lead-based
                paint post-remediation with dust wipes will be required. After completion of abatement work, a lead-based paint abatement clearance will be required.  
            </p>

            <p class="text-left text-2sm pt-8 leading-relaxed pb-20">
                Respectfully Submitted,
                <br />
                <br />
                <!-- Display the inspector's signature -->
                <img :src="signatureUrl" 
                    alt="Inspector Signature" 
                    @error="handleImageError" 
                    class="w-auto h-auto max-h-16 object-contain" />
                <br />
                {{ record.inspector_name }}<br>
                Lead Inspector/Risk Assessor,<br> 
                Permit No.: {{ record.license_number }}<br>
                Encl: Floor Plan, 
                <span v-if="record.pass_fail === 'pass'">Lead-safe Certificate;</span>
                <span v-if="record.pass_fail === 'fail'">Photographs;</span>
            </p>

            <!-- Other Resources Section -->
            <Title>Other Resources</Title>
            <div class="mb-8">
                
                <div>
                <h3>Floor Plan:</h3>
                <a :href="`/storage/${record.floor_plan}`" target="_blank" class="text-blue-500 underline">
                    Open Floor Plan
                </a>                
                </div>
                <!-- XRF Report Link
                <div v-if="record.includeXrf">
                <h3>XRF Report:</h3>
                <a :href="`/storage/${record.xrf_report}`" target="_blank" class="text-blue-500 underline">
                    Open XRF Report
                </a>
                </div> -->

                <!-- Other Resources Section 2 -->
                <Title class="mt-8">Other Resources 2</Title>
                <div>
                    <!-- Display PDF or Image as Embedded File -->
                    <h3>Floor Plan (Embedded):</h3>
                    <PrimaryBtn @click="renderFile(`/storage/${record.floor_plan}`, 'floorPlanCanvas')" class="font-bold">Load Floor Plan</PrimaryBtn> <canvas id="floorPlanCanvas" width="600" height="950"></canvas>
                   
                    <!-- <div v-if="record.includeXrf" class="mb-8">
                        <h3 class="mt-8">XRF Report (Embedded):</h3>
                        <PrimaryBtn @click="renderFile(`/storage/${record.xrf_report}`, 'xrfReportCanvas')" class="font-bold">Load XRF Report</PrimaryBtn>
                        <canvas id="xrfReportCanvas" width="600" height="900"></canvas>
                    </div> -->
                </div>
                <!-- Other Resources Section END -->
                
            </div>

            <Title v-if="record.pass_fail === 'fail'">Hazard Photos</Title>

            <!-- Photos Section -->
            <div v-if="record.pass_fail === 'fail'" class="grid grid-cols-2 gap-4 mb-20">
                <div v-for="photo in record.photos" :key="photo.id" class="flex flex-col items-center justify-center">
                    <img v-if="photo.photo_path" 
                        :src="`/storage/${photo.photo_path}`" 
                        alt="Inspection Photo" 
                        class="w-full h-72 object-cover" 
                        loading="lazy" 
                        @error="handleImageError" />
                    <p v-else>No photo available</p>

                    <!-- Description below the image -->
                    <p class="mt-2 text-left text-sm text-gray-900">
                        {{ photo.description }}: {{ photo.room }} | {{ photo.component }} | {{ photo.hazard }}
                    </p>
                    <hr class="border-t-1 border-black w-full mb-2 mt-2"> <!-- Added w-full here -->

                </div>
            </div>

            <div class="mb-8 text-left w-full">
                <p class="text-left mb-2">
                {{ companyName }}<br>
                {{ companyAddress }}<br>
                {{ companyCity }}, {{ companyState }} {{ companyZip }}<br>
                Email: {{ companyEmail }}<br>
                Phone: {{ companyPhone }}
            </p>
            <p><strong>Prepared By: </strong>{{ companyName }}</p>{{ formatDate(record.selected_date) }}
            </div>

        </div>
    </Container>
</template>