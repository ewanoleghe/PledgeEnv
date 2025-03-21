<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryBtn from "./PrimaryBtn.vue"; // Your button component

// Define props passed from the parent
const props = defineProps({
  service: String,
  price: Number,
  price_visual: Number,
  price_dust: Number, // Optional for reInspection
  xrf: Number, // Optional for newInspection
  xrf_reinspection_price: Number, // Optional for reInspection
  visual_reinspection_price: Number, // Optional for reInspection
  dust_wipe_reinspection_price: Number, // Optional for reInspection
  weekendFee: Number, // Optional for reInspection
  totalWeekendFee: Number, // Optional for reInspection
  NewSubTotal: Number, // Optional for reInspection
  totalXrf: Number, // Optional for reInspection
  baseXrf: Number, // Optional for reInspection
  credSucharg: Number, // Optional for reInspection
  totalPay: Number, // Optional for reInspection
  basePrice: Number, // Optional for reInspection
  totalBasePrice: Number, // Optional for reInspection
  includeXrf: Boolean,
  orderNumber: String,
  InspectionType: {
    type: String,
    validator: value => ['newInspection', 'reInspection'].includes(value)
  },
  dcaMunicode: String, // Optional for both
  municipality: String, // Optional for both
  county: String, // Optional for both
  methodology: String, // Optional for both
  totalCost: Number,
  subtotal: Number, // Common for both
  selectedDate: String, // Common for both
  selectedTime: String, // Optional for both
  name: String, // Optional for both
  email: String, // Optional for both
  phone: String, // Optional for both
  address: String, // Optional for both
  apt: String, // Optional for both
  city: String, // Optional for both
  state: String, // Optional for both
  block: String, // Optional for both
  lot: String, // Optional for both
  units: String, // Optional for both
  builtBefore1978: Boolean, // Optional for both
  useSameContactForAll: Boolean, // Optional for both
  siteContact: String, // Optional for both
  siteContactEmail: String, // Optional for both
  siteContactPhone: String, // Optional for both
  optionalMessage: String, // Optional for both
});

console.log(props.selectedDate);

// Create the form using Inertia's useForm
// Create the form using Inertia's useForm and initialize with props
const form = useForm({
  selectedTime: '',
  name: props.name || '', // Initialize with the prop value
  email: props.email || '',
  phone: props.phone || '',
  address: props.address || '',
  apt: props.apt || '',
  city: props.city || '',
  state: props.state || '',
  block: props.block || '',
  lot: props.lot || '',
  units: props.units || 0, // Initialize with 0 units
  builtBefore1978: props.builtBefore1978 || null,
  useSameContactForAll: '' || true,
  siteContact: '', // Site contact for all units
  siteContactEmail: '',
  siteContactPhone: '',
  optionalMessage: '',
  unitsDetails: [], // Add a field to hold unit-specific contact information


  // Add props here
  service: props.service,
  price: props.price,
  price_visual: 0.00,
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
});

// Watch the number of units and initialize `unitsDetails` accordingly
watch(() => form.units, (newUnits) => {
  form.unitsDetails = Array.from({ length: newUnits }, (_, index) => ({
    unit: '',
    siteContact: '',
    siteContactEmail: '',
    siteContactPhone: '',
  }));
});

// Function to handle radio change for contact handling
const handleRadioChange = (value) => {
  if (value) {
    // If yes, set siteContact, siteContactEmail, siteContactPhone
    form.siteContact = props.siteContact || '';
    form.siteContactEmail = props.siteContactEmail || '';
    form.siteContactPhone = props.siteContactPhone || '';
  } else {
    // If no, initialize unitsDetails
    form.unitsDetails = Array.from({ length: props.units }, (_, index) => ({
      unit: '',
      siteContact: '',
      siteContactEmail: '',
      siteContactPhone: '',
    }));
  }
};

// Watch the useSameContactForAll to auto-fill site contact info
watch(() => form.useSameContactForAll, (newValue) => {
  if (newValue) {
    // Fill all site contact fields with the same values
    for (let unit of form.unitsDetails) {
      unit.siteContact = form.siteContact;
      unit.siteContactEmail = form.siteContactEmail;
      unit.siteContactPhone = form.siteContactPhone;
    }
  }
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

// Computed property to count the number of words in the optional message
const wordCount = computed(() => {
  return form.optionalMessage.trim().split(/\s+/).length;
});

// Function to trim the message if it exceeds 200 words
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

// // Basic validation function
// const validateForm = () => {
//     errors.value = {}; // Reset previous errors

//     // Example of required fields validation
//     if (!form.data.name) errors.value.name = "Name is required";
//     if (!form.data.email) errors.value.email = "Email is required";
//     if (!form.data.phone) errors.value.phone = "Phone number is required";
//     if (!form.data.address) errors.value.address = "Address is required";
//     if (!form.data.siteContact) errors.value.siteContact = "Site contact name is required";
//     if (!form.data.siteContactPhone) errors.value.siteContactPhone = "Site contact phone is required";

//     return Object.keys(errors.value).length === 0; // Return true if no errors
//   };
  
// Handle form submission
const submitForm = () => {
  if (!props.selectedDate) {
    console.error('Selected date is missing.');
    return;
  }
  if (!form.useSameContactForAll) {
    // Check if all unit input fields are filled
    const allUnitsValid = form.unitsDetails.every(unit => 
      unit.unit && unit.siteContact && unit.siteContactEmail && unit.siteContactPhone
    );

    if (!allUnitsValid) {
      console.log('Please fill all required fields for each unit');
      return;
    }
  }
 
};

</script>

<template>

<form @submit.prevent="form.post(route('service.requested'))">
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

        <h3 class="text-lg font-semibold">Re-Inspection Property Form</h3>

        <div class="grid grid-cols-2 gap-4">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium">Name:</label>
            <input 
            readonly
            type="text"
            id="name"
            v-model="form.name"
            :placeholder="props.name"
            class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
            required
        />
          </div>

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium">Email:</label>
            <input 
            readonly
              type="email"
              id="email"
              v-model="form.email"
              :placeholder="props.email"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
              required
            />
          </div>

          <!-- Phone Field -->
          <div>
            <label for="phone" class="block text-sm font-medium">Phone Number:</label>
            <input 
            readonly
              type="text"
              id="phone"
              v-model="form.phone"
              :placeholder="props.phone"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
              required
            />
          </div>

          <!-- Built Before 1978 Button Group -->
          <div>
            <label for="builtBefore1978" class="block text-sm font-medium">Is the property built before 1978?</label>
            <div class="flex space-x-4">
                <button
                type="button"
                class="bg-gray-500 text-white px-4 py-2 rounded-md"
                :disabled="true"  
                >
                {{ props.builtBefore1978 === 'after' ? 'No, built after 1978' : 'Yes, built before 1978' }}
                </button>
            </div>
          </div>
          

          <!-- Address Field -->
          <div>
            <label for="address" class="block text-sm font-medium">Address of the Rental Property:</label>
            <input 
            readonly
              type="text"
              id="address"
              v-model="form.address"
              :placeholder="props.address"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
              required
            />
          </div>

          <!-- Apartment / Floor No. Field -->
          <div>
            <label for="apt" class="block text-sm font-medium">Apt. / Floor No.:</label>
            <input 
            readonly
              type="text"
              id="apt"
              v-model="form.apt"
              :placeholder="props.apt"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
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
            readonly
              type="text"
              id="city"
              v-model="form.city"
              :placeholder="props.city"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
              required
            />
          </div>

          <!-- State Field -->
          <div>
            <label for="state" class="block text-sm font-medium">State:</label>
            <input 
            readonly
              type="text"
              id="state"
              v-model="form.state"
              :placeholder="props.state"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
              required
            />
          </div>

          <!-- Block Field -->
          <div>
            <label for="block" class="block text-sm font-medium">Block:</label>
            <input 
            readonly
              type="text"
              id="block"
              v-model="form.block"
              :placeholder="props.block"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
            />
          </div>

          <!-- Lot Field -->
          <div>
            <label for="lot" class="block text-sm font-medium">Lot:</label>
            <input 
            readonly
              type="text"
              id="lot"
              v-model="form.lot"
              :placeholder="props.lot"
              class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   placeholder:font-bold placeholder:text-lg 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
        </div>
        <hr>
        
        <!-- Units to be Inspected Field -->
        <div>
          <label for="units" class="block text-sm font-medium"># of Failed Units to Be Re-Inspected:</label>
          <input  
            type="number" 
            id="units" 
            v-model="form.units" 
            :placeholder="props.units" 
            class="mt-1 p-2 w-1/6 border border-gray-300 rounded-md
            placeholder:text-gray-900 placeholder:font-bold placeholder:text-lg
            dark:border-gray-500 dark:placeholder:text-gray-300
            dark:bg-gray-800 dark:text-gray-100" 
            required 
            :max="props.units" 
            :min="1"
          /> 
          
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
                  @change="handleRadioChange(true)"
                  class="mr-2"
                />
                Yes
              </label>
              <label>
                <input 
                  type="radio" 
                  v-model="form.useSameContactForAll" 
                  :value="false" 
                  @change="handleRadioChange(false)"
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
                :required="form.useSameContactForAll" 
                placeholder="Enter site contact site Contact" 
                class="mt-1 p-2 w-full border rounded-md 
                      border-gray-300 placeholder:text-gray-900 
                      dark:border-gray-500 dark:placeholder:text-gray-300
                      dark:bg-gray-800 dark:text-gray-100"
              />

              <label for="siteContactEmail" class="block text-sm font-medium">Site Contact Email:</label>
              <input 
                type="email" 
                id="siteContactEmail" 
                v-model="form.siteContactEmail" 
                :required="form.useSameContactForAll" 
                placeholder="Enter site contact Email" 
                class="mt-1 p-2 w-full border rounded-md 
                      border-gray-300 placeholder:text-gray-900
                      dark:border-gray-500 dark:placeholder:text-gray-300
                      dark:bg-gray-800 dark:text-gray-100"
              />

              <label for="siteContactPhone" class="block text-sm font-medium">Site Contact Phone:</label>
              <input 
                type="text" 
                id="siteContactPhone" 
                v-model="form.siteContactPhone" 
                :required="form.useSameContactForAll" 
                placeholder="Enter site contact Phone" 
                class="mt-1 p-2 w-full border rounded-md 
                      border-gray-300 placeholder:text-gray-900 
                      dark:border-gray-500 dark:placeholder:text-gray-300
                      dark:bg-gray-800 dark:text-gray-100"
              />
              </div>
          </div>

                  <!-- If 'Use same contact for all units' is 'No', loop through units and show individual fields -->
                  <div v-else>
              <div v-for="unitIndex in Number(form.units)" :key="unitIndex" class="space-y-4 mb-4">
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
                              class="mt-1 p-2 w-full border rounded-md 
                                    border-gray-300 placeholder:text-gray-900 
                                    dark:border-gray-500 dark:placeholder:text-gray-300
                                    dark:bg-gray-800 dark:text-gray-100" 
                              :required="!form.useSameContactForAll"
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
                              class="mt-1 p-2 w-full border rounded-md 
                                    border-gray-300 placeholder:text-gray-900 
                                    dark:border-gray-500 dark:placeholder:text-gray-300
                                    dark:bg-gray-800 dark:text-gray-100" 
                              :required="!form.useSameContactForAll"
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
                              class="mt-1 p-2 w-full border rounded-md 
                                    border-gray-300 placeholder:text-gray-900 
                                    dark:border-gray-500 dark:placeholder:text-gray-300
                                    dark:bg-gray-800 dark:text-gray-100" 
                              :required="!form.useSameContactForAll"
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
                              class="mt-1 p-2 w-full border rounded-md 
                                    border-gray-300 placeholder:text-gray-900 
                                    dark:border-gray-500 dark:placeholder:text-gray-300
                                    dark:bg-gray-800 dark:text-gray-100" 
                              :required="!form.useSameContactForAll"
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
                class="mt-1 p-2 w-full border rounded-md 
                   border-gray-300 placeholder:text-gray-900 
                   dark:border-gray-500 dark:placeholder:text-gray-300
                   dark:bg-gray-800 dark:text-gray-100"
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
