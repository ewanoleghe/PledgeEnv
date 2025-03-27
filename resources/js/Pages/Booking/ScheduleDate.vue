<script setup>
import { ref } from 'vue';  // Import ref from Vue
import { useForm } from "@inertiajs/vue3";
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import Calendar from '../../Components/Calendar.vue';
import Municipality from '../../Components/Municipality.vue';

// Receive the service name, price, and datesWithBookings from Inertia props
const props = defineProps({
  service: { type: String, required: true },
  price: { type: [Number, null], default: null },
  price_visual: { type: [Number, null], default: null },
  price_dust: { type: [Number, null], default: null },
  xrf: { type: [Number, null], default: null },
  xrf_reinspection_price: { type: [Number, null], default: null },
  visual_reinspection_price: { type: [Number, null], default: null },
  dust_wipe_reinspection_price: { type: [Number, null], default: null },
  weekendFee: { type: [Number, null], default: null },
  datesWithBookings: { type: Array, required: true },
  error: { type: Object, default: null }
});

console.log('Received props:', props);  // Debugging line to ensure price is received

// Parent's reactive variable for total cost
const totalCost = ref('');  // This will store the value of totalCost received from the child
const selectedMunicipality = ref(null);  // This will hold the selected municipality
const selectedInspectionType = ref(null);  // This will hold the selected inspection type
const orderNumber = ref('');  // This will store the order number if reinspection

// Handle the update:totalCost event from the child component
const handleTotalCost = (newTotalCost) => {
  totalCost.value = newTotalCost;  // Update the totalCost ref with the emitted value
};

// Create a local ref for the selected date and checkbox
const selectedDate = ref(null);
const includeXrf = ref(false);  // Track the checkbox state

const handleInspectionTypeUpdate = (type) => {
  form.InspectionType = type; // Update the state with the new inspection type
  console.log('Updated Inspection Type in parent:', form.InspectionType); // For debugging
};

// Use Inertia's form handling method
const form = useForm({
  service: props.service,
  price: props.price,
  price_visual: props.price_visual,
  price_dust: props.price_dust,
  xrf: props.xrf,
  xrf_reinspection_price: props.xrf_reinspection_price,
  visual_reinspection_price: props.visual_reinspection_price,
  dust_wipe_reinspection_price: props.dust_wipe_reinspection_price,
  weekendFee: props.weekendFee,
  datesWithBookings: props.datesWithBookings,
  selectedDate: selectedDate.value,
  includeXrf: includeXrf.value,
  InspectionType: '',               // Placeholder
  orderNumber: '',
  dcaMunicode: null,
  municipality: null,
  county: null,
  methodology: null,
  totalCost: null
});

const handleFormErrors = (errors) => {
    console.log('Received form errors from child:', errors);
    // Here you can set the errors in a reactive variable, show toast notifications, etc.
};

// Handle form submission
const submit = () => {
  // Validate selected date and other required fields
  const selectedDateValue = selectedDate.value;
  const selectedInspectionTypeValue = form.InspectionType;
  const selectedMunicipalityValue = selectedMunicipality.value;

  let hasErrors = false; 

  // Check if a date is selected
  if (!selectedDateValue) {
    alert("Please select a date.");
    hasErrors = true;
  }

  // If the service is Lead Inspection, check additional fields
  if (props.service === 'Lead Inspection') {
    if (!selectedInspectionTypeValue) {
      alert("Please select an inspection type.");
      hasErrors = true;
    }

    if (!selectedMunicipalityValue) {
      alert("Please select a municipality.");
      hasErrors = true;
    }
  }

  // Additional validation for reInspection
  if (selectedInspectionTypeValue === 'reInspection' && !orderNumber.value) {
    alert("Provide the Order Number for Reinspection.");
    hasErrors = true;
  }

  // If there are no errors, proceed with form submission
  if (!hasErrors) {
    // Assign form data values dynamically
    form.selectedDate = selectedDateValue;
    form.includeXrf = includeXrf.value;
    form.selectedMunicipality = selectedMunicipalityValue || null; 
    form.InspectionType = selectedInspectionTypeValue;
    form.orderNumber = orderNumber.value; 

    // Update municipality details directly before submission
    form.dcaMunicode = selectedMunicipalityValue ? selectedMunicipalityValue.dcaMunicode : null;
    form.municipality = selectedMunicipalityValue ? selectedMunicipalityValue.municipality : null;
    form.county = selectedMunicipalityValue ? selectedMunicipalityValue.county : null;
    form.methodology = selectedMunicipalityValue ? selectedMunicipalityValue.methodology : null;
    form.totalCost = totalCost.value; 

    // Log the form state before submission
    console.log('Form data before submission:', form);

    // Send the form payload to the backend
    form.post(route('service.time'), { method: 'POST' })
      .then(response => {
        console.log('Form submitted successfully:', response);
      })
      .catch(error => {
        console.error('Form submission failed:', error);
      });
  }
};
</script>

<template>
  <Head title="- Services Request Date" />
  <Container class="w-3/5">
    <div class="mb-8 text-center">
      <Title>{{ props.service }}</Title>
    </div>
    <hr>
    <!-- Display error message for order number -->
    <div v-if="error && error.orderNumber" class="bg-green-100 border border-green-300 text-green-800 text-center font-semibold py-2 px-4 rounded-md shadow-md mb-4">
        {{ error.orderNumber }}
    </div>


    <!-- Municipality Component -->
    <Municipality  
      v-if="props.service === 'Lead Inspection'" 
      :service="props.service"
      :xrf="props.xrf"
      :price="props.price"
      :price_visual="props.price_visual"
      :price_dust="props.price_dust"
      :xrf_reinspection_price="props.xrf_reinspection_price"
      :visual_reinspection_price="props.visual_reinspection_price"
      :dust_wipe_reinspection_price="props.dust_wipe_reinspection_price"
      :includeXrf="includeXrf" 
      :orderNumber="orderNumber"
      @update:totalCost="handleTotalCost"
      @update:selectedMunicipality="selectedMunicipality = $event"  
      @update:inspectionType="handleInspectionTypeUpdate"
      @update:formValid="handleFormValidityCheck"
      @update:orderNumber="orderNumber = $event"
      @update:includeXrf="includeXrf = $event"
      @formSubmissionFailed="handleFormErrors"
    />
 
    
    <!-- Date picker component -->
    <div>
      <p class="mt-2 text-gray-700 font-bold text-xl dark:text-slate-200">Step 1. Select available date for your {{ props.service }} service.</p>
      <Calendar 
        :datesWithBookings="datesWithBookings || []" 
        :similarDates="props.similarDates || []"
        :weekendFee="props.weekendFee"
        @update:selectedDate="selectedDate = $event"  
      />
    </div>

    <!-- Submit button -->
    <div class="p-6">
      <PrimaryBtn @click="submit" class="text-lg py-3 px-6 mx-auto block">Next</PrimaryBtn>
    </div>
    <hr>
  </Container>
</template>
