<script setup>
import { ref } from 'vue';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";

// List of services
const services = [
    { name: 'Lead Inspection', description: 'Ensure your home is free of lead hazards.' },
    { name: 'Asbestos Management', description: 'Safe management and removal of asbestos.' },
    { name: 'Mold Assessment', description: 'Assessing and identifying mold problems.' },
    { name: 'Radon Control', description: 'Control and mitigate radon gas in your home.' },
    { name: 'Sewer Inspection & Cleaning', description: 'Inspection and cleaning of sewer systems.' },
    { name: 'Residential Tank Sweep', description: 'Inspection of underground tanks on residential properties.' },
    { name: 'Remediation & Consulting', description: 'Consulting and remediation services for environmental hazards.' }
];

// Track selected services
const selectedServices = ref([]);
  
// Toggle service selection
function toggleSelection(serviceName) {
  const index = selectedServices.value.indexOf(serviceName);
  if (index === -1) {
    selectedServices.value.push(serviceName);
  } else {
    selectedServices.value.splice(index, 1);
  }
}
</script>

<template>
    <Head title="- Multiple Services Request" />
    <Container class="w-3/5">
        <div class="mb-8 text-center">
            <Title>Multiple Services Request</Title>
        </div>

        <form class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <p class="text-slate-900 text-medium dark:text-slate-200">
                Click services to make more than one request. Selected request turns green.
            </p>

            <p class="text-slate-900 text-medium dark:text-slate-200 text-right">
                Require one service? <br>
                <!-- Button with a route using <Link> -->
                <Link :href="route('schedule.index')" 
                class="inline-block mt-2 py-2 px-6 bg-blue-500 text-white font-semibold rounded-lg text-center hover:bg-blue-600 transition">
                Single Service Request
                </Link>

            </p>
            </div>
            
            <!-- Services Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-8 dark:text-slate-700">
                <div 
                    v-for="service in services" 
                    :key="service.name" 
                    class="p-4 rounded-lg shadow-lg hover:shadow-xl transition cursor-pointer"
                    :class="{
                        'bg-green-500 text-white': selectedServices.includes(service.name), 
                        'bg-gray-200': !selectedServices.includes(service.name)
                    }"
                    @click="toggleSelection(service.name)"
                >
                    <h3 class="text-lg font-semibold">{{ service.name }}</h3>
                    <p class="text-sm mt-2">{{ service.description }}</p>
                </div>
            </div>

            <PrimaryBtn class="text-lg py-3 px-6 mx-auto block">Next</PrimaryBtn>


        </form>
    </Container>
</template>