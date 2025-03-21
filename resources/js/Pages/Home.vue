<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import Container from "../Components/FullContainer.vue";
import Title from "../Components/Title.vue";
import { useForm } from "@inertiajs/vue3";

// Define image reference
// const homeImg = "/images/home.jpg";  // Direct reference to the public folder image

// Access the APP_NAME environment variable directly
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name'; // Default if not set

// Array of background images
const images = [
  "/images/home.jpg", // Image 1
  "/images/home2.jpg", // Image 2
  "/images/home3.jpg", // Image 3
  "/images/home4.jpg", // Image 4
  "/images/home5.jpg", // Image 5
  "/images/home6.jpg", // Image 6
];

const currentImageIndex = ref(0);

// Function to change the image
const changeImage = (direction) => {
  // Update the index with wrap-around logic
  currentImageIndex.value = (currentImageIndex.value + direction + images.length) % images.length;
};

// Services array with names and identifiers
const services = [
  { name: 'Lead Inspection' },
  { name: 'Lead Inspection NY' },
];

// Create a form with the `useForm` hook
const form = useForm({
  service: "",
});

// Handle form submission
const submitService = (serviceName) => {
  form.service = serviceName;

  form.post(route("request.service"), {
    method: "POST",
    data: form,
    onSuccess: () => {
      // Handle success here
    }
  });
};

// Computed property for the current image style
const currentImageStyle = computed(() => ({
  backgroundImage: `url(${images[currentImageIndex.value]})`
}));

// Change image every 3 seconds (automatically)
let intervalId;

onMounted(() => {
  // Change the image every 3 seconds
  intervalId = setInterval(() => {
    changeImage(1); // Move to the next image automatically
  }, 3000); // 3000ms = 3 seconds
});

onBeforeUnmount(() => {
  // Clear the interval when the component is destroyed
  clearInterval(intervalId);
});
</script>

<template>
    <Head title="- Home" />
    
    <!-- First Container with Dynamic Background Image -->
    <Container 
      class="w-full bg-cover bg-center relative sm:bg-contain sm:min-h-[400px] md:min-h-[700px] sm:w-full" 
      :style="currentImageStyle">
    <!-- <Container 
      class="w-full bg-cover bg-center relative sm:bg-contain sm:min-h-[400px] md:min-h-[700px] sm:w-full" 
      :style="{ backgroundImage: `url(${homeImg})` }"></Container> -->

        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="relative z-10 mb-8 text-center">
            <Title class="text-center justify-center font-bold text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-slate-100">
                {{ companyName.toUpperCase() }}
            </Title>
        </div>

        <!-- Navigation Arrows -->
        <div class="absolute left-1 top-1/2 transform -translate-y-1/2 z-20"> <!-- Add z-index here -->
            <button @click="changeImage(-1)" class="p-4 text-white bg-black bg-opacity-50 hover:bg-opacity-75 transition text-4xl">
                &lt; <!-- Left Arrow -->
            </button>
        </div>
        <div class="absolute right-1 top-1/2 transform -translate-y-1/2 z-20"> <!-- Add z-index here -->
            <button @click="changeImage(1)" class="p-4 text-white bg-black bg-opacity-50 hover:bg-opacity-75 transition text-4xl">
                &gt; <!-- Right Arrow -->
            </button>
        </div>

        <!-- Main Description -->
        <div class="relative z-10 grid grid-cols-1 gap-8 mb-10 pb-10 px-4 sm:px-8 md:px-12">
            <p class="text-center justify-center font-bold text-2xl text-slate-100">
                Environmental Consulting for residential and commercial buildings.
            </p>
            <p class="text-center justify-center font-bold text-2xl text-slate-100 mb-10 pb-4">
                NEW JERSEY & NEW YORK.
            </p>
            <p class="text-center justify-center font-bold text-xl text-slate-100 pb-5">
                Our Inspection Certificates are accepted in all Municipalities.
            </p>
            <p class="text-center justify-center font-bold text-2xl text-slate-100"> 
                <!-- There are fines for failing a lead paint inspection, filing late, or failing to file. To ensure compliance with the new lead regulations;<br>
                Book your lead inspection Now. -->
                There are fines for failing to complete a lead-based paint inspection for pre-1978 rental properties. To ensure compliance with the new lead regulations;<br>
                Book your lead inspection Now.
            </p>

            <!-- New Button Row for NJ and NY Inspection -->
            <div class="flex flex-col items-center mb-2">
                <p class="text-center font-medium text-2xs text-slate-100 mb-2">
                    [Our pricing match any competitors price in the state of NJ and NY.]
                </p>
                <div class="flex space-x-4">
                    <button @click="submitService(services[0].name)" class="p-4 font-bold text-white bg-green-600 hover:bg-green-800 transition">
                        NJ LEAD INSPECTIONS
                    </button>
                    <button @click="submitService(services[1].name)" class="p-4 font-bold text-white bg-green-600 hover:bg-green-800 transition">
                        NY LEAD INSPECTIONS
                    </button>
                </div>
            </div>
        </div>

        <!-- Single Row with Multiple Buttons, positioned at the bottom of the image -->
        <div class="absolute bottom-0 left-0 right-0 z-10 flex flex-wrap bg-opacity-75 bg-black mt-8">
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Lead Based<br> Paint Inspection
            </button>
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Lead in<br> Drinking Water
            </button>
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Asbestos<br> Testing
            </button>
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Mold<br> Testing
            </button>
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Radon<br> Control
            </button>
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Sewer<br> Inspection
            </button>
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Residential<br> Tank Sweep
            </button>
            <button class="flex-1 p-4 font-bold text-white bg-red-600 hover:bg-red-800 transition">
                Remediation<br> & Consulting
            </button>
        </div>
    </Container>

    <!-- Second Container -->
    <Container class="w-full min-h-screen mt-8">
        <div class="text-center">
            <div class="flex justify-center flex-wrap">
                <div class="flex-1 min-w-[250px] p-4 h-[500px">
                    <h2 class="font-bold text-2xl text-slate-900 dark:text-slate-100">The Lead-Based Paint Inspection Law NJ & NY</h2>
                    <div class="w-full h-full p-4 font-medium text-slate-900 bg-slate-100 hover:bg-slate-200 transition rounded-lg text-left dark:text-slate-900">
                        <p class="mb-5"> The Lead-based Paint Inspection Law 
                            <a href="https://www.nj.gov/dca/codes/resources/leadpaint.shtml" target="_blank" rel="noopener noreferrer">
                                [ <strong>NJ</strong>: P.L.2021, c.182 <i class="fas fa-external-link-alt"></i> ]
                            </a>   
                            and 
                            <a href="https://www.nyc.gov/site/hpd/services-and-information/lead-based-paint.page" target="_blank" rel="noopener noreferrer">
                                [ <strong>NY</strong>: Local Law 31 <i class="fas fa-external-link-alt"></i> ]
                            </a>
                            addresses lead-based paint hazards in residential rental property and establishes lead-based paint hazard control work programs.
                        </p>
                        <p class="mb-5">
                            So, what does this Lead Safe Certification law require of a property owner?
                        </p>
                        <div class="mb-5">
                            If you rent a pre-1978 property in NJ & NY that does not qualify for one of several exemptions, as of July 2022, you are required to have a lead-paint inspection (interiors, common areas and [exteriors / out buildings in certain conditions]) upon tenancy turnover of each unit or every three years, which ever happens first. You must obtain a Lead Safe Certification or Lead-Free Certification to rent a pre-1978 property.
                        
                            <ul class="mt-5 mb-5 list-none">
                                <li class="relative pl-5 mb-3">
                                    <span class="absolute left-0 text-black text-2xl">•</span>
                                    <strong>Lead-SAFE Certification</strong> is issued where, upon inspection, a lead paint hazard(s) is/are found on the property by state certified professional but is safely treated and requires no further action at the time of inspection or occupancy turn-over. This certification type is only good for two years and will expire.
                                </li>
                                <li class="relative pl-5 mb-3">
                                    <span class="absolute left-0 text-black text-2xl">•</span>
                                    <strong>Lead-FREE Certification</strong> is issued where, upon inspection, no lead paint is found anywhere on the property by a state certified professional (with Handheld / portable X-ray fluorescent (XRF) analyzers). This certification type is good for the life of the property and does not expire unless a lead-based paint illness is reported to the State.
                                </li>
                            </ul>
                        </div>

                        <p class="mb-5">
                            If you have not tested for lead based paint and the property is pre-1978 original construction, the law states you must assume all interior and exterior paint on the property is lead-based paint. 
                        </p>

                        <div class="flex flex-col space-y-4 items-center">
                            <p class="text-center font-bold">Schedule your Lead Inspection</p>
                            <div class="flex space-x-4">
                                <button @click="submitService(services[0].name)" class="p-4 font-medium text-white bg-green-600 hover:bg-green-800 transition">
                                    NJ LEAD INSPECTIONS
                                </button>
                                <button @click="submitService(services[1].name)" class="p-4 font-medium text-white bg-green-600 hover:bg-green-800 transition">
                                    NY LEAD INSPECTIONS
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="flex-1 min-w-[250px] p-4 h-[500px">
                    <h2 class="font-bold text-2xl text-slate-900 dark:text-slate-100">We are State certified to provide Lead Safe/Free Certificates</h2>
                    <div class="w-full h-full p-4 font-medium text-slate-900 bg-slate-100 hover:bg-slate-200 transition rounded-lg text-left dark:text-slate-900">
                        All our highly efficient, Inspectors are licensed and certified as Lead Inspector/Risk Assessors (L.I.R.A.), by the New Jersey Department of Health pursuant to their regulations at N.J.A.C. 8:62, adopted under the authority of N.J.S.A. 26:2Q-1, et seq. and the U.S. Environmental Protection Agency (EPA) pursuant to their regulations at 40 CFR Part 745.225.

                        <p class="mt-5">
                            We are also certified to provide Lead Safe/Free Certificates for the State of New York.
                        </p>

                            <ul class="mt-5 mb-5 list-none">
                                <li class="relative pl-5 mb-3">
                                    <span class="absolute left-0 text-black text-2xl font-bold">•</span>
                                    We can handle all of your lead service needs. From landlord compliance, home purchases, to elevated Blood Lead Levels.
                                </li>
                                <li class="relative pl-5 mb-3">
                                    <span class="absolute left-0 text-black text-2xl font-bold">•</span>
                                    Using the most advanced techniques and equipment available today, Our inspectors can conduct a thorough inspection usually within 24 hours of your request, and at competitive pricing.
                                </li>
                                <li class="relative pl-5 mb-3">
                                    <span class="absolute left-0 text-black text-2xl font-bold">•</span>
                                    We completely document the condition of the property and issue a comprehensive report detailing the results.
                                </li>
                            </ul>

                            <div>
                                <h3 class="font-bold text-4xl">Our Mission</h3>
                                <p class="mt-5">
                                    Whether it is a Lead Based Paint Inspection, Lead in Drinking Water, Asbestos Testing, Mold Testing, Radon Control, Sewer Inspection, Residential Tank Sweep or Remediation & Consulting that you need, we will make it our mission to find you the most affordable and effective way to eliminate any concern or violation without compromising our superior standard of quality. We will make sure you remain legally compliant so you can remain focused on your goals.
                                </p>
                                <Link :href="route('home')" class="mt-5 inline-block px-6 py-2 text-black bg-white border border-black rounded hover:bg-yellow-400 hover:text-black transition">
                                    Read More About Us
                                </Link>
                            </div>
                    </div>
                </div>
                
            </div>
        </div>
    </Container>
    
    <!-- Third Container -->
    <Container class="w-full mt-8">
    <div class="text-center">
        <div class="flex justify-center flex-wrap">
            <div class="flex-1 min-w-full sm:min-w-[250px] p-4">
                <h2 class="font-bold text-3xl sm:text-2xl text-slate-900 dark:text-slate-100">Areas We Serve</h2>
                <div class="w-full p-4 font-medium text-slate-900 bg-slate-100 hover:bg-slate-200 transition rounded-lg text-left dark:text-slate-900">
                    <p class="mb-5 font-bold text-3xl">
                        We provide services in the following areas:
                    </p>
                    <div class="flex flex-col sm:flex-row justify-between">
                        <!-- Column 1: New Jersey -->
                        <div class="flex flex-col w-full sm:w-1/2 space-y-4 mb-4 sm:mb-0">
                            <h3 class="font-bold text-4xl">The State of New Jersey</h3>
                            <div class="flex flex-col sm:flex-row justify-between w-full">
                                <!-- Sub-column 1 for NJ -->
                                <div class="flex flex-col space-y-2 w-full sm:w-1/2">
                                    <h4 class="font-bold">All Counties in NJ</h4>
                                    <ul class="list-disc list-inside">
                                        <li>Atlantic County</li>
                                        <li>Bergen County</li>
                                        <li>Burlington County</li>
                                        <li>Camden County</li>
                                        <li>Cape May County</li>
                                        <li>Cumberland County</li>
                                        <li>Essex County</li>
                                    </ul>
                                </div>
                                <!-- Sub-column 2 for NJ -->
                                <div class="flex flex-col space-y-2 w-full sm:w-1/2 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Gloucester County</li>
                                        <li>Hudson County</li>
                                        <li>Hunterdon County</li>
                                        <li>Mercer County</li>
                                        <li>Middlesex County</li>
                                        <li>Monmouth County</li>
                                        <li>Morris County</li>
                                        <li>Ocean County</li>
                                        <li>Passaic County</li>
                                        <li>Salem County</li>
                                        <li>Somerset County</li>
                                        <li>Sussex County</li>
                                        <li>Union County</li>
                                        <li>Warren County</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Column 2: New York -->
                        <div class="flex flex-col w-full sm:w-1/2 space-y-4">
                            <h3 class="font-bold text-4xl">The State of New York</h3>
                            <div class="flex flex-col sm:flex-row justify-between w-full">
                                <!-- Sub-column 1 for NY -->
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3">
                                    <h4 class="font-bold">All Counties in NY</h4>
                                    <ul class="list-disc list-inside">
                                        <li>Albany County</li>
                                        <li>Allegany County</li>
                                        <li>Bronx County</li>
                                        <li>Broome County</li>
                                        <li>Cattaraugus County</li>
                                        <li>Cayuga County</li>
                                        <li>Chautauqua County</li>
                                    </ul>
                                </div>
                                <!-- Sub-column 2 for NY -->
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Chemung County</li>
                                        <li>Chenango County</li>
                                        <li>Clinton County</li>
                                        <li>Columbia County</li>
                                        <li>Cortland County</li>
                                        <li>Delaware County</li>
                                        <li>Dutchess County</li>
                                    </ul>
                                </div>
                                <!-- Sub-column 3 for NY -->
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Erie County</li>
                                        <li>Essex County</li>
                                        <li>Franklin County</li>
                                        <li>Fulton County</li>
                                        <li>Genesee County</li>
                                        <li>Greene County</li>
                                        <li>Hamilton County</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-between w-full mt-4">
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3">
                                    <ul class="list-disc list-inside">
                                        <li>Herkimer County</li>
                                        <li>Jefferson County</li>
                                        <li>Kings County (Brooklyn)</li>
                                        <li>Lewis County</li>
                                        <li>Livingston County</li>
                                    </ul>
                                </div>
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Madison County</li>
                                        <li>Monroe County</li>
                                        <li>Montgomery County</li>
                                        <li>Nassau County</li>
                                        <li>New York County (Manhattan)</li>
                                    </ul>
                                </div>
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Niagara County</li>
                                        <li>Oneida County</li>
                                        <li>Onondaga County</li>
                                        <li>Ontario County</li>
                                        <li>Orange County</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-between w-full mt-4">
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3">
                                    <ul class="list-disc list-inside">
                                        <li>Orleans County</li>
                                        <li>Oswego County</li>
                                        <li>Otsego County</li>
                                        <li>Putnam County</li>
                                        <li>Queens County</li>
                                    </ul>
                                </div>
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Rensselaer County</li>
                                        <li>Richmond County (Staten Island)</li>
                                        <li>Rockland County</li>
                                        <li>Saint Lawrence County</li>
                                        <li>Saratoga County</li>
                                    </ul>
                                </div>
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Schenectady County</li>
                                        <li>Schoharie County</li>
                                        <li>Schuyler County</li>
                                        <li>Seneca County</li>
                                        <li>Steuben County</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-between w-full mt-4">
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3">
                                    <ul class="list-disc list-inside">
                                        <li>Suffolk County</li>
                                        <li>Sullivan County</li>
                                        <li>Tioga County</li>
                                        <li>Tompkins County</li>
                                        <li>Ulster County</li>
                                    </ul>
                                </div>
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Warren County</li>
                                        <li>Washington County</li>
                                        <li>Wayne County</li>
                                        <li>Westchester County</li>
                                        <li>Wyoming County</li>
                                    </ul>
                                </div>
                                <div class="flex flex-col space-y-2 w-full sm:w-1/3 mt-4 sm:mt-0">
                                    <ul class="list-disc list-inside">
                                        <li>Yates County</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-4 items-center mt-4">
                        <p class="text-center font-bold text-lg">Schedule your Lead Inspection</p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <button @click="submitService(services[0].name)" class="w-full sm:w-auto p-4 font-medium text-white bg-green-600 hover:bg-green-800 transition rounded-lg">
                                NJ LEAD INSPECTIONS
                            </button>
                            <button @click="submitService(services[1].name)" class="w-full sm:w-auto p-4 font-medium text-white bg-green-600 hover:bg-green-800 transition rounded-lg">
                                NY LEAD INSPECTIONS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</Container>
</template>

