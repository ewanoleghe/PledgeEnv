<script setup>
import { ref, computed } from 'vue'; // Import `ref` and `computed` from 'vue'
import { useForm } from '@inertiajs/vue3';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import Time from '../../Components/Time.vue';
import TimeReInspection from '../../Components/TimeReInspection.vue';

// Receive props from Inertia
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

// Create a form instance with Inertia.js
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
  optionalMessage: '',
});

const eData = ref({});

// // Handle form update from the child component
// const handleFormUpdate = (updatedFormData) => {
//   Object.assign(form.data, updatedFormData); // Merge the updated form data into the parent form
// };

const handleFormUpdate = (updatedFormData) => {
  // Merge updated form data into the parent form
  Object.assign(eData, updatedFormData); // Update eData with the new form data

  // Pass errors to the child component
  if (updatedFormData.errors) {
    errors.value = updatedFormData.errors;
  }
};


// Function to format the date in "Tue, Oct 10, 2024" format
const formatDate = (date) => {
  const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
  return new Date(date).toLocaleDateString('en-US', options);
};

// Computed property to check if the selected date is a weekend
const isSelectedDateWeekend = computed(() => {
  const selectedDate = new Date(props.selectedDate);
  return selectedDate.getDay() === 6 || selectedDate.getDay() === 0; // Saturday or Sunday
});
</script>

<template>
  <Head title="Services Request Date" />
  <Container class="w-3/5">
    <div class="mb-8 text-center">
      <Title>
        {{ props.InspectionType === 'reInspection' ? 'Lead Re-Inspection' : props.service }}
        <br/>
        <span v-if="props.InspectionType === 'reInspection'">(Order # {{ props.orderNumber }})</span>
      </Title>
    </div>
    <hr>

    <!-- Display selected service -->
    <div class="flex justify-center">
      <table class="table-auto font-xl text-left dark:bg-gray-800 w-3/4 max-w-lg">
        <tbody>
          <tr>
            <td class="font-semibold text-gray-900 dark:text-white">Selected Service:</td>
            <td class="font-semibold text-gray-900 dark:text-white">{{ props.InspectionType === 'reInspection' ? 'Lead Re-Inspection' : props.service }}</td>
          </tr>
          <tr>
            <td class="font-semibold text-gray-900 dark:text-white">County:</td>
            <td class="font-semibold text-gray-900 dark:text-white">{{ props.county }}</td>
          </tr>
          <tr>
            <td class="font-semibold text-gray-900 dark:text-white">Municipality:</td>
            <td class="font-semibold text-gray-900 dark:text-white">{{ props.municipality }}</td>
          </tr>
          <tr>
            <td class="font-semibold text-gray-900 dark:text-white">Method:</td>
            <td class="font-semibold text-gray-900 dark:text-white">
              {{ 
                (props.InspectionType === 'reInspection' && !props.includeXrf) ? 'Dust Wipe Sampling' :
                (props.InspectionType === 'reInspection' && props.methodology === 'Dust Wipe Sampling') ? 'Dust Wipe Sampling' :
                (props.InspectionType === 'reInspection' && props.includeXrf) ? 'Dust Wipe Sampling + XRF' :
                (props.InspectionType === 'newInspection' && props.methodology) ? props.methodology :
                (props.InspectionType === 'newInspection' && props.includeXrf) ? props.methodology + ' + XRF' :
                ''
              }}                
            </td>
          </tr>
          
          <tr>
            <td class="text-xl text-gray-900 dark:text-white"><hr></td>
            <td class="text-xl text-gray-900 dark:text-white"><hr></td>
          </tr>
          <tr v-if="props.InspectionType === 'newInspection'">
            <td class="font-semibold text-gray-900 dark:text-white">New Inspection:</td>
            <td class="font-semibold text-gray-900 dark:text-white">
              ${{ props.methodology === 'Visual Inspection' ? props.price_visual : props.price_dust }} fee per unit
            </td>
          </tr>
          <tr v-if="props.InspectionType === 'reInspection'">
            <td class="font-semibold text-gray-900 dark:text-white">Reinspection:<br>
              <span class="text-xs text-gray-900 dark:text-white">(Order # {{ props.orderNumber }})</span>
            </td>
            <td class="font-semibold text-gray-900 dark:text-white">
              ${{ props.dust_wipe_reinspection_price }} fee per unit
            </td>
          </tr>

          <!-- Conditionally display the XRF service row if 'includeXrf' is true -->
          <tr v-if="props.includeXrf">
            <td class="font-semibold text-gray-900 dark:text-white">Include XRF Service:</td>
            <td class="font-semibold text-gray-900 dark:text-white">
              ${{ 
                props.InspectionType === 'newInspection' 
                  ? props.xrf 
                  : props.xrf_reinspection_price 
              }} fee per unit
            </td>
          </tr>
          <tr v-if="isSelectedDateWeekend">
            <td class="font-semibold text-red-900 dark:text-white">Weekend Charge:</td>
            <td class="font-semibold text-gray-900 dark:text-white">${{ props.weekendFee }} fee per unit</td>
          </tr>
          <tr>
            <td class="font-semibold text-gray-900 dark:text-white"><hr class="mt-2 border-t-1 border-gray-600 dark:border-white"></td>
            <td class="font-semibold text-gray-600 dark:text-white"><hr class="mt-2 border-t-1 border-gray-600 dark:border-white"></td>
          </tr>
          <tr>
            <td class="text-xl text-gray-900 dark:text-white mt-6">Subtotal:</td>
            <td class="text-xl text-gray-900 dark:text-white mt-6">${{ props.subtotal }} fee per unit</td>
          </tr>
          <tr>
            <td class="font-semibold text-gray-900 dark:text-white"><hr class="mt-2 mb-2 border-t-1 border-gray-600 dark:border-white"></td>
            <td class="font-semibold text-gray-600 dark:text-white"><hr class="mt-2 mb-2 border-t-1 border-gray-600 dark:border-white"></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- List of fields with left alignment -->
    <div v-if="props.InspectionType === 'newInspection'">
      <Title class="text-center mt-6 text-gray-900 dark:text-white">Information Needed To Schedule {{ props.service }} Service</Title>
      <ul class="list-inside list-disc text-left mx-auto max-w-xl mt-6 mb-6 text-gray-900 dark:text-white">
        <li>Address of Each Unit</li>
        <li>Unit Identifier (Numbers or Letter)</li>
        <li>Block and Lot Number | 
          <a href="https://njpropertyrecords.com/" target="_blank" class="text-sm text-blue-600 hover:text-red-700 dark:text-blue-400 dark:hover:text-red-500">Find my block and lot number?</a>
        </li>
        <li>Site Contact Name and Phone Number</li>
      </ul>
    </div>
    <hr>

    <!-- Date picker component -->
    <div>
      <p class="mt-4 text-gray-700 font-bold text-xl dark:text-slate-200">
        Step 2. Select available Time slot for your 
        {{ props.InspectionType === 'reInspection' ? 'Lead Re-Inspection' : props.service }} service on {{ formatDate(props.selectedDate) }}.
      </p>
      <p class="mt-5 text-gray-700 font-bold text-xl dark:text-slate-200">Available Slots for {{ formatDate(props.selectedDate) }}.</p>
      <!-- Pass the form object and listen for updates -->
      <Time 
        v-if="InspectionType === 'newInspection'"
        :selectedDate="selectedDate" 
        :service="service" 
        :price="price" 
        :price_visual="price_visual" 
        :price_dust="price_dust" 
        :xrf="xrf" 
        :xrf_reinspection_price="xrf_reinspection_price"
        :visual_reinspection_price="visual_reinspection_price"
        :dust_wipe_reinspection_price="dust_wipe_reinspection_price"
        :weekendFee="weekendFee"
        :includeXrf="includeXrf"
        :InspectionType="InspectionType"
        :orderNumber="orderNumber"
        :dcaMunicode="dcaMunicode"
        :municipality="municipality"
        :county="county"
        :methodology="methodology"
        :totalCost="totalCost"
        :subtotal="subtotal"
        :reinspection_price="reinspection_price"
        :optionalMessage="optionalMessage"
        :errors="errors"
        @update-form="handleFormUpdate"
      />

      <!-- Conditionally render the TimeReInspection component -->
      <TimeReInspection
      v-if="InspectionType === 'reInspection'"
      :service="service"
      :price="price"
      :price_visual="price_visual"
      :price_dust="price_dust"
      :xrf="xrf"
      :xrf_reinspection_price="xrf_reinspection_price"
      :visual_reinspection_price="visual_reinspection_price"
      :dust_wipe_reinspection_price="dust_wipe_reinspection_price"
      :weekendFee="weekendFee"
      :totalWeekendFee="totalWeekendFee"
      :NewSubTotal="NewSubTotal"
      :totalXrf="totalXrf"
      :baseXrf="baseXrf"
      :credSucharg="credSucharg"
      :totalPay="totalPay"
      :basePrice="basePrice"
      :totalBasePrice="totalBasePrice"
      :includeXrf="includeXrf"
      :InspectionType="InspectionType"
      :orderNumber="orderNumber"
      :dcaMunicode="dcaMunicode"
      :municipality="municipality"
      :county="county"
      :methodology="methodology"
      :totalCost="totalCost"
      :subtotal="subtotal"
      :selectedDate="selectedDate"
      :selectedTime="selectedTime"
      :units="units"
      :builtBefore1978="builtBefore1978"
      :useSameContactForAll="useSameContactForAll"
      :siteContact="siteContact"
      :siteContactEmail="siteContactEmail"
      :siteContactPhone="siteContactPhone"
      :optionalMessage="optionalMessage"
      :name="name"
      :email="email"
      :phone="phone"
      :address="address"
      :apt="apt"
      :city="city"
      :state="state"
      :block="block"
      :lot="lot"
      @update="handleUpdate"
    />
    </div>
  </Container>
</template>
