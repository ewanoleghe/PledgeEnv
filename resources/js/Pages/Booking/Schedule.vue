<script setup>
import { ref } from 'vue';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import { useForm } from "@inertiajs/vue3";

// List of services
const services = [
  { name: 'Lead Inspection', description: 'Ensure your home is free of lead hazards.' },
  { name: 'Asbestos Management', description: 'Safe management and removal of asbestos.' },
  { name: 'Mold Assessment', description: 'Assessing and identifying mold problems.' },
  { name: 'Radon Control', description: 'Control and mitigate radon gas in your home.' },
  { name: 'Sewer Inspection & Cleaning', description: 'Inspection and cleaning of sewer systems.' },
  { name: 'Residential Tank Sweep', description: 'Inspection of underground tanks on residential properties.' },
  { name: 'Remediation & Consulting', description: 'Consulting and remediation services for environmental hazards.' },
  { name: 'Lead in Drinking Water', description: 'We provides comprehensive testing services for copper and lead in drinking water.' }
];

// Create a form with the `useForm` hook
const form = useForm({
  service: "", // This will hold the selected service
});

// Track selected service
const selectedService = ref(null);

// Toggle service selection (only one can be selected at a time)
function toggleSelection(serviceName) {
  // If the same service is clicked again, deselect it
  if (selectedService.value === serviceName) {
    selectedService.value = null;
  } else {
    selectedService.value = serviceName;
  }
}

// Handle form submission
const submit = () => {
  if (selectedService.value) {
    form.service = selectedService.value;

    form.post(route("request.service"), {
      method: "POST",  // Ensure method is POST
      data: form,  // Send form data as payload
      onError: (errors) => {
        console.error('There was an error submitting the form:', errors);
      },
      onFinish: () => {
        console.log('Form submission completed!');
      }
    });
  } else {
    alert('Please select a service');
  }
};

</script>


<template>
    <Head title="- Single Service Request" />
  <Container class="w-3/5">
    <div class="mb-8 text-center">
      <Title>Single Service Request</Title>
    </div>

    <!-- Form to submit a single service -->
    <form @submit.prevent="submit" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <p class="text-slate-900 text-medium dark:text-slate-200">
          <strong>Select the Type of Service Desired</strong>. Service request turns green.
        </p>

        <p class="text-slate-900 text-medium dark:text-slate-200 text-right">
          Require more than one request? <br>
          <Link :href="route('schedule.many')" class="inline-block mt-2 py-2 px-6 bg-blue-500 text-white font-semibold rounded-lg text-center hover:bg-blue-600 transition">
            Multiple Services Request
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
            'bg-green-500 text-white': selectedService === service.name, 
            'bg-gray-200': selectedService !== service.name
          }"
          @click="toggleSelection(service.name)"
        >
          <h3 class="text-lg font-semibold">{{ service.name }}</h3>
          <p class="text-sm mt-2">{{ service.description }}</p>
        </div>
      </div>

      <!-- Submit Button -->
      <PrimaryBtn class="text-lg py-3 px-6 mx-auto block">Next</PrimaryBtn>
    </form>
  </Container>
</template>
