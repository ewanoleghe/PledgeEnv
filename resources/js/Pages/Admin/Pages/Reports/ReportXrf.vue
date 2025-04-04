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
const companyURL = import.meta.env.VITE_APP_URL || 'Default Name';
const njdca = import.meta.env.VITE_COMPANY_NJDCA || 'Default NJDCA';

// Emit event to the parent
const emit = defineEmits();

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    record: Object,
    signatureUrl: String,
    xrfData: Array,
    pageName: String,
});

console.log(props.xrfData); // Check if the data is coming through

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
          <p class="text-2xl">Report Template - XRF</p>
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
            <p class="centered-text font-bold text-2xl mb-8">
                Lead-Based Paint Evaluation Report Inspection
            </p>
            <p class="text-left text-2sm mb-8">
                <span class="font-bold">Performed At:</span><br>
                {{ record.address }}, <span v-if="record.designation"><br>Unit: {{ record.designation }}, </span><br>
                {{ record.municipality }}, NJ {{ record.city }}
            </p>
            <p class="text-left text-2sm mb-2">
                <span class="font-bold">Performed For:</span><br>
                {{ record.property_owner_name }}<br>
                {{ record.address }}, <span v-if="record.designation">Unit: {{ record.designation }}, </span><br>
                {{ record.municipality }}, NJ {{ record.city }}
            </p>
            <p class="text-right font-bold text-2sm mb-4">
                {{ formatDate(record.selected_date) }}
            </p>          

            <div class="flex justify-end mt-48">
                <div class="border border-black rounded p-4">
                    <p class="text-left text-2sm">
                        <span class="font-bold">Prepared By:</span><br>
                        {{ companyName }}.<br>
                        {{ companyAddress }}<br>
                        {{ companyCity }}, {{ companyState }} {{ companyZip }}<br>
                        Phone {{ companyPhone }}<br>
                        Email {{ companyEmail }},<br>
                        Inspection Date: {{ formatDate(record.selected_date) }},<br>
                        Order Number: {{ record.order_id }}
                    </p>
                </div>
            </div>
        </div>
    </Container>

    <Container class="w-full max-w-4xl mx-auto mt-2">
        <div class="mt-3">
            <div class="container mx-auto p-6">
                <h2 class="text-3xl font-bold mb-6">Table of Contents</h2>
                <ul class="space-y-2">
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Contact Information</span>
                        <span class="text-gray-500">3</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Executive Summary</span>
                        <span class="text-gray-500">4</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Components with Lead Based Paint</span>
                        <span class="text-gray-500">4</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Regulatory Requirements</span>
                        <span class="text-gray-500">5</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Required Disclosure</span>
                        <span class="text-gray-500">5</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Required Training for Workers</span>
                        <span class="text-gray-500">5</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Controlling Lead-Based Paint</span>
                        <span class="text-gray-500">5</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Abatement for Lead-Based Paint Free Certification</span>
                        <span class="text-gray-500">6</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Component Removal</span>
                        <span class="text-gray-500">6</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Paint Stripping</span>
                        <span class="text-gray-500">6</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Procedures & Methodology</span>
                        <span class="text-gray-500">6</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Location Conventions</span>
                        <span class="text-gray-500">6</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Paint Testing</span>
                        <span class="text-gray-500">6</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">X-Ray Fluorescence</span>
                        <span class="text-gray-500">6</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg">Calibration Check Readings</span>
                        <span class="text-gray-500">7</span>
                    </li>
                    <li class="flex justify-between items-center border-t border-gray-300 pt-4">
                        <h2 class="text-3xl font-bold mb-2">Appendices</h2>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg ">Appendix A - Floor Plan</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-lg ">Appendix B - Lead-Based Paint Evaluation Report</span>
                    </li>
                </ul>
            </div>
        </div>
    </Container>

<Container class="w-full max-w-4xl mx-auto mt-2">
    <div class="mt-3">
        <div class="container mx-auto p-6">
            <h2 class="text-3xl font-bold mb-6">Contact Information
            </h2>
            <span class="text-lg font-bold">Site</span>
            <table class="w-full border border-gray-300 mb-5">
                <tbody>
                    <tr>
                        <td class="border-r border-gray-300 p-4 text-left">
                            Street Address 
                        </td>
                        <td class="p-4 text-left">
                            {{ record.address }}, <span v-if="record.designation">Unit: {{ record.designation }}, </span><br> {{ record.municipality }}, NJ {{ record.city }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <span class="text-lg font-bold">Property Owner</span>
            <table class="w-full border border-gray-300 mb-5 border-collapse">
                <tbody>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Name
                        </td>
                        <td class="p-2 text-left">
                            {{ record.property_owner_name }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Address
                        </td>
                        <td class="p-2 text-left">
                            {{ record.address }}, <span v-if="record.designation">Unit: {{ record.designation }}, </span><br> {{ record.municipality }}, NJ {{ record.city }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border-r border-gray-300 p-2 text-left">
                            Phone Number
                        </td>
                        <td class="p-2 text-left">
                            {{ record.property_owner_phone }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <span class="text-lg font-bold">Lead-Based Paint Inspector/Risk Assessor</span>
            <table class="w-full border border-gray-300 mb-5 border-collapse">
                <tbody>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Name
                        </td>
                        <td class="p-2 text-left">
                            {{ record.inspector_name }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Permit Number
                        </td>
                        <td class="p-2 text-left">
                            {{ record.license_number }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Instrumentation
                        </td>
                        <td class="p-2 text-left">
                            {{ "Viken Pb200e" }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Signature
                        </td>
                        <td class="p-2 text-left">
                            <!-- Display the inspector's signature -->
                        <img :src="signatureUrl" 
                            alt="Inspector Signature" 
                            @error="handleImageError" 
                            class="w-auto h-auto max-h-16 object-contain" />
                        </td>
                    </tr>
                    <tr>
                        <td class="border-r border-gray-300 p-2 text-left">
                            Date
                        </td>
                        <td class="p-2 text-left">
                            {{ formatDate(record.selected_date) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <span class="text-lg font-bold">Firm</span>
            <table class="w-full border border-gray-300 mb-5 border-collapse">
                <tbody>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Organization:
                        </td>
                        <td class="p-2 text-left">
                            {{ companyName }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Certification #:
                        </td>
                        <td class="p-2 text-left">
                            NJDCA {{ njdca }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Sreet:
                        </td>
                        <td class="p-2 text-left">
                            {{ companyAddress }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            City, State & Zip: 
                        </td>
                        <td class="p-2 text-left">
                            {{ companyCity }}, {{ companyState }} {{ companyZip }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <td class="border-r border-gray-300 p-2 text-left">
                            Phone Number:
                        </td>
                        <td class="p-2 text-left">
                            {{ companyPhone }}                        
                        </td>
                    </tr>
                    <tr>
                        <td class="border-r border-gray-300 p-2 text-left">
                            Web Address:
                        </td>
                        <td class="p-2 text-left">
                            {{ companyURL }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</Container>

<Container class="w-full max-w-4xl mx-auto mt-2">

        <div class="flex flex-col items-end mb-4">
            <img :src="companyLogo" alt="Company Logo" class="w-52 h-auto mb-2">
            <p class="text-right">
                {{ companyAddress }}<br>
                {{ companyCity }}, {{ companyState }} {{ companyZip }}<br>
                Email: {{ companyEmail }}<br>
                Phone: {{ companyPhone }}
            </p>
        </div>

        <div class="mb-2 mt-8 text-center w-full">
            <p class="centered-text font-bold text-2xl mb-2">
                Executive Summary
            </p>
            <p class="centered-text text-2sm mb-2">
                {{ record.address }}, 
                <span v-if="record.designation">Unit: {{ record.designation }}, </span>
                {{ record.municipality }}, NJ {{ record.city }}
            </p>
            <p class="centered-text font-bold text-2sm mb-4">
                {{ formatDate(record.selected_date) }}
            </p>           

            <p class="text-left text-2sm leading-relaxed mb-4">
                On {{ formatDate(record.selected_date) }}, {{ companyName }} [NJDCA {{ njdca }}] performed a lead-based paint inspection at {{ record.address }}, <span v-if="record.designation">Unit: {{ record.designation }}</span>,  {{ record.municipality }}, NJ {{ record.city }}; (the “property”).  The lead-based paint inspection sampling protocol that was applied follows “Inspections in Single-Family Housing” Chapter 7 of the HUD Guidelines (2012 revision) and the protocol as referenced in USEPA 40 CFR Part 745.227(b). See Appendix B Lead Paint Inspection Report for the complete set of X-Ray Fluorescence data. 
            </p>
            <p v-if="record.xrf_pass_fail === 'fail'" class="text-left text-2sm leading-relaxed mb-4">
                The tables below indicate the location of the lead-based paint found. Each positive reading applies to
                all similar components in the same room equivalent (room, hall, stairwell, building exterior, etc.) For
                a lead-based paint free certification, the lead must be stripped or the leaded component replaced
                and confirmation achieved. Enclosure and encapsulation are not acceptable methods for a leadbased paint free certification. If no lead-based paint was identified, the table will list “None” and the
                dwelling unit is considered lead-based paint free.  
            </p>
            
            <div v-if="record.xrf_pass_fail === 'fail'" class="mb-8 mt-8 text-center w-full">
                <table class="min-w-full border-collapse text-xs">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-black text-center text-x border border-black">Room Equivalent</th>
                            <th class="text-black text-center text-x border border-black">Component</th>
                            <th class="text-black text-center text-x border border-black">Substrate</th>
                            <th class="text-black text-center text-x border border-black">Wall</th>
                            <th class="text-black text-center text-x border border-black">Value (mg/cm²)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in xrfData" :key="index">
                            <td class="text-black text-center border border-black">{{ item.Room }}</td>
                            <td class="text-black text-center border border-black">{{ item.Member }}</td>
                            <td class="text-black text-center border border-black">{{ item.Substrate }}</td>
                            <td class="text-black text-center border border-black">{{ item.Wall }}</td>
                            <td class="text-black text-center border border-black">{{ item.Concentration }}</td>
                        </tr>
                        <tr v-if="xrfData.length === 0">
                            <td colspan="5" class="text-center font-bold text-sm">No data available.</td>
                        </tr>
                    </tbody>
                </table>
                </div>

            <p class="text-left text-2sm leading-relaxed mb-20">
                EPA 40 CFR 745.227(h) states lead-based paint is present on any surface that is tested and found to
                contain lead equal to or in excess of 1.0 milligrams per square centimeter or equal to or in excess of
                0.5% by weight. Local thresholds may be lower than this Federal standard. 
            </p>
        </div>
    </Container>

<Container class="w-full max-w-4xl mx-auto mt-2">
        <div class="container mx-auto p-6">
            <h2 class="text-xl font-bold mb-6">Regulatory Requirements</h2>
            <span class="text-sm font-bold italic">Required Disclosure</span>
            

            <p class="text-left text-2sm leading-relaxed mb-4">
                A summary of this lead-based paint evaluation report must be provided to new lessees (tenants). A
                complete copy of this report must be provided to purchasers and owners of this property and it must
                be made available to new tenants under federal law (24 CFR PART 35 AND 40 CFR PART 745)
                before they become obligated under a lease or sales contract. Landlords (lessors) and sellers are
                also required to distribute an educational pamphlet approved by the U.S. Environmental Protection
                Agency and include standard warning language in their leases or sales contracts to ensure that
                parents have the information they need to protect their children from lead-based paint hazards.”
            </p>

            <p class="text-left text-2sm leading-relaxed mb-4">
                Should the recipient of this report receive federal subsidy they are responsible to comply with all
                requirements of 24 CFR Part 35 Requirements for the Notification, Evaluation and Reduction of
                Lead-Based Paint Hazards in Federally Owned Residential Property and Housing Receiving Federal
                Assistance; Final Rule which, are applicable to the type of program they are participating in and the
                dollar amount of subsidy being received. If this property or any of its tenants receives financial
                federal assistance, the results of the evaluation or hazard reduction activities must be provided by
                the designated party (client) to the owner of the referenced property and the occupants within 15
                calendar days of the date when the designated party receives this report, or makes the presumption
                that lead-based paint hazards do exist.  
            </p>

            <span class="text-sm font-bold italic">Required Training for Workers</span>

            <p class="text-left text-2sm leading-relaxed mb-4">
                Should the lead-based paint and lead hazard reduction activities be part of a program which receives
                federal subsidy or a New Jersey multifamily building, all persons performing “Interim Controls” or
                “Standard Treatments” must be trained in accordance with 29 CFR 1926.59 and be supervised by an
                individual who successfully completed one of the following courses:
            </p>

            <div class="max-w-3xl mx-auto bg-white rounded-lg mb-4">
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>A lead-based paint abatement supervisors course accredited in accordance with 40 CFR 745.225</li>
                    <li>A lead-based paint abatement worker course accredited in accordance with 40 CFR 745.225</li>
                    <li>The lead-based paint Maintenance Training Program, “Work Smart, Work Wet, and Work Clean to Work Lead Safe”, prepared by the National Environmental Training Association for EPA and HUD</li>
                    <li>“The Remodeler’s and Renovator’s Lead-Based Paint Training Program,” prepared by HUD and the National Association of the Remodeling Industry</li>
                    <li>Another course approved by HUD for this purpose after consultation with EPA.</li>
                </ol>
            </div>

            <p class="text-left text-2sm leading-relaxed mb-4">
                In accordance with Section 35.1340 all Lead-Based Paint and Lead Hazard reduction activities,
                which are not exempt (see regulations) require Lead Dust Wipe Clearance testing by a 1) certified
                lead inspector, 2) certified risk assessor or 3) a dust wipe sampling technician whose work is
                reviewed by a certified risk assessor.
                <br /><br />
                If a renovation at the property is to occur, all work should comply with 40 CFR 745 Subpart EResidential Property Renovation.
            </p>

            <span class="text-lg font-bold">Controlling Lead-Based Paint</span>
            
            <p class="text-left text-2sm leading-relaxed mb-4">
                There are different options available for controlling lead-based paint. Each option has its own
                associated costs and benefits both short and long term. In most cases, a combination of the options
                can be implemented to reduce the possibility of lead contamination. {{ companyName }}. 
                strongly suggests that each option is thoroughly contemplated before beginning any activity.
                <br /><br />
                Components that are found to be positive for lead-based paint should be checked for deterioration.
                Lead-based paint in deteriorated condition is considered a paint-lead hazard. Those components
                should be address as soon as possible using lead safe work practices at a minimum. However, if any
                components are found to test positive for lead based paint, they should be considered for future
                component removal or paint stripping.
            </p>

            <span class="text-3sm font-bold italic">Abatement for Lead-Based Paint Free Certification</span>
            <br /><br />

            <span class="text-sm font-bold">Component Removal</span>
            
            <p class="text-left text-2sm leading-relaxed mb-4">
                Component removal is a permanent solution to the issue of potential exposure of lead. It requires
                taking the old lead-based painted component out and replacing it with a new non-lead painted
                component. The cost associated with this option depends mostly on the cost of the replacement
                component. Since labor is most often the more costly aspect of controlling lead issues, many owners
                choose component removal over more labor intensive methods. Components often chosen for
                removal are wood trim, windows, most doors, and exterior railings. Plaster and drywall ceilings and
                walls, fire rated doors, and wood porch components should also be considered.
            </p>

            <span class="text-sm font-bold">Paint Stripping</span>
            <p class="text-left text-2sm leading-relaxed mb-4">
                Paint stripping is a permanent solution to the issue of potential exposure of lead. The paint can be
                removed either in-place or by an off-site processing facility. In-place removal can be mechanical or
                chemical. In-place paint stripping has the issue of proper disposal of the hazardous waste
                generated.
                <br /><br />
                Mechanical stripping scrapes the paint off the substrate. Most times dry scraping is prohibited, but
                sanding or scraping can be done in conjunction with engineering controls to reduce airborne and
                settled lead dust. Power tools used to remove the paint must be equipped with a HEPA filtered
                shroud. Wetting a surface and hand scraping is also permitted. The components most often chosen
                for hand scraping are window and door jambs. Power tools are better equipped to handle lager
                surface areas.
                <br /><br />
                Chemical stripping in-place uses strong chemicals to soften the paint for easier removal from the
                substrate. The chemicals are either very acidic or very basic, so proper training and protection for
                the worker is imperative. Generally, the chemicals must remain in- place overnight, so maintaining a
                secure worksite separate from occupants is mandatory.
                <br /><br />
                Off site facilities use much stronger chemicals to remove the lead-based paint from the component.
                Components often chosen for off-site paint removal are intricate metal pieces. Sometimes this
                method is used for intricate wood work, but the stronger chemicals soften the wood and can drive
                lead into the wood while removing the paint.
            </p>

            <span class="text-3sm font-bold italic">Procedures & Methodology</span>
            <br /><br />

            <span class="text-sm font-bold">Location Conventions</span>
            
                <p class="text-left text-2sm leading-relaxed mb-4">
                    When reviewing Appendix A “Floor Plan” and Appendix B “Lead-Based Paint Evaluation Report”, you
                will notice that the letters A, B, C, and D or the numbers 1, 2, 3 and 4 are used to identify the
                location of specific components. The key to correct orientation is the location of the “A” or “1” wall.
                The “B” or “2” wall, “C” or “3” wall, and “D” or “4” wall run clockwise from the “A” or “1” wall. The
                Lead-Based Paint Evaluation Report lists this information under the “Wall” column. The “Location”
                column uses numbering of replicated components starting with “1” at left and continuing
                sequentially to right respectively to describe the location of the component while facing the wall
                identified.
                </p>
                <p class="text-sm font-bold mb-4">Paint Testing</p>
                <p class="text-sm font-bold mb-4">X-Ray Fluorescence</p>

                <p class="text-left text-2sm leading-relaxed mb-4">
                X-Ray Fluorescence (XRF) paint testing is performed to detect the presence of lead on painted
                surfaces. The XRF instrument is state-of-the art equipment. XRF testing is usually the preferred
                method of testing, because it is non-destructive, quantitative and can be performed on the spot with
                acceptable accuracy. {{ companyName }}’s evaluators follow the manufacturer’s
                suggested use and the Performance Characteristic Sheet of the XRF instrument being used. The
                results of the XRF testing are the basis for drawing conclusions and making recommendations in the
                report.
                <br /><br />
                All {{ companyName }}’s evaluators follow 40 CFR 745 and the HUD Guidelines for
                testing lead using an XRF instrument. All federal, state and city regulations are followed when
                applicable. The evaluator will test one of each and every different type of testing combination
                (component) in each room being surveyed. Each XRF reading is assigned an exclusive sample
                reference number and a measurement that is stored in the instrument. Each sample reference
                number location is logged on the XRF instrument for future reference, testing location, and report
                generation. The above described testing format is followed unless otherwise not practical or if the
                evaluator’s judgment decides to test in a different systematic approach.
                <br/><br/>
                It should be noted that detected lead levels below current levels still could create lead dust or lead
                contaminated soil hazards if the paint is turned into dust by abrasion, scraping, or sanding leading
                to possible elevated blood lead levels. Lead poisoning is a cumulative affect. Should a child or an
                adult inhale or ingest sufficient quantities of low concentrations of leaded paint, dust, or soil, it will
                accumulate in the body’s systems and could eventually cumulate to an elevated blood level of
                concern.
                </p>

                <p class="text-sm font-bold mb-4">Any untested building components should be considered lead-based paint until tested.</p>
                <p class="text-sm font-bold mb-4">Calibration Check Readings</p>

                <p class="text-left text-2sm leading-relaxed mb-4">
                    In addition to the manufacturer’s recommended warm up and quality control procedures, {{ companyName }}. 
                    collects quality control readings as recommended in the HUD
                    Guidelines. Quality control for XRF instrumentation instruments involves readings to check
                    calibration.
                <br /><br />
                    For each XRF instrument, one set of XRF calibration check readings are recommended at least every
                    four hours. The first is a set of three nominal-time or source decay corrected time XRF calibration
                    check readings to be taken before the inspection begins for the day. The second occurs either after
                    the day’s inspection work has been completed, or at least every four hours, whichever occurs first.
                    {{ companyName }}’s XRF calibration check readings are taken on the Standard
                    Reference Material (SRM) paint film nearest to 1.0 mg/cm<sup>2</sup>
                    within the National Institute of
                    Standards and Technology (NIST) SRM Used or the XRF manufacturer’s factory supplied SRM film.
                    Three readings are collected on the SRM. The average of the three readings on the SRM must be
                    within the acceptable plus and minus tolerances for proper calibration as detailed in the
                    Performance Characteristic Sheet (PCS). All calibration checks are taken with the SRM film
                    positioned at least several inches away from any potential source of lead.
                <br/><br/>
                    Three readings are taken each time calibration check readings are made. The average of the
                    readings are compared to the known value and if the average value is within the acceptable
                    calibration check tolerance specified in the XRF Performance Characteristic Sheet the instrument is
                    considered in control. If the average readings are not within the calibration check tolerance the
                    instrument is not used until the instrument is brought back into control.
                </p>


            <!-- Other Resources Section -->
            <Title>Other Resources</Title>
            <div class="mb-8">
                
                <div>
                <h3>Floor Plan:</h3>
                <a :href="`/storage/${record.floor_plan}`" target="_blank" class="text-blue-500 underline">
                    Open Floor Plan
                </a>                
                </div><br>
                <div v-if="record.includeXrf">
                <h3>XRF Report:</h3>
                <a :href="`/storage/${record.xrf_report}`" target="_blank" class="text-blue-500 underline">
                    Open XRF Report
                </a>
                </div>

                <!-- Other Resources Section 2 -->
                <Title class="mt-8">Other Resources 2</Title>
                <div>
                    <!-- Display PDF or Image as Embedded File -->
                    <h3>Floor Plan (Embedded):</h3>
                    <PrimaryBtn @click="renderFile(`/storage/${record.floor_plan}`, 'floorPlanCanvas')" class="font-bold">Load Floor Plan</PrimaryBtn> <canvas id="floorPlanCanvas" width="600" height="950"></canvas>
                   
                    <div v-if="record.includeXrf" class="mb-8">
                        <h3 class="mt-8">XRF Report (Embedded):</h3>
                        <PrimaryBtn @click="renderFile(`/storage/${record.xrf_report}`, 'xrfReportCanvas')" class="font-bold">Load XRF Report</PrimaryBtn>
                        <canvas id="xrfReportCanvas" width="600" height="900"></canvas>
                    </div>
                </div>
                <!-- Other Resources Section END -->
                <!-- Other Resources Section END -->
                <!-- if xrf_pass_fail === 'fail' VISUAL REPORT -->
                <!-- if xrf_pass_fail === 'pass' LOAD Lead Free -->
                
            </div>
        </div>
    </Container>
</template>