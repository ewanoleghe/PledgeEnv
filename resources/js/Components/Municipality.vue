<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';  
import { useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

// Emit selectedMunicipality and form validation state
const emit = defineEmits([
  'update:selectedMunicipality', 
  'update:totalCost', 
  'update:formValid', 
  'update:inspectionType',
  'update:orderNumber', // Add emissions needed for order number 
  'update:includeXrf'   // Emit includeXrf when its value changes
]);

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

// Computed property to check if the InspectionType is 'reInspection'
const isOrderNumberRequired = computed(() => form.InspectionType === 'reInspection');

// Declare selectedMunicipality to store the selected municipality data
const selectedMunicipality = ref(null);

// Declare includeXrf ref to store XRF checkbox state
const includeXrf = ref(false);

// Initialize form with includeXrf
const form = useForm({
  InspectionType: '',
  orderNumber: '',
  selectedMunicipality: null,
  includeXrf: false,
  dcaMunicode: '',
  municipality: '',
  county: '',
  methodology: ''
});

// Validate form
const isFormValid = computed(() => {
  return form.InspectionType !== '' && selectedMunicipality.value !== null && 
         (form.InspectionType !== 'reInspection' || form.orderNumber !== '');
});

let municipalities = ref([]);

// Load municipalities function
const loadMunicipalities = async () => {
  try {
    const response = await import('./municipalities.json');
    municipalities.value = response.default || response;
  } catch (error) {
    console.error('Error loading municipalities:', error);
  }
};

// Handle search query and filtering
const searchQuery = ref('');
const dropdownVisible = ref(false);

const debouncedSearch = debounce((query) => {
  searchQuery.value = query;
}, 300);

const filteredMunicipalities = computed(() => {
  const search = searchQuery.value.toLowerCase();
  return municipalities.value.filter(municipality => {
    return (
      municipality.municipality.toLowerCase().includes(search) || 
      municipality.county.toLowerCase().includes(search)
    );
  });
});

watch(searchQuery, (newQuery) => {
  dropdownVisible.value = newQuery.length > 0;
  debouncedSearch(newQuery);
});

watch(() => form.orderNumber, (newValue) => {
  console.log("Order Number changed:", newValue);
  emit('update:orderNumber', newValue); // Emit the new value to the parent
});

watch(() => form.includeXrf, (newValue) => {
  emit('update:includeXrf', newValue);  // Make sure to emit the new value if needed
});

const showDropdown = () => {
  dropdownVisible.value = filteredMunicipalities.value.length > 0;
};

// Handle municipality selection
const selectMunicipality = (municipality) => {
  form.selectedMunicipality = municipality;
  selectedMunicipality.value = municipality;
  searchQuery.value = municipality.municipality;
  dropdownVisible.value = false;

  // Update the form with selected municipality data
  form.dcaMunicode = municipality.dcaMunicode;
  form.municipality = municipality.municipality;
  form.county = municipality.county;
  form.methodology = municipality.methodology;

  emit('update:selectedMunicipality', municipality); // Emit the municipality data to the parent
};

// Hide dropdown when clicking outside
const hideDropdown = (event) => {
  const dropdownElement = document.getElementById('municipality-dropdown');
  const inputElement = document.getElementById('municipality-input');
  
  if (
    dropdownElement && !dropdownElement.contains(event.target) &&
    inputElement && !inputElement.contains(event.target)
  ) {
    dropdownVisible.value = false;
  }
};

onMounted(() => {
  loadMunicipalities();  // Load municipalities on mount
  document.addEventListener('click', hideDropdown);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', hideDropdown);
});

// Set InspectionType in form
const setInspectionType = (type) => {
    form.InspectionType = type; // Update the local field
    emit('update:inspectionType', form.InspectionType); // Emit the updated inspection type
    emit('update:formValid', isFormValid.value); // Update form validity if necessary

    if (type === 'reInspection') {
        // Emit the order number whenever it's a reInspection,
        // ensuring order number input is handled
        emit('update:orderNumber', form.orderNumber);
    }
};

// Clear municipality selection
const clearSelection = () => {
  selectedMunicipality.value = null;
  form.selectedMunicipality = null;
  searchQuery.value = '';
  dropdownVisible.value = false;
};

// Computed property for total cost
const totalCost = computed(() => {
  if (!selectedMunicipality.value) {
    return 'Please select a municipality to see cost.';
  }

  const municipality = selectedMunicipality.value;
  const methodology = municipality.methodology;

  const xrfPrice = parseFloat(props.xrf) || 0;
  const priceVisual = parseFloat(props.price_visual) || 0;
  const priceDust = parseFloat(props.price_dust) || 0;
  const xrfReinspectionPrice = parseFloat(props.xrf_reinspection_price) || 0;
  const visualReinspectionPrice = parseFloat(props.visual_reinspection_price) || 0;
  const dustWipeReinspectionPrice = parseFloat(props.dust_wipe_reinspection_price) || 0;

  let basePrice = null;

  // Ensure that methodology for 'reInspection' is always set to 'Dust Wipe Sampling'
  if (form.InspectionType === 'reInspection') {
    form.methodology = 'Dust Wipe Sampling';  // Set methodology to 'Dust Wipe Sampling' for reInspection

    // Set the basePrice for reInspection based on methodology
    basePrice = dustWipeReinspectionPrice;  // reInspection can only use 'Dust Wipe Sampling'
  } else if (form.InspectionType === 'newInspection') {
    // For 'newInspection', use either visual or dust price based on methodology
    basePrice = form.methodology === 'Visual Inspection' ? priceVisual : priceDust;
  }

  // Check for valid base price
  if (basePrice === null || isNaN(basePrice)) {
    return 'Price Not Available';
  }

  // Calculate the total
  let total = basePrice;
  if (form.includeXrf) {  // Ensure to use the form's includeXrf state
    total += form.InspectionType === 'newInspection' ? xrfPrice : xrfReinspectionPrice;
  }

  const formattedTotal = total.toFixed(2); // Ensure formatted total is defined here
  emit('update:totalCost', formattedTotal); // Emit the formatted total
  return formattedTotal; // Return the formatted total
});

// Submit form data (including selectedMunicipality values)
const submitForm = () => {
    // Prepare the submission data
    const submissionData = {
        ...form,
        ...(selectedMunicipality.value ? {
            dcaMunicode: selectedMunicipality.value.dcaMunicode,
            municipality: selectedMunicipality.value.municipality,
            county: selectedMunicipality.value.county,
            methodology: selectedMunicipality.value.methodology,
        } : {}),
        includeXrf: includeXrf.value, // Include includeXrf in the submission
        orderNumber: form.orderNumber,
    };

    // Submit the form using Inertia
    form.post('/submit-endpoint', {
        data: submissionData,
        onSuccess: () => {
            console.log('Form submitted successfully');
            form.errors = {}; // Clear errors on successful submission
        },
        onError: (errors) => {
            console.log('Form submission failed', errors);
            form.errors = errors; // Populate errors
        },
    });
};
</script>

<template>
  <Head title="- Services Request Date" />
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5">
     <!-- Check if inspectorsCode is empty -->
    <div v-if="!inspectorsCode">
      <label for="municipality" class="block text-x font-bold dark:text-white">Select Municipality:</label>
      <div class="relative">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search Municipality or County..."
          class="mt-1 p-2 w-full border border-gray-300 rounded-md dark:bg-gray-700 dark:text-white"
          @focus="showDropdown"
          id="municipality-input"
          required
        />
        <p v-if="!selectedMunicipality" class="text-red-600 text-sm">Please select a municipality.</p>
        <!-- Clear button 'x' -->
        <button
          v-if="selectedMunicipality"
          type="button"
          @click="clearSelection"
          class="absolute right-2 top-2 bg-gray-300 text-gray-700 rounded-full p-1 hover:bg-gray-400"
          aria-label="Clear selection"
        >
          &times; <!-- This is the 'x' character -->
        </button>
        <ul
          v-if="dropdownVisible && filteredMunicipalities.length > 0"
          id="municipality-dropdown"
          class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md max-h-60 overflow-y-auto dark:bg-gray-700"
        >
          <li
            v-for="(municipality, index) in filteredMunicipalities"
            :key="index"
            class="px-4 py-2 hover:bg-gray-200 cursor-pointer dark:text-white dark:hover:bg-gray-600"
            @click="selectMunicipality(municipality)"
          >
            {{ municipality.municipality }} - {{ municipality.county }} ({{ municipality.dcaMunicode }})
          </li>
        </ul>
        <p v-if="dropdownVisible && filteredMunicipalities.length === 0" class="mt-2 text-gray-500 dark:text-white">No results found</p>
      </div>
    </div>

    <!-- Check if inspectorsCode is empty -->
    <div v-if="!inspectorsCode">
      <label for="InspectionType" class="block text-x font-bold dark:text-white">Has your property undergone an inspection before?</label>
      <div class="flex space-x-4">
        <button
          type="button"
          :class="[form.InspectionType === 'newInspection' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-white', 'px-4 py-2 rounded-md']"
          @click="setInspectionType('newInspection')"
        >
          No, First Lead Inspection
        </button>
        <button
          type="button"
          :class="[form.InspectionType === 'reInspection' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-white', 'px-4 py-2 rounded-md']"
          @click="setInspectionType('reInspection')"
        >
          Yes, Reinspection
        </button>
      </div>
      <div v-if="form.InspectionType === 'reInspection'">
        <label for="orderNumber" class="block text-sm font-medium dark:text-white">Order Number for Reinspection:</label>
        <input
          type="text"
          id="orderNumber"
          v-model="form.orderNumber"
          placeholder="Enter Initial Inspection order number"
          class="mt-1 p-2 w-3/4 border border-gray-300 rounded-md dark:bg-gray-700 dark:text-white"
          :required="form.InspectionType === 'reInspection'"
          :maxlength="7"
          pattern="[0-9]*"
        />
        <p v-if="form.errors.orderNumber" class="text-red-600 text-sm">{{ form.errors.orderNumber[0] }}</p>
        <p v-if="form.InspectionType === 'reInspection' && form.orderNumber === ''" class="text-red-600 text-sm">Initial Inspection order number Required</p>
        <p v-if="form.InspectionType === 'reInspection'" class="text-red-600 text-sm mt-5 font-bold">* For Re-Inspection (Failed house or apartment) only the area that failed will be re-tested for lead-based paint hazard.
          </p>
        
      </div>
      <p v-if="form.InspectionType === ''" class="text-red-600 text-sm">Please select an inspection type.</p>
    </div>
  </div>

  <!-- Check if inspectorsCode is empty -->
  <div class="bg-gray-200 p-6 rounded-lg mb-4 text-center dark:bg-gray-800">
      <p class="font-bold text-red-600 dark:text-red">Optional<br>
        <span class="ml-2 text-sm font-bold dark:text-white">(This does not guarantee Lead Free Designation)</span>
      </p>
      <label class="inline-flex items-center text-gray-900 dark:text-white">
        <input
          type="checkbox"
          class="form-checkbox text-blue-500 dark:text-blue-300"
          v-model="form.includeXrf"
        />
        <span class="ml-2 dark:text-white">Include XRF Service for Lead Free Certification, an additional ${{ props.xrf }} fee per unit/${{ props.xrf_reinspection_price }} for re-Inspection. </span>
      </label><br>
      <span class="ml-2 text-sm font-bold dark:text-white"> *Provided all Interior components with lead-base paints have been replaced. </span>
  </div>

  <!-- Display selected service1 -->
  <div v-if="inspectorsCode" class="text-center">
  <p class="text-lg font-bold dark:text-white">Selected Municipality Information</p>
  <p class="dark:text-white">Municipality: {{ selectedMunicipality?.municipality }}</p>
  <p class="dark:text-white">County: {{ selectedMunicipality?.county }}</p>
  <p class="dark:text-white">DCA Municode: {{ selectedMunicipality?.dcaMunicode }}</p>
  <!-- Assuming 'inspectionType' is part of selectedMunicipality -->
 
  <!-- Display a message if it's set -->
  <p v-if="message" class="dark:text-gray-400">Message: {{ message }}</p>
  
  <!-- Add any additional displayed data or actions related to inspectorsCode -->
</div>

  <hr />
  <!-- Display selected service2 -->
  <div class="text-center">
      <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white">
          Selected Service: {{ props.service }}<br>
          Inspection Type: 
          <span :class="{
            'text-gray-900 dark:text-white': selectedMunicipality || form.InspectionType === 'reInspection',
            'text-red-600 font-medium text-sm dark:text-red-400': !selectedMunicipality && form.InspectionType !== 'reInspection'
          }">
            <!-- Conditional display logic for InspectionType -->
            {{
              form.InspectionType === 'newInspection'
                ? (selectedMunicipality ? selectedMunicipality.methodology + (form.includeXrf ? ' + XRF' : '') : '-- -- --')
                : form.InspectionType === 'reInspection'
                ? 'Dust Wipe Sampling' + (form.includeXrf ? ' + XRF' : '')
                : '-- -- --'
            }}
          </span><br>
          <!-- Show XRF price only if includeXrf is true -->
          <span v-if="form.includeXrf" class="ml-2 text-sm font-bold dark:text-white">
            XRF Price: ${{ props.xrf }} 
            *This amount is added for XRF inspection.
          </span><br v-if="form.includeXrf">
          <!-- Displaying the computed total cost -->
          Cost: 
          <span class="text-gray-900 dark:text-white">
            {{ totalCost.includes('Price Not Available') || totalCost.includes('Please select a municipality to see cost.') ? totalCost : `$${totalCost}` }} per unit
          </span><br>
      </h3>
  </div>
  <hr />
</template>
