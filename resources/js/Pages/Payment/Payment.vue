<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';  // Import `ref` and `computed` from 'vue'
import { useForm } from '@inertiajs/vue3';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue"; // Your button component
// Load Stripe
import { loadStripe } from '@stripe/stripe-js';

// Receive the 'eData' prop from Inertia
const props = defineProps({
  eData: Object, // Receiving the validated form data from the server
  clientSecret: String, // Receiving the clientSecret from the server
    bookingRequestId: Number, // Receiving the bookingRequest ID for tracking purposes
    orderId: String,
});

// Initialize the form using the received data
const form = useForm({
  // Basic Fields
  service: props.eData.service,
  selectedDate: props.eData.selectedDate,
  price: typeof props.eData.price === 'string' 
         ? parseFloat(props.eData.price.replace(/,/g, '')) 
         : props.eData.price,  // If it's a string, remove commas and convert to float
  price_visual: typeof props.eData.price_visual === 'string' 
                ? parseFloat(props.eData.price_visual.replace(/,/g, '')) 
                : props.eData.price_visual, // Similarly for price_visual
  price_dust: typeof props.eData.price_dust === 'string' 
              ? parseFloat(props.eData.price_dust.replace(/,/g, '')) 
              : props.eData.price_dust, // Similarly for price_dust
  xrf: typeof props.eData.xrf === 'string' 
        ? parseFloat(props.eData.xrf.replace(/,/g, '')) 
        : props.eData.xrf,  // Similarly for xrf
  xrf_reinspection_price: typeof props.eData.xrf_reinspection_price === 'string' 
                          ? parseFloat(props.eData.xrf_reinspection_price.replace(/,/g, '')) 
                          : props.eData.xrf_reinspection_price, // Similarly for xrf_reinspection_price
  visual_reinspection_price: typeof props.eData.visual_reinspection_price === 'string' 
                             ? parseFloat(props.eData.visual_reinspection_price.replace(/,/g, '')) 
                             : props.eData.visual_reinspection_price, // Similarly for visual_reinspection_price
  dust_wipe_reinspection_price: typeof props.eData.dust_wipe_reinspection_price === 'string' 
                                ? parseFloat(props.eData.dust_wipe_reinspection_price.replace(/,/g, '')) 
                                : props.eData.dust_wipe_reinspection_price, // Similarly for dust_wipe_reinspection_price
  
  includeXrf: props.eData.includeXrf,
  selectedTime: props.eData.selectedTime,
  name: props.eData.name,
  email: props.eData.email,
  phone: props.eData.phone,
  address: props.eData.address,
  apt: props.eData.apt,
  city: props.eData.city,
  state: props.eData.state,
  block: props.eData.block,
  lot: props.eData.lot,
  units: props.eData.units,
  builtBefore1978: props.eData.builtBefore1978,
  useSameContactForAll: props.eData.useSameContactForAll,
  siteContact: props.eData.siteContact,
  siteContactEmail: props.eData.siteContactEmail,
  siteContactPhone: props.eData.siteContactPhone,
  optionalMessage: props.eData.optionalMessage,
  unitsDetails: props.eData.unitsDetails,
  agreedToTerms: props.eData.agreedToTerms, // Checkbox for Terms of Service

  // Optional Fields (nullable)
  totalWeekendFee: typeof props.eData.totalWeekendFee === 'string' 
                   ? parseFloat(props.eData.totalWeekendFee.replace(/,/g, '')) 
                   : props.eData.totalWeekendFee, // If provided, convert to number
  weekendFee: typeof props.eData.weekendFee === 'string' 
                   ? parseFloat(props.eData.weekendFee.replace(/,/g, '')) 
                   : props.eData.weekendFee, // If provided, convert to number
  totalCost: typeof props.eData.totalCost === 'string' 
             ? parseFloat(props.eData.totalCost.replace(/,/g, '')) 
             : props.eData.totalCost, // Similarly for totalCost
  
  // New calculated fields based on the validated data
  NewSubTotal: typeof props.eData.NewSubTotal === 'string' 
               ? parseFloat(props.eData.NewSubTotal.replace(/,/g, '')) 
               : props.eData.NewSubTotal, // Similarly for NewSubTotal
  
  totalXrf: typeof props.eData.totalXrf === 'string' 
            ? parseFloat(props.eData.totalXrf.replace(/,/g, '')) 
            : props.eData.totalXrf,  // Similarly for totalXrf
  
  baseXrf: typeof props.eData.baseXrf === 'string' 
           ? parseFloat(props.eData.baseXrf.replace(/,/g, '')) 
           : props.eData.baseXrf,  // Similarly for baseXrf
  
  credSucharg: typeof props.eData.credSucharg === 'string' 
               ? parseFloat(props.eData.credSucharg.replace(/,/g, '')) 
               : props.eData.credSucharg,  // Similarly for credSucharg
  
  totalPay: typeof props.eData.totalPay === 'string' 
            ? parseFloat(props.eData.totalPay.replace(/,/g, '')) 
            : props.eData.totalPay,  // Similarly for totalPay
  
  basePrice: typeof props.eData.basePrice === 'string' 
            ? parseFloat(props.eData.basePrice.replace(/,/g, '')) 
            : props.eData.basePrice,  // Similarly for basePrice
  
  totalBasePrice: typeof props.eData.totalBasePrice === 'string' 
                  ? parseFloat(props.eData.totalBasePrice.replace(/,/g, '')) 
                  : props.eData.totalBasePrice,  // Similarly for totalBasePrice
  
  dcaMunicode: props.eData.dcaMunicode,
  municipality: props.eData.municipality,
  county: props.eData.county,
  methodology: props.eData.methodology,
  orderNumber: props.eData.orderNumber,
  subtotal: typeof props.eData.subtotal === 'string' 
            ? parseFloat(props.eData.subtotal.replace(/,/g, '')) 
            : props.eData.subtotal, // Similarly for subtotal 

  orderId: props.eData.orderId,
  discountCode: props.eData.discountCode,
  discountTotal: props.eData.discountTotal,
  discountPercentage: props.eData.discountPercentage,
});



// Handle form submission
// Function to format the date in "Tue, Oct 10, 2024" format
const formatDate = (date) => {
  const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
  return new Date(date).toLocaleDateString('en-US', options);
};

// Reload the page
const refreshPage = () => {
  window.location.reload();  // This will reload the current page
};

// Set a timer to refresh the page every 30 minutes (1800000 milliseconds)
let refreshInterval;

onMounted(() => {
  refreshInterval = setInterval(() => {
    refreshPage();
  }, 1800000); // Refresh every 30 minutes
});

// Clear the interval when the component is unmounted
onBeforeUnmount(() => {
  clearInterval(refreshInterval);
});

// Define a method to get the first ten words
const getFirstTenWords = (message) => {
  if (!message) return ''; // Handle empty or undefined message
  return message.split(' ').slice(0, 10).join(' ') + (message.split(' ').length > 10 ? '...' : '');
};
//========================================================

const paymentStatus = ref(null);
const stripe = ref(null);
const elements = ref(null);
const loading = ref(false);
const paymentElement = ref(null);

onMounted(async () => {
  try {
    // Assume that you received the clientSecret from the backend via Inertia
    const clientSecret = props.clientSecret;

    // Load Stripe.js with your public key
    stripe.value = await loadStripe(import.meta.env.VITE_STRIPE_KEY);

    // Create the Elements instance and initialize a Payment Element with the clientSecret
    elements.value = stripe.value.elements({
      clientSecret: clientSecret, // Provide the clientSecret
    });

    paymentElement.value = elements.value.create('payment');
    paymentElement.value.mount('#payment-element');
  } catch (error) {
    console.error('Error initializing Stripe Elements:', error);
  }
});

// Handle the form submission and payment process
// Handle the payment submission
const submitPayment = async () => {
  loading.value = true;

  // Use the Payment Element to confirm payment
  const { error, paymentIntent } = await stripe.value.confirmPayment({
    elements: elements.value,
    confirmParams: {
      return_url: window.location.origin + '/payment/success', // Full URL
    },
  });

  if (error) {
    // Show error to your customer (e.g., insufficient funds)
    paymentStatus.value = { success: false, message: error.message };
  } else if (paymentIntent.status === 'succeeded') {
    // Payment was successful, now submit the form
    submitForm();
  } else {
    paymentStatus.value = { success: false, message: 'Payment failed.' };
  }

  loading.value = false;
};

// Handle form submission after successful payment
const submitForm = () => {
  form.post(route('/payment/success'), {
    method: "POST",
    onFinish: () => {
      console.log('Form submitted successfully after payment');
      paymentStatus.value = { success: true, message: 'Payment and form submission successful!' };
      Inertia.visit('/payment/success'); // Redirect to a success page as needed
    },
    onError: (errors) => {
      console.log('Form submission failed. Errors:', errors);
      paymentStatus.value = { success: false, message: 'Form submission failed after payment.' };
    }
  });
};

</script>

<template>
  <Head title="- Billing details" />
  <Container class="w-3/5">
    <form @submit.prevent="submitPayment" >
      <div class="mb-8 text-center">
        <Title>{{ props.eData.service }} Order & Billing details</Title>
      </div>
      <hr>

      <!-- Two-column layout for Billing Details and Order Summary -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Column 1: Billing Details -->
        <div>
          <h2 class="text-xl font-semibold mb-4">Order ID: {{ props.orderId }}</h2>
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
              <table class="table-auto w-full max-w-lg text-left text-gray-900 dark:text-white">
              <tbody>
                <tr>
                  <td class="text-gray-900 text-sm font-bold dark:text-white">Rental Property Address:</td>
                  <td colspan="2" class="text-right text-sm text-gray-900 dark:text-white">
                    <strong>{{ props.eData.name }} </strong>,<br>
                    {{ props.eData.address }}, {{ props.eData.apt }} <br>{{ props.eData.municipality }}, {{ props.eData.state }}<br>
                    {{ props.eData.city }} - {{ props.eData.county }} County</td>
                </tr>
                <tr>
                  <td colspan="3">
                    <hr class="my-1 " />
                  </td>
                </tr>
                <tr v-if="props.eData.useSameContactForAll">
                  <td class="text-gray-900 font-bold text-sm dark:text-white">Site Contact:</td>
                  <td colspan="2" class="text-right text-sm text-gray-900 dark:text-white">
                    Site Contact: {{ props.eData.siteContact }} <br>Email: {{ props.eData.siteContactEmail }} <br>Phone: {{ props.eData.siteContactPhone }}
                  </td>
                </tr>

                <!-- If 'useSameContactForAll' is false, loop over 'unitsDetails' to show site contact information for each unit -->
                <tr v-else>
                  <td class="text-gray-900 font-bold text-sm dark:text-white">Units Details:</td>
                  <td colspan="2" class="text-right text-xs text-gray-900 dark:text-white">
                    <div v-for="(unit, index) in props.eData.unitsDetails" :key="index" class="mb-3">
                      <p><strong>Unit {{ index + 1 }}:</strong></p>  <!-- Dynamically displaying "Unit 1", "Unit 2", etc. -->
                      <p>Site Contact: {{ unit.siteContact }} <br>Email: {{ unit.siteContactEmail }} <br>Phone: {{ unit.siteContactPhone }}</p>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <hr class="my-1 " />
                  </td>
                </tr>                
                <tr>
                  <td class="text-gray-900 font-bold mt-4 text-sm dark:text-white">Selected Service:</td>
                  <td colspan="2" class="text-center font-bold mt-4 text-sm text-gray-900 dark:text-white">{{ props.eData.service }}</td>
                </tr>
                <tr>
                  <td class="text-gray-900 mt-4 text-sm dark:text-white">Inspection Type:</td>
                  <td colspan="2" class="text-center font-bold mt-4 text-sm text-gray-900 dark:text-white">
                    {{ props.eData.InspectionType === 'reInspection' ? `Re-Inspection \n\n (${props.eData.orderNumber})` : 'New Inspection' }}
                  </td>
                </tr>

                <tr>
                  <td colspan="1" class="text-gray-900 text-sm dark:text-white">Method:</td>
                  <td colspan="2" class="text-gray-900 text-sm dark:text-white">{{ props.eData.methodology }}<br>
                    <span class="text-xs font-bold text-gray-900 dark:text-white">({{ props.eData.county }} County)</span></td>
                </tr>
                <tr>
                  <td class="text-gray-900 text-sm dark:text-white">Time Slot</td>
                  <td colspan="2" class="text-gray-900 text-sm dark:text-white">{{ props.eData.selectedTime === 'morning' ? 'Morning 8.00 am - 12.00 pm' : 'Afternoon 12.00 pm - 5.00 pm' }}</td>
                </tr>
                <tr>
                  <td class="text-gray-900 text-sm dark:text-white">Date</td>
                  <td colspan="2" class="text-gray-900 text-sm dark:text-white">{{ formatDate(props.eData.selectedDate) }}</td>
                </tr>
                <tr>
                  <td class="text-gray-900 text-sm dark:text-white">No of Units:</td>
                  <td class="text-gray-900 text-sm dark:text-white">{{ props.eData.units }} Units</td>
                </tr>
                <tr v-if="props.eData.optionalMessage">
                  <td class="text-gray-900 text-sm dark:text-white">Optional Message:</td>
                  <td colspan="2" class="text-xs text-gray-900 dark:text-white">{{ getFirstTenWords(props.eData.optionalMessage) }}</td>
                </tr>

                <tr>
                  <td colspan="3">
                    <hr class="my-1 " />
                  </td>
                </tr>
      
      
                <!-- Display Weekend Fee if applicable -->
                <tr v-if="props.eData.totalWeekendFee > 0">
                  <td class="text-gray-900 text-sm dark:text-white">Weekend Fee:</td>
                  <td class="text-gray-900 text-sm dark:text-white">${{ props.eData.weekendFee }} fee per unit</td>
                  <td class="text-gray-900 text-sm dark:text-white">${{ props.eData.totalWeekendFee }}</td>
                </tr>
      
                <!-- Cost per unit -->
                <tr>
                  <td class="text-gray-900 text-sm dark:text-white">{{ props.eData.service }} Cost:
                    <span v-if="props.eData.discountPercentage" class="text-red-600"> 
                      <sup class="text-xs font-bold">({{ props.eData.discountPercentage }}% off)</sup> 
                    </span>
                  </td>
                  <td class="text-gray-900 text-sm dark:text-white">${{ props.eData.basePrice }} per unit</td>
                  <td class="text-gray-900 text-sm dark:text-white">${{ props.eData.totalBasePrice }}</td>
                </tr>
      
                <!-- Conditionally display the XRF service row if 'includeXrf' is true -->
                <tr v-if="props.eData.includeXrf">
                  <td class="text-gray-900 text-sm dark:text-white">XRF Service (Yes):</td>
                  <td class="text-gray-900 text-sm dark:text-white">${{ props.eData.baseXrf }} fee per unit</td>
                  <td class="text-gray-900 text-sm dark:text-white">${{ props.eData.totalXrf }}</td>
                </tr>
      
                <tr>
                  <td class="text-gray-900 text-sm dark:text-white"></td>
                  <td class="text-gray-900 text-sm dark:text-white"></td>
                </tr>
      
                <!-- Added horizontal line before Total Amount -->
                <tr>
                  <td colspan="3">
                    <hr class="my-1 " />
                  </td>
                </tr>
      
                <!-- Sub Total Amount row -->
                <tr>
                  <td colspan="2" class="font-bolder text-gray-900 dark:text-white">Subtotal:</td>
                  <td class="font-bolder text-gray-900 dark:text-white">${{ props.eData.NewSubTotal }}</td> <!-- Display SUBtotalAmount from backend -->
                </tr>
                <tr>
                  <td colspan="3">
                    <hr class="my-1 " />
                  </td>
                </tr>
      
                <!-- Credit Card Surcharge	 -->
                <tr>
                  <td colspan="2" class="font-bolder text-gray-900 dark:text-white">Credit Card Surcharge	:</td>
                  <td class="font-bolder text-gray-900 dark:text-white">${{ props.eData.credSucharg }}</td> <!-- Display sucharge from backend -->
                </tr>
               <tr>
                  <td colspan="3">
                    <hr class="my-4 " />
                  </td>
                </tr>
      
                <!-- Total Amount row -->
                <tr class="mt-4">
                  <td colspan="2" class="font-semibold text-gray-900 dark:text-white">Total Amount:</td>
                  <td class="font-semibold text-gray-900 dark:text-white">${{ props.eData.totalPay }}</td> <!-- Display totalPay from backend -->
                </tr>
              </tbody>
            </table>
          </div>

        </div>

          <!-- Column 2: Order Summary -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Payment Details</h2>

          <!-- Stripe Card Element -->
          <div class="col-span-2 mt-3 dark:text-white" id="payment-element"></div>
          <!-- <div class="col-span-2 mt-6 p-5 dark:text-white" id="card-element"></div> This will span both columns -->
          <hr>
          <span class="text-xs text-right">Please ensure this is the billing zip code for your Credit Card</span>

              <!-- Payment Status -->
              <div v-if="paymentStatus" :class="paymentStatus.success ? 'success' : 'error'" class="mt-6 mb-6 font-medium text-center">
                <p :class="paymentStatus.success ? 'text-green-500' : 'text-red-500'">{{ paymentStatus.message }}</p>
              </div>

                <!-- Actions Section -->
                <div class="col-span-2 p-5">
                    <PrimaryBtn type="submit" :disabled="loading" class="w-full py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-300">
                      Pay ${{ props.eData.totalPay }}
                    </PrimaryBtn>

                    <!-- Cancel Payment Link -->
                    <Link href="#" @click.prevent="refreshPage" class="text-x py-6 px-6 mt-5 mx-auto block text-slate-600 hover:text-slate-300 text-center dark:text-white">
                      Cancel Payment
                    </Link>
                </div>

        </div>
       
      </div>
      <hr>
    </form>
  </Container>
</template>