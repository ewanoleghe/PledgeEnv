<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryBtn from "./PrimaryBtn.vue"; // Your button component

// Define props passed from the parent
const props = defineProps({
  service: String,
  price: Number,
  price_visual: Number,
  price_dust: Number,
  xrf: Boolean,
  xrf_reinspection_price: Number,
  visual_reinspection_price: Number,
  dust_wipe_reinspection_price: Number,
  weekendFee: Boolean,
  selectedDate: String,
  includeXrf: Boolean,
  InspectionType: String,
  orderNumber: String,
  dcaMunicode: String,
  municipality: String,
  county: String,
  methodology: String,
  totalCost: Number,
  subtotal: Number,
  reinspection_price: Number,
});

console.log(props.selectedDate);

// Create the form using Inertia's useForm
const form = useForm({
  selectedTime: '',
  name: '',
  email: '',
  phone: '',
  address: '',
  apt: '',
  city: '',
  state: '',
  block: '',
  lot: '',
  units: 0, // Initialize with 0 units
  builtBefore1978: null,
  useSameContactForAll: false, // New field to track if same contact is used
  siteContact: '',  // Site contact for all units
  siteContactEmail: '',
  siteContactPhone: '',
  optionalMessage: '',
  unitsDetails: [], // Add a field to hold unit-specific contact information

  // Add props here
  service: props.service,
  price: props.price,
  price_visual: props.price_visual,
  price_dust: props.price_dust,
  xrf: props.xrf,
  xrf_reinspection_price: props.xrf_reinspection_price,
  visual_reinspection_price: props.visual_reinspection_price,
  dust_wipe_reinspection_price: props.dust_wipe_reinspection_price,
  weekendFee: props.weekendFee,
  selectedDate: props.selectedDate,
  includeXrf: props.includeXrf,
  InspectionType: props.InspectionType,
  orderNumber: props.orderNumber,
  dcaMunicode: props.dcaMunicode,
  municipality: props.municipality,
  county: props.county,
  methodology: props.methodology,
  totalCost: props.totalCost,
  subtotal: props.subtotal,
  reinspection_price: props.reinspection_price,
  errors: {},
});

// Watch the number of units and initialize `unitsDetails` accordingly
watch(() => form.units, (newUnits) => {
  // Ensure `unitsDetails` has the correct number of unit objects
  form.unitsDetails = Array.from({ length: newUnits }, (_, index) => ({
    unit: '',
    siteContact: '',
    siteContactEmail: '',
    siteContactPhone: '',
  }));
});

// Computed property to display the selected time in a friendly format
const formattedTime = computed(() => {
  return form.selectedTime === 'morning'
    ? 'Morning (8 AM - 12 PM)'
    : form.selectedTime === 'afternoon'
    ? 'Afternoon (12 PM - 5 PM)'
    : '';
});

// Watch to auto-fill site contact info for all units when checkbox is selected
watch(() => form.useSameContactForAll, (newValue) => {
  if (newValue) {
    // Auto-fill all site contact fields with the same values
    form.siteContactEmail = form.siteContactEmail || form.siteContactEmail;
    form.siteContactPhone = form.siteContactPhone || form.siteContactPhone;
  }
});

// Function to handle button click and update 'builtBefore1978'
const setBuiltBefore1978 = (value) => {
  form.builtBefore1978 = value;

  // Show alert if "No, built after 1978" is selected
  if (value === 'after') {
    alert("If your property is built after 1978, you do not need a lead inspection.");
  }
};

// Computed property to count the number of words in the optional message
const wordCount = computed(() => {
  return form.optionalMessage.trim().split(/\s+/).length;
});

// Function to trim the message if it exceeds 20 words
const trimMessage = (event) => {
  const words = form.optionalMessage.trim().split(/\s+/);
  if (words.length > 20) {
    form.optionalMessage = words.slice(0, 20).join(' ');
  }
};

const formatDateForDb = (date) => {
  const formattedDate = new Date(date);
  return formattedDate.toISOString().split('T')[0]; // Formats as 'YYYY-MM-DD'
};

// Error handling
const errors = ref({}); // Stores validation errors from the backend
const message = ref(''); // Stores success message (if any)

// Watch for changes in form errors
watch(() => form.errors, (newErrors) => {
  errors.value = newErrors;
}, { immediate: true });

// Method to submit the form
const submitForm = () => {
  form.post(route('service.requested'), {
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
  <form @submit.prevent="submitForm">
    <div class="space-y-6">
      <!-- Time selection options styled as radio buttons -->
      <div class="flex space-x-6">
      <label
        :class="[
          form.selectedTime === 'morning' ? 'bg-green-500 text-white border-green-700' : 'bg-gray-300 text-gray-700 border-gray-400',
          'flex flex-col items-center cursor-pointer p-4 border rounded-md text-lg font-bold'
        ]"
      >
        <input
          type="radio"
          v-model="form.selectedTime" 
          value="morning"
          class="hidden"
        />
        Morning (8 AM - 12 PM)
      </label>
      <label
        :class="[
          form.selectedTime === 'afternoon' ? 'bg-green-500 text-white border-green-700' : 'bg-gray-300 text-gray-700 border-gray-400',
          'flex flex-col items-center cursor-pointer p-4 border rounded-md text-lg font-bold'
        ]"
      >
        <input
          type="radio"
          v-model="form.selectedTime" 
          value="afternoon"
          class="hidden"
        />
        Afternoon (12 PM - 5 PM)
      </label>
    </div>

      <!-- Show form when a time is selected -->
      <div v-if="formattedTime" class="space-y-4">
        <h3 class="text-lg font-semibold">You selected the {{ formattedTime }}.</h3><hr>

        <h3 class="text-lg font-semibold">Property Form</h3>

        <div class="grid grid-cols-2 gap-4">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium">Name:</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              placeholder="Enter your name"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
              required
            />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
          </div>

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium">Email:</label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              placeholder="Enter your email"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
              required
            />
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
          </div>

          <!-- Phone Field -->
          <div>
            <label for="phone" class="block text-sm font-medium">Phone Number:</label>
            <input
              type="text"
              id="phone"
              v-model="form.phone"
              placeholder="Enter your phone number"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
              required
            />
            <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
          </div>

          <!-- Built Before 1978 Button Group -->
          <div>
            <label for="builtBefore1978" class="block text-sm font-medium">Is the property built before 1978?</label>
            <div class="flex space-x-4">
              <button
                type="button"
                :class="[
                  form.builtBefore1978 === 'after' ? 'bg-blue-500 text-white' : 'bg-blue-200 text-gray-700',
                  'px-4 py-2 rounded-md'
                ]"
                @click="setBuiltBefore1978('after')"
              >
                No, built after 1978
              </button>
              <button
                type="button"
                :class="[
                  form.builtBefore1978 === 'before' ? 'bg-gray-500 text-white' : 'bg-blue-200 text-gray-700',
                  'px-4 py-2 rounded-md'
                ]"
                @click="setBuiltBefore1978('before')"
              >
                Yes, built before 1978
              </button>
            </div>
            <!-- Conditional Prompt when 'No, built after 1978' is selected -->
            <div v-if="form.builtBefore1978 === 'after'" class="text-red-500 mt-2">
              <p>If your property is built after 1978, you do not need a lead inspection.</p>
            </div>
          </div>
          

          <!-- Address Field -->
          <div>
            <label for="address" class="block text-sm font-medium">Address of the Rental Property:</label>
            <input
              type="text"
              id="address"
              v-model="form.address"
              placeholder="Enter the rental property address"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
              required
            />
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
          </div>

          <!-- Apartment / Floor No. Field -->
          <div>
            <label for="apt" class="block text-sm font-medium">Apt. / Floor No.:</label>
            <input
              type="text"
              id="apt"
              v-model="form.apt"
              placeholder="Enter Apt / Floor No."
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
            />
          </div>

          <!-- City Field -->
          <div>
            <!-- Label with two columns (Post or Zip code and City/County) -->
            <div class="flex justify-between items-center">
              <label for="city" class="block text-sm font-medium w-1/2"><span class="font-bold">{{ props.municipality }}</span> Zip code:</label>
              <label for="city" class="block text-sm font-medium w-1/2 text-right">
                <span class="font-bold"> {{ props.county }} County </span>
              </label>
            </div>
            
            <!-- Input field for city/zip code -->
            <input
              type="text"
              id="city"
              v-model="form.city"
              placeholder="Enter Post or Zip code"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
              required
            />

            <!-- Error message if there are errors for the city input -->
            <div v-if="form.errors.city" class="text-red-500 text-sm mt-1">
              {{ form.errors.city }}
            </div>
          </div>

          <!-- State Field -->
          <div>
            <label for="state" class="block text-sm font-medium">State:</label>
            <input
              type="text"
              id="state"
              v-model="form.state"
              placeholder="Enter state"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
              required
            />
            <div v-if="form.errors.state" class="text-red-500 text-sm mt-1">{{ form.errors.state }}</div>
          </div>

          <!-- Block Field -->
          <div>
            <label for="block" class="block text-sm font-medium">Block:</label>
            <input
              type="text"
              id="block"
              v-model="form.block"
              placeholder="Enter block number"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
            />
          </div>

          <!-- Lot Field -->
          <div>
            <label for="lot" class="block text-sm font-medium">Lot:</label>
            <input
              type="text"
              id="lot"
              v-model="form.lot"
              placeholder="Enter lot number"
              class="mt-1 p-2 w-full border border-gray-300 rounded-md"
            />
          </div>
        </div>
        <hr>
        
        <h3 class="text-lg font-semibold">Site Contact & Additional Units</h3>
        <!-- Units to be Inspected Field -->
      <div>
        <label for="units" class="block text-sm font-medium"># of Units to Be Inspected:</label>
        <input 
          type="number" 
          id="units" 
          v-model="form.units" 
          placeholder="Enter the number of units" 
          class="mt-1 p-2 w-1/6 border border-gray-300 rounded-md" 
          required 
          min="1"
          @input="form.units = Math.max(1, form.units)"
        /> 
        <!-- Conditional Bold Text -->
        <p v-if="form.units >= 3" class="font-bold">
            If you have more than 5 units for inspection, please contact us for special pricing options.
        </p>
      </div>

      <!-- New Radio Button for 'Use same contact for all units' -->
      <div class="col-span-2">
        <label class="block text-sm font-medium">Use same contact for all units:</label>
        <div class="flex space-x-4">
          <label>
            <input 
              type="radio" 
              v-model="form.useSameContactForAll" 
              :value="true" 
              class="mr-2"
            />
            Yes
          </label>
          <label>
            <input 
              type="radio" 
              v-model="form.useSameContactForAll" 
              :value="false" 
              class="mr-2"
            />
            No
          </label>
        </div>
      </div>

      <hr>

      <!-- If 'Use same contact for all units' is 'Yes', show the single set of site contact fields -->
      <div v-if="form.useSameContactForAll">
        <div class="space-y-4">
          <label for="siteContact" class="block text-sm font-medium">Site/Tenant's Contact:</label>
          <input 
            type="text" 
            id="siteContact" 
            v-model="form.siteContact" 
            placeholder="Enter site contact name" 
            class="mt-1 p-2 w-full border border-gray-300 rounded-md"
          />

          <label for="siteContactEmail" class="block text-sm font-medium">Site Contact Email:</label>
          <input 
            type="email" 
            id="siteContactEmail" 
            v-model="form.siteContactEmail" 
            placeholder="Enter site contact email" 
            class="mt-1 p-2 w-full border border-gray-300 rounded-md"
          />

          <label for="siteContactPhone" class="block text-sm font-medium">Site Contact Phone:</label>
          <input 
            type="text" 
            id="siteContactPhone" 
            v-model="form.siteContactPhone" 
            placeholder="Enter site contact phone number" 
            class="mt-1 p-2 w-full border border-gray-300 rounded-md"
          />
        </div>
      </div>

      <!-- If 'Use same contact for all units' is 'No', loop through units and show individual fields -->
<div v-else>
  <div v-for="unitIndex in form.units" :key="unitIndex" class="space-y-4 mb-4">
    <p>Inspection - Unit {{ unitIndex }}</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      
      <!-- Unit # Field -->
      <div>
        <label :for="'unit' + unitIndex" class="block text-sm font-medium">Unit #:</label>
        <input 
          type="text" 
          :id="'unit' + unitIndex" 
          v-model="form.unitsDetails[unitIndex - 1].unit"  
          placeholder="Enter unit number" 
          class="mt-1 p-2 w-full border border-gray-300 rounded-md" 
        />
      </div>

      <!-- Site Contact Field -->
      <div>
        <label :for="'siteContact' + unitIndex" class="block text-sm font-medium">Site/Tenant's Contact:</label>
        <input 
          type="text" 
          :id="'siteContact' + unitIndex" 
          v-model="form.unitsDetails[unitIndex - 1].siteContact"  
          placeholder="Enter site contact name" 
          class="mt-1 p-2 w-full border border-gray-300 rounded-md" 
        />
      </div>

      <!-- Site Contact Email Field -->
      <div>
        <label :for="'siteContactEmail' + unitIndex" class="block text-sm font-medium">Site Contact Email:</label>
        <input 
          type="email" 
          :id="'siteContactEmail' + unitIndex" 
          v-model="form.unitsDetails[unitIndex - 1].siteContactEmail"  
          placeholder="Enter site contact email" 
          class="mt-1 p-2 w-full border border-gray-300 rounded-md" 
        />
      </div>

      <!-- Site Contact Phone Field -->
      <div>
        <label :for="'siteContactPhone' + unitIndex" class="block text-sm font-medium">Site Contact Phone:</label>
        <input 
          type="text" 
          :id="'siteContactPhone' + unitIndex" 
          v-model="form.unitsDetails[unitIndex - 1].siteContactPhone" 
          placeholder="Enter site contact phone number" 
          class="mt-1 p-2 w-full border border-gray-300 rounded-md" 
        />
      </div>
    </div>
    <hr>
          <hr>
        </div>
      </div>

        <div>
            <label for="optionalMessage" class="block text-sm font-medium">Optional Message:</label>
            <textarea 
                id="optionalMessage" 
                v-model="form.optionalMessage" 
                placeholder="Enter any additional message or notes" 
                class="mt-1 p-2 w-full border border-gray-300 rounded-md"
            ></textarea>
        </div>
        <!-- Show word count and warning message -->
        <div class="text-sm mt-1">
        <span :class="wordCount > 20 ? 'text-red-500' : 'text-gray-500'">
            {{ wordCount }} / 20 words
        </span>
        <p v-if="wordCount > 20" class="text-red-500 text-xs mt-1">
            You have exceeded the maximum word limit. Please limit your message to 20 words.
        </p>
        </div>

        <div class="p-6 flex justify-between">
          <button type="button" @click="refreshPage" class="text-lg py-3 px-6 mx-auto block bg-red-500 text-white hover:bg-red-700 rounded">
            Cancel
          </button>
          <PrimaryBtn @click="submitForm" class="text-lg py-3 px-6 mx-auto block">Next</PrimaryBtn>
        </div>
      </div>
    </div>
  </form>
</template>
