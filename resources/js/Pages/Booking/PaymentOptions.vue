<script setup>
import { ref, computed, watch } from 'vue';  // Import `ref` and `computed` from 'vue'
import { useForm } from '@inertiajs/vue3';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";

// Access the APP_NAME environment variable directly
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name'; // Default if not set

// Receive the 'paySelectData' prop from Inertia
const props = defineProps({
   paySelectData: Object,
   error: String,
   success: Boolean
});

console.log(props.message);

// Initialize the payment selection tracking
const selectedPaymentMethod = ref(''); // Track selected payment method

// Initialize the form using the received data
const form = useForm({
  discountCode: props.paySelectData.discountCode,  // Add a discountCode field to track the value
  inspector_name: props.paySelectData.inspector_name,  // Add a Inspectors Name
  discountTotal: props.paySelectData.discountTotal,
  discountPercentage: props.paySelectData.discountPercentage,
  selectedDate: props.paySelectData.selectedDate,
  service: props.paySelectData.service,
  price: parseFloat(props.paySelectData.price?.replace(/,/g, '') || 0).toFixed(2),
  price_visual: parseFloat((props.paySelectData.price_visual || 0).toString().replace(/,/g, '')).toFixed(2),
  price_dust: parseFloat(props.paySelectData.price_dust?.replace(/,/g, '') || 0).toFixed(2),
  xrf: parseFloat(props.paySelectData.xrf?.replace(/,/g, '') || 0).toFixed(2),
  xrf_reinspection_price: parseFloat(props.paySelectData.xrf_reinspection_price?.replace(/,/g, '') || 0).toFixed(2),
  visual_reinspection_price: parseFloat(props.paySelectData.visual_reinspection_price?.replace(/,/g, '') || 0).toFixed(2),
  dust_wipe_reinspection_price: parseFloat(props.paySelectData.dust_wipe_reinspection_price?.replace(/,/g, '') || 0).toFixed(2),
  
  weekendFee: parseFloat(props.paySelectData.weekendFee || 0), // This does not need to be formatted to 2 decimal places
  totalWeekendFee: parseFloat(props.paySelectData.totalWeekendFee || 0), // This does not need to be formatted to 2 decimal places
  NewSubTotal: parseFloat(props.paySelectData.NewSubTotal || 0).toFixed(2), // Ensure NewSubTotal is parsed and formatted
  totalXrf: parseFloat(props.paySelectData.totalXrf || 0).toFixed(2), // Ensure totalXrf is parsed and formatted
  baseXrf: parseFloat(props.paySelectData.baseXrf || 0).toFixed(2), // Ensure baseXrf is parsed and formatted
  credSucharg: parseFloat(props.paySelectData.credSucharg || 0).toFixed(2), // Ensure credSucharg is parsed and formatted
  totalPay: parseFloat(props.paySelectData.totalPay || 0).toFixed(2), // Ensure totalPay is parsed and formatted
  basePrice: parseFloat(props.paySelectData.basePrice || 0).toFixed(2), // Format basePrice to 2 decimal places
  totalBasePrice: parseFloat(props.paySelectData.totalBasePrice || 0).toFixed(2), // Format totalBasePrice to 2 decimal places

  includeXrf: props.paySelectData.includeXrf,
  InspectionType: props.paySelectData.InspectionType,
  orderNumber: props.paySelectData.orderNumber,
  dcaMunicode: props.paySelectData.dcaMunicode,
  municipality: props.paySelectData.municipality,
  county: props.paySelectData.county,
  methodology: props.paySelectData.methodology,
  totalCost: parseFloat(props.paySelectData.totalCost || 0).toFixed(2),
  subtotal: parseFloat(props.paySelectData.subtotal?.replace(/,/g, '') || 0).toFixed(2),
  selectedTime: props.paySelectData.selectedTime,
  name: props.paySelectData.name,
  email: props.paySelectData.email,
  phone: props.paySelectData.phone,
  address: props.paySelectData.address,
  apt: props.paySelectData.apt,
  city: props.paySelectData.city,
  state: props.paySelectData.state,
  block: props.paySelectData.block,
  lot: props.paySelectData.lot,
  units: props.paySelectData.units,
  builtBefore1978: props.paySelectData.builtBefore1978,
  useSameContactForAll: props.paySelectData.useSameContactForAll,
  siteContact: props.paySelectData.siteContact,
  siteContactEmail: props.paySelectData.siteContactEmail,
  siteContactPhone: props.paySelectData.siteContactPhone,
  optionalMessage: props.paySelectData.optionalMessage,
  unitsDetails: props.paySelectData.unitsDetails.map(unit => ({
    ...unit,
    unitNumber: unit.unitNumber || '',
    siteContact: unit.siteContact || '',
    siteContactEmail: unit.siteContactEmail || '',
    siteContactPhone: unit.siteContactPhone || '',
  })),
  agreedToTerms: props.paySelectData.agreedToTerms, // Checkbox for Terms of Service
  selectedPaymentMethod: selectedPaymentMethod.value, // Track the selected payment method
});

// Function to format the date in "Tue, Oct 10, 2024" format
const formatDate = (date) => {
  const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
  return new Date(date).toLocaleDateString('en-US', options);
};

// Watch for changes to selectedPaymentMethod to update the form
watch(selectedPaymentMethod, (newValue) => {
  form.selectedPaymentMethod = newValue; // Update the form whenever a new payment method is selected
});

const selectPaymentMethod = (method) => {
  selectedPaymentMethod.value = method; // Set the selected payment method
};

const isPaymentSelected = computed(() => {
  return selectedPaymentMethod.value !== '';
});

const submitForm = () => {
  if (!isPaymentSelected.value) {
    alert('Please select a payment method before proceeding.');
    return;
  }

  console.log('Submitting form with data:', form);

  form.post(route('service.booking'), {
    method: "POST",
    onFinish: () => {
      console.log('Form submitted successfully');
    },
    onError: (errors) => {
      console.log('Form submission failed. Errors:', errors);
    }
  });
};

// Reload the page
const refreshPage = () => {
  window.location.reload();  
};

// Define a method to get the first ten words
const getFirstTenWords = (message) => {
  if (!message) return ''; 
  return message.split(' ').slice(0, 10).join(' ') + (message.split(' ').length > 10 ? '...' : '');
};
</script>

<template>
    <Head title="- Select a payment method" />
    <Container class="w-3/5">
      <div class="mb-8 text-center">
        <Title>{{ props.paySelectData.InspectionType === 'reInspection' ? 'Lead Paint Re-Inspection' : props.paySelectData.service }}
          <br/>
          <span v-if="props.paySelectData.InspectionType === 'reInspection'">(Order # {{ props.paySelectData.orderNumber }})</span>
        </Title>
      </div>
      <hr>

      <!-- Display the message if it's provided -->
      <div v-if="props.error" class="mb-4 text-green-600">
  {{ props.error }}
</div>
  
      <!-- Display selected service -->
      <div class="flex justify-center">
        <table class="table-auto font-xl text-left dark:bg-gray-800 w-3/4 max-w-lg">
          <tbody>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Selected Service:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">{{ props.paySelectData.service }}</td>
            </tr>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Service Type:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">
                {{ props.paySelectData.InspectionType === 'newInspection' ? 'New Inspection' : 'Re-Inspection' }} <br>
                <span v-if="props.paySelectData.orderNumber" class="font-bold text-xs">(order # {{ props.paySelectData.orderNumber }})</span>
              </td>
            </tr>

            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">County:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">{{ props.paySelectData.county }} County</td>
            </tr>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Municipality:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">{{ props.paySelectData.municipality }}</td>
            </tr>
            <tr v-if="props.paySelectData.includeXrf === true">
              <td class="font-semibold text-gray-900 dark:text-white">Method:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">
                {{ props.paySelectData.methodology }} + XRF included
              </td>
            </tr>
            <tr v-else>
              <td class="font-semibold text-gray-900 dark:text-white">Method:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">
                {{ 
                  (props.paySelectData.InspectionType === 'reInspection') ? 
                    (props.paySelectData.includeXrf ? 'Dust Wipe Sampling + XRF' : 'Dust Wipe Sampling') :
                  (props.paySelectData.InspectionType === 'newInspection') ? 
                    (props.paySelectData.includeXrf ? `${props.paySelectData.methodology} + XRF` : props.paySelectData.methodology) :
                  ''
                }}
              </td>
            </tr>
            <!-- Added horizontal line before Total Amount -->
            <tr>
              <td colspan="3">
                <hr class="my-4 " />
              </td>
            </tr>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Date:</td>
              <td class="font-semibold text-gray-900 dark:text-white">{{ formatDate(props.paySelectData.selectedDate) }} </td>
            </tr>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Selected Time:</td>
              <td colspan="2" class="font-semibold text-gray-900 dark:text-white">
                {{ props.paySelectData.selectedTime === 'morning' ? 'Morning 8.00 am - 12.00 pm' : 'Afternoon 12.00 pm - 5.00 pm' }} </td>
            </tr>

            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">No of Units:</td>
              <td class="font-semibold text-gray-900 dark:text-white">{{ props.paySelectData.units }} Unit{{ props.paySelectData.units > 1 ? 's' : '' }}</td>
            </tr>

            <!-- Added horizontal line before Total Amount -->
            <tr>
              <td colspan="3">
                <hr class="my-4 " />
              </td>
            </tr>
  
            <tr>
              <td colspan="3">
                <hr class="my-4 mb-2 border-t-2 border-gray-900 dark:border-gray-200" />
              </td>
            </tr>

            <!-- Display Weekend Fee if applicable -->
            <tr v-if="props.paySelectData.totalWeekendFee > 0">
              <td class="font-semibold text-gray-900 dark:text-white">Weekend Fee:</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ props.paySelectData.weekendFee }} per unit</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.paySelectData.totalWeekendFee).toFixed(2) }}</td>
            </tr>
            
            <!-- Cost per unit -->
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Cost:
                <span v-if="props.paySelectData.discountCode" class="text-red-600"> 
                  <sup class="text-xs">({{ props.paySelectData.discountPercentage }}% off)</sup> 
                </span>
              </td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.paySelectData.basePrice).toFixed(2) }} per unit</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.paySelectData.totalBasePrice).toFixed(2) }}</td>
            </tr>

            <!-- Conditionally display the XRF service row if 'includeXrf' is true -->
            <tr v-if="props.paySelectData.includeXrf">
              <td class="font-semibold text-gray-900 dark:text-white">Include XRF Service:</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.paySelectData.baseXrf).toFixed(2) }} per unit</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.paySelectData.totalXrf).toFixed(2) }}</td>
            </tr>

            <tr>
              <td class="font-semibold text-gray-900 dark:text-white"></td>
              <td class="font-semibold text-gray-900 dark:text-white"></td>
            </tr>
            <!-- Message optional-->
            <tr v-if="props.paySelectData.optionalMessage">
              <td class="font-semibold text-gray-900 dark:text-white">Optional Message:</td>
              <td colspan="2" class="text-s text-gray-900 dark:text-white">{{ getFirstTenWords(props.paySelectData.optionalMessage) }}</td>
            </tr>

            <tr>
              <td class="font-semibold text-gray-900 dark:text-white"></td>
              <td class="font-semibold text-gray-900 dark:text-white"></td>
            </tr>
  
            <!-- Added horizontal line before Total Amount -->
            <tr>
              <td colspan="3">
                <hr class="my-4 border-t-1 border-gray-900 dark:border-gray-200" />
              </td>
            </tr>
  
            <!-- Total Amount row -->
            <tr class="mt-4">
              <td colspan="2" class="font-semibold text-gray-900 dark:text-white  text-2xl">SubTotal:</td>
              <td class="font-semibold text-gray-900 dark:text-white text-2xl">${{ parseFloat(props.paySelectData.NewSubTotal).toFixed(2) }}</td> <!-- Display SubTotal from backend -->
            </tr>
            <!-- Added horizontal line before Total Amount -->
            <tr>
              <td colspan="3">
                <hr class="my-4 mb-8 border-t-2 border-gray-900 dark:border-gray-200" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <hr>
  
      <!-- Payment Method -->
      <div class="flex space-x-4 p-4">
      <!-- Manual Payment Section -->
      <div class="w-1/2 cursor-pointer flex-shrink-0">
          <div 
              @click="selectPaymentMethod('MANUAL')" 
              :class="[
                  'p-4 border rounded-lg flex flex-col h-full transition-all',
                  selectedPaymentMethod === 'MANUAL' ? 'bg-green-100 dark:bg-green-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700',
                  'border-gray-300 dark:border-gray-600',
                  'text-gray-900 dark:text-gray-200'
              ]"
          >
              <div class="flex items-center mb-2">
                  <h2 class="text-lg font-semibold">Manual Payment</h2>
              </div>
              <div class="grid grid-cols-2 gap-4 ml-7">
                  <div class="flex items-center p-3 text-left bg-white border border-gray-400 rounded-md transition dark:bg-gray-800 dark:border-gray-600">
                      <i class="fas fa-dollar-sign text-2xl text-green-600 mr-2" aria-hidden="true"></i>
                      <span class="text-lg">On Site Pay</span>
                  </div>
                  <div class="flex items-center p-3 text-left bg-white border border-gray-400 rounded-md transition dark:bg-gray-800 dark:border-gray-600">
                      <i class="fas fa-check-circle text-2xl text-blue-600 mr-2" aria-hidden="true"></i>
                      <span class="text-lg">CHECK</span>
                  </div>
              </div>
              <p v-if="selectedPaymentMethod === 'MANUAL'" class="p-8 text-sm text-gray-600 dark:text-gray-200">
                  On arrival, payment must be made either by cash or a check addressed to <strong>{{ companyName }}</strong>, which must be presented to our inspector before the start of service.
              </p>
              <p v-if="selectedPaymentMethod === 'MANUAL'" class="mt-2 text-sm text-gray-600 dark:text-gray-200 font-bold">
                  *This order will be manually verified.
              </p>
          </div>
      </div>

      <!-- Electronics Section -->
      <div class="w-1/2 cursor-pointer flex-shrink-0">
          <div 
              @click="selectPaymentMethod('ELECTRONICS')" 
              :class="[
                  'p-4 border rounded-lg flex flex-col h-full transition-all',
                  selectedPaymentMethod === 'ELECTRONICS' ? 'bg-green-100 dark:bg-green-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700',
                  'border-gray-300 dark:border-gray-600',
                  'text-gray-900 dark:text-gray-200'
              ]"
          >
              <div class="flex items-center mb-2">
                  <h2 class="text-lg font-semibold">Electronic Payment <i class="fas fa-lock text-green-500 mr-2" aria-hidden="true"></i></h2>
              </div>
              <div class="grid grid-cols-2 gap-4 ml-7">
                  <div class="flex items-center p-3 text-left bg-white border border-gray-400 rounded-md transition dark:bg-gray-800 dark:border-gray-600">
                      <i class="fas fa-credit-card text-xl text-yellow-500 mr-2" aria-hidden="true"></i>
                      <span>Stripe</span>
                  </div>
                  <div class="flex items-center p-3 text-left bg-white border border-gray-400 rounded-md transition dark:bg-gray-800 dark:border-gray-600">
                      <i class="fas fa-credit-card text-xl text-yellow-500 mr-2" aria-hidden="true"></i>
                      <span>Credit / Debit</span>
                  </div>
                  <div class="flex items-center p-3 text-left bg-white border border-gray-400 rounded-md transition dark:bg-gray-800 dark:border-gray-600">
                      <i class="fas fa-pencil-alt text-xl text-orange-500 mr-2" aria-hidden="true"></i>
                      <span>Card Manual Entry</span>
                  </div>
                  <div class="flex items-center p-3 text-left bg-white border border-gray-400 rounded-md transition dark:bg-gray-800 dark:border-gray-600">
                      <i class="fas fa-university text-xl text-purple-600 mr-2" aria-hidden="true"></i>
                      <span>Bank Payment</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
  

<div class="mt-6">
    <h2 class="text-xl font-bold flex items-center">
          <i class="fas fa-lock text-green-500 mr-2" aria-hidden="true"></i>
          Payment Security
    </h2>
    <p class="mt-2">Protecting your information is our top priority. Our website uses the most advanced technologies to ensure a safe and secure payment experience.</p>
    <p class="mt-2">We are PCI Level 1 compliant, the highest level of security certification. This ensures that our payment systems meet the most stringent security standards, providing you with confidence in our ability to safeguard your financial information.</p>
</div>
      <hr>
  
      <div v-if="selectedPaymentMethod" class="p-6 flex justify-between md:grid-cols-2 gap-4">
        <button type="button" @click="refreshPage" class="text-lg py-3 px-6 mx-auto block bg-red-500 text-white hover:bg-red-700 rounded">
          Cancel Payment
        </button>
        <PrimaryBtn @click="submitForm" class="text-lg py-3 px-6 mx-auto block">
            <i class="fa fa-arrow-circle-o-right text-white mr-2"></i>
            Proceed to Checkout
        </PrimaryBtn>
        
      </div>
      
    </Container>
  </template>
  
  