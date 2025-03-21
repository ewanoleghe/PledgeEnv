<script setup>
import { ref, watch, computed } from 'vue';  // Add this import
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import { useForm } from '@inertiajs/vue3';
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Initialize the form data
const form = useForm({
  service_name: '', // Initialize the service_name with an empty string
  price: '',
  price_visual: '',
  price_dust: '',
  xrf: '',
  xrf_reinspection_price: '',
  visual_reinspection_price: '',
  dust_wipe_reinspection_price: '',
  weekendFee: '',
  bookMax: '',
  description: '', // Include description in the form
});

// List of services with their descriptions
const services = [
  { service_name: 'Lead Inspection', description: 'Ensure your home is free of lead hazards.' },
  { service_name: 'Asbestos Management', description: 'Safe management and removal of asbestos.' },
  { service_name: 'Mold Assessment', description: 'Assessing and identifying mold problems.' },
  { service_name: 'Radon Control', description: 'Control and mitigate radon gas in your home.' },
  { service_name: 'Sewer Inspection & Cleaning', description: 'Inspection and cleaning of sewer systems.' },
  { service_name: 'Residential Tank Sweep', description: 'Inspection of underground tanks on residential properties.' },
  { service_name: 'Remediation & Consulting', description: 'Consulting and remediation services for environmental hazards.' },
  { service_name: 'Lead in Drinking Water', description: 'We provide comprehensive testing services for copper and lead in drinking water.' },
];

// Automatically update the description based on the selected service
const selectedServiceDescription = computed(() => {
  const selectedService = services.find(service => service.service_name === form.service_name);
  return selectedService ? selectedService.description : '';
});

// Watch for changes in selected service and update form.description
watch(() => form.service_name, (newValue) => {
  const selectedService = services.find(service => service.service_name === newValue);
  form.description = selectedService ? selectedService.description : '';
});

// Error handling
const errors = ref({}); // Stores validation errors from the backend
const message = ref(''); // Stores success message (if any)

// Watch for changes in form errors
watch(() => form.errors, (newErrors) => {
  errors.value = newErrors;
}, { immediate: true });

// Method to submit the form
const submitForm = () => {
  form.post(route('admin.service.store'), {
    preserveScroll: true,
    onSuccess: () => {
      message.value = 'Service created successfully';
    },
    onError: (errors) => {
      errors.value = errors;
    }
  });
};
</script>

<template>
    <Container>
      <Title>Create New Service</Title>
  
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Column 1: 70% -->
        <div class="md:w-full">
          <h2 class="mt-6 text-lg font-semibold">Service Creation</h2>
  
          <!-- Success Message -->
          <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
          </div>
  
          <!-- Error Messages -->
          <div v-if="errors && Object.keys(errors).length" class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>
  
          <!-- Button Container -->
          <div class="flex justify-between mt-2 mb-4">
            <!-- Back Button -->
            <Link :href="route('admin.service.index')" 
              as="button"
              type="button"
              class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-600">
              Back
            </Link>
          </div>
  
          <!-- Form -->
          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Display All service -->
            <table class="min-w-full mt-4 border border-gray-300">
              <thead class="text-sm">
                <tr class="bg-gray-200">
                  <th colspan="2" class="px-4 py-2 border text-xl font-bold text-center">Service Info</th>
                </tr>
              </thead>
              <tbody class="text-sm">
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Service Type</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    <!-- Service Type Dropdown -->
                    <select 
                      v-model="form.service_name"
                      class="mt-1 p-2 pl-10 block w-full rounded-md font-bold text-sm dark:text-slate-900 border-slate-300 outline-0 focus:ring-1 focus:ring-inset focus:ring-indigo-400 focus:border-indigo-400 placeholder:text-slate-400"
                    >
                      <option value="" disabled>Select Service Type</option>
                      <option v-for="service in services" :key="service.service_name" :value="service.service_name">
                        {{ service.service_name }}
                      </option>
                    </select>
                    <!-- Validation Error for Service Type -->
                    <div v-if="errors['service_name']" class="text-red-600 text-xs mt-1">
                      {{ errors['service_name'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Price (NY)</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="Price" 
                      v-model="form.price" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Price -->
                    <div v-if="errors['price']" class="text-red-600 text-xs mt-1">
                      {{ errors['price'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Visual Inspection</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="Visual Inspection Price" 
                      v-model="form.price_visual" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Visual Inspection -->
                    <div v-if="errors['price_visual']" class="text-red-600 text-xs mt-1">
                      {{ errors['price_visual'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Dust Wipe Inspection</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="Dust Wipe Price" 
                      v-model="form.price_dust" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Dust Wipe Inspection -->
                    <div v-if="errors['price_dust']" class="text-red-600 text-xs mt-1">
                      {{ errors['price_dust'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">XRF</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="XRF Price" 
                      v-model="form.xrf" 
                      class="w-full" 
                    />
                    <!-- Validation Error for XRF -->
                    <div v-if="errors['xrf']" class="text-red-600 text-xs mt-1">
                      {{ errors['xrf'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">XRF Re-Inspection</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="XRF Re-Inspection Price" 
                      v-model="form.xrf_reinspection_price" 
                      class="w-full" 
                    />
                    <!-- Validation Error for XRF Re-Inspection -->
                    <div v-if="errors['xrf_reinspection_price']" class="text-red-600 text-xs mt-1">
                      {{ errors['xrf_reinspection_price'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Visual Re-Inspection</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="Visual Re-Inspection Price" 
                      v-model="form.visual_reinspection_price" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Visual Re-Inspection -->
                    <div v-if="errors['visual_reinspection_price']" class="text-red-600 text-xs mt-1">
                      {{ errors['visual_reinspection_price'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Dust Wipe Re-Inspection</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="Dust Wipe Re-Inspection Price" 
                      v-model="form.dust_wipe_reinspection_price" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Dust Wipe Re-Inspection -->
                    <div v-if="errors['dust_wipe_reinspection_price']" class="text-red-600 text-xs mt-1">
                      {{ errors['dust_wipe_reinspection_price'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Weekend Charge</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    $<InputField 
                      label="Weekend Charge" 
                      v-model="form.weekendFee" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Weekend Charge -->
                    <div v-if="errors['weekendFee']" class="text-red-600 text-xs mt-1">
                      {{ errors['weekendFee'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Maximum Daily Booking</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    <InputField 
                      label="Maximum Daily Booking" 
                      v-model="form.bookMax" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Maximum Daily Booking -->
                    <div v-if="errors['bookMax']" class="text-red-600 text-xs mt-1">
                      {{ errors['bookMax'] }}
                    </div>
                  </td>
                </tr>
                <!-- Dynamically Display the Description -->
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Service Description</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    <div v-if="selectedServiceDescription" class="text-sm text-gray-700">
                      {{ selectedServiceDescription }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
  
            <!-- Submit Button -->
            <div class="flex justify-end mt-6">
              <PrimaryBtn type="submit">Add New Service</PrimaryBtn>
            </div>
          </form>
        </div>
      </div>
    </Container>
  </template>
