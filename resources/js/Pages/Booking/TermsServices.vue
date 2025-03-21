<script setup>
import { ref, computed, watch } from 'vue';  // Import `ref`, `computed`, and `watch` from 'vue'
import { useForm } from '@inertiajs/vue3';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";

// Access the APP_NAME environment variable directly
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name'; // Default if not set

// Receive the 'bookDate' prop from Inertia
const props = defineProps({
   bookDate: Object,
   message: Object,
   success: Boolean
});

console.log(props.message);

// Initialize the form using the received data
const form = useForm({
  discountCode: '',  // Add a discountCode field to track the value
  discountTotal: '',
  discountPercentage: '',
  inspector_name: '',
  selectedDate: props.bookDate.selectedDate,
  service: props.bookDate.service,
  price: parseFloat(props.bookDate.price?.replace(/,/g, '') || 0).toFixed(2),
  price_visual: parseFloat((props.bookDate.price_visual || 0).toString().replace(/,/g, '')).toFixed(2),
  price_dust: parseFloat(props.bookDate.price_dust?.replace(/,/g, '') || 0).toFixed(2),
  xrf: parseFloat(props.bookDate.xrf?.replace(/,/g, '') || 0).toFixed(2),
  xrf_reinspection_price: parseFloat(props.bookDate.xrf_reinspection_price?.replace(/,/g, '') || 0).toFixed(2),
  visual_reinspection_price: parseFloat(props.bookDate.visual_reinspection_price?.replace(/,/g, '') || 0).toFixed(2),
  dust_wipe_reinspection_price: parseFloat(props.bookDate.dust_wipe_reinspection_price?.replace(/,/g, '') || 0).toFixed(2),
  
  weekendFee: parseFloat(props.bookDate.weekendFee || 0), // This does not need to be formatted to 2 decimal places
  totalWeekendFee: parseFloat(props.bookDate.totalWeekendFee || 0), // This does not need to be formatted to 2 decimal places
  NewSubTotal: parseFloat(props.bookDate.NewSubTotal || 0).toFixed(2), // Ensure NewSubTotal is parsed and formatted
  totalXrf: parseFloat(props.bookDate.totalXrf || 0).toFixed(2), // Ensure totalXrf is parsed and formatted
  baseXrf: parseFloat(props.bookDate.baseXrf || 0).toFixed(2), // Ensure baseXrf is parsed and formatted
  credSucharg: parseFloat(props.bookDate.credSucharg || 0).toFixed(2), // Ensure credSucharg is parsed and formatted
  totalPay: parseFloat(props.bookDate.totalPay || 0).toFixed(2), // Ensure totalPay is parsed and formatted
  basePrice: parseFloat(props.bookDate.basePrice || 0).toFixed(2), // Format basePrice to 2 decimal places
  totalBasePrice: parseFloat(props.bookDate.totalBasePrice || 0).toFixed(2), // Format totalBasePrice to 2 decimal places

  includeXrf: props.bookDate.includeXrf,
  InspectionType: props.bookDate.InspectionType,
  orderNumber: props.bookDate.orderNumber,
  dcaMunicode: props.bookDate.dcaMunicode,
  municipality: props.bookDate.municipality,
  county: props.bookDate.county,
  methodology: props.bookDate.methodology,
  totalCost: parseFloat(props.bookDate.totalCost || 0).toFixed(2),
  subtotal: parseFloat(props.bookDate.subtotal?.replace(/,/g, '') || 0).toFixed(2),
  selectedTime: props.bookDate.selectedTime,
  name: props.bookDate.name,
  email: props.bookDate.email,
  phone: props.bookDate.phone,
  address: props.bookDate.address,
  apt: props.bookDate.apt,
  city: props.bookDate.city,
  state: props.bookDate.state,
  block: props.bookDate.block,
  lot: props.bookDate.lot,
  units: props.bookDate.units,
  builtBefore1978: props.bookDate.builtBefore1978,
  useSameContactForAll: props.bookDate.useSameContactForAll,
  siteContact: props.bookDate.siteContact,
  siteContactEmail: props.bookDate.siteContactEmail,
  siteContactPhone: props.bookDate.siteContactPhone,
  optionalMessage: props.bookDate.optionalMessage || '',
  unitsDetails: props.bookDate.unitsDetails.map(unit => ({
    ...unit,
    unitNumber: unit.unitNumber || '',
    siteContact: unit.siteContact || '',
    siteContactEmail: unit.siteContactEmail || '',
    siteContactPhone: unit.siteContactPhone || '',
  })),
  agreedToTerms: false, // Checkbox for Terms of Service
});

// Function to update the form values with new data from the backend
const updateFormValues = (updatedData) => {
  console.log('Updated Data:', updatedData);

  form.NewSubTotal = parseFloat(updatedData.NewSubTotal || 0).toFixed(2);
  form.totalXrf = parseFloat(updatedData.totalXrf || 0).toFixed(2);
  form.baseXrf = parseFloat(updatedData.baseXrf || 0).toFixed(2);
  form.credSucharg = parseFloat(updatedData.credSucharg || 0).toFixed(2);
  form.totalPay = parseFloat(updatedData.totalPay || 0).toFixed(2);
  form.basePrice = parseFloat(updatedData.basePrice || 0).toFixed(2);
  form.totalBasePrice = parseFloat(updatedData.totalBasePrice || 0).toFixed(2);
  form.discountTotal = updatedData.discountTotal; // The discount total
  form.discountPercentage = updatedData.discountPercentage; // The discount percentage
  form.inspector_name = updatedData.inspector_name; // The discount percentage
};

// Define the computed property to get the first 10 words of optionalMessage
const getFirstTenWords = computed(() => {
  if (props.bookDate?.optionalMessage) {
    return props.bookDate.optionalMessage.trim().split(/\s+/).slice(0, 10).join(' ');
  }
  return ''; // or return a fallback message like "No optional message provided"
});

// Watch for changes in `props.bookDate` and update form values accordingly
watch(() => props.bookDate, (newBookDate) => {
  updateFormValues(newBookDate);
}, { immediate: true });

// Function to format the date in "Tue, Oct 10, 2024" format
const formatDate = (date) => {
  const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
  return new Date(date).toLocaleDateString('en-US', options);
};

// Toggle visibility for discount code section
const hasDiscountCode = ref(false);

const toggleDiscountCodeVisibility = () => {
  hasDiscountCode.value = !hasDiscountCode.value;  // Toggle the visibility state
};

// Computed property for checking if a discount code is applied
const isDiscountCodeApplied = computed(() => !!form.discountCode);

// Function to apply discount code
const applyDiscountCode = () => {
  if (!form.discountCode) {
    alert("Please enter a discount code.");
    return;
  }

  form.post(route('service.applyDiscount'), {
    data: {
      discountCode: form.discountCode,
      selectedDate: form.selectedDate,
      service: form.service,
      // Add other necessary parameters as needed depending on your backend expectations
    },
    onSuccess: (response) => {
      console.log('Discount code applied successfully:', response);
      
      // Update form with new data from the response
      updateFormValues(response.bookDate);
      if (response.error) {
        alert(response.error); // Handle error if any
      }
    },
    onError: (error) => {
      console.error('Failed to apply discount code:', error);
    }
  });
};

// Handle form submission
const submitForm = () => {
  // Check if terms are agreed upon before submission
  if (!form.agreedToTerms) {
    alert('You must agree to the Terms of Service before proceeding.');
    return;
  }

  // Log the form data to check if it's being submitted correctly
  console.log('Submitting form with data:', form);

  form.post(route('service.paymentOptions'), {
    method: "POST",
    onFinish: () => {
      console.log('Form submitted successfully');
    },
    onError: (errors) => {
      // Log the errors if the submission fails
      console.log('Form submission failed. Errors:', errors);
    }
  });
};

// Reload the page
const refreshPage = () => {
  window.location.reload();  // This will reload the current page
};
</script>


<template>
    <Head title="- Terms of Service (TOS)" />
    <Container class="w-3/5">
      <div class="mb-8 text-center">
        <Title>{{ props.bookDate.InspectionType === 'reInspection' ? 'Lead Paint Re-Inspection' : props.bookDate.service }}
          <br/>
          <span v-if="props.bookDate.InspectionType === 'reInspection'">(Order # {{ props.bookDate.orderNumber }})</span>
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
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">{{ props.bookDate.service }}</td>
            </tr>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Service Type:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">
                {{ props.bookDate.InspectionType === 'newInspection' ? 'New Inspection' : 'Re-Inspection' }} <br>
                <span v-if="props.bookDate.orderNumber" class="font-bold text-xs">(order # {{ props.bookDate.orderNumber }})</span>
              </td>
            </tr>

            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">County:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">{{ props.bookDate.county }} County</td>
            </tr>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Municipality:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">{{ props.bookDate.municipality }}</td>
            </tr>
            <tr v-if="props.bookDate.includeXrf === true">
              <td class="font-semibold text-gray-900 dark:text-white">Method:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">
                {{ props.bookDate.methodology }} + XRF included
              </td>
            </tr>
            <tr v-else>
              <td class="font-semibold text-gray-900 dark:text-white">Method:</td>
              <td colspan="2" class="font-semibold text-center text-gray-900 dark:text-white">
                {{ 
                  (props.bookDate.InspectionType === 'reInspection') ? 
                    (props.bookDate.includeXrf ? 'Dust Wipe Sampling + XRF' : 'Dust Wipe Sampling') :
                  (props.bookDate.InspectionType === 'newInspection') ? 
                    (props.bookDate.includeXrf ? `${props.bookDate.methodology} + XRF` : props.bookDate.methodology) :
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
              <td class="font-semibold text-gray-900 dark:text-white">{{ formatDate(props.bookDate.selectedDate) }} </td>
            </tr>
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Selected Time:</td>
              <td colspan="2" class="font-semibold text-gray-900 dark:text-white">
                {{ props.bookDate.selectedTime === 'morning' ? 'Morning 8.00 am - 12.00 pm' : 'Afternoon 12.00 pm - 5.00 pm' }} </td>
            </tr>

            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">No of Units:</td>
              <td class="font-semibold text-gray-900 dark:text-white">{{ props.bookDate.units }} Unit{{ props.bookDate.units > 1 ? 's' : '' }}</td>
            </tr>

            <!-- Added horizontal line before Total Amount -->
            <tr>
              <td colspan="3">
                <hr class="my-4 " />
              </td>
            </tr>
  
               <!-- Bold Text to Show Discount Code Section -->
                <!-- Discount Code Section -->
                  <tr v-if="props.bookDate.InspectionType == 'newInspection'">
                      <td colspan="3">
                          <div 
                              @click="toggleDiscountCodeVisibility" 
                              class="font-semibold text-sm cursor-pointer my-0"
                              :class="{'text-green-600': props.bookDate.discountCode, 'text-blue-600': !props.bookDate.discountCode}"
                            >
                              {{ props.bookDate.discountCode ? `Discount applied successfully! You saved $${props.bookDate.discountTotal}` : 'I have a discount code' }}
                          </div>
                      </td>
                  </tr>

                  <tr v-if="hasDiscountCode && !props.bookDate.discountCode && props.bookDate.InspectionType !== 'reInspection'">
                      <td class="font-semibold text-gray-900 dark:text-white">Discount Code:</td>
                      <td colspan="2">
                          <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                              <input
                                  v-model="form.discountCode"
                                  type="text"
                                  class="w-full sm:w-auto p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Enter discount code"
                              />
                              <button
                                  @click="applyDiscountCode"
                                  class="w-full sm:w-auto bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none"
                              >
                                  Apply
                              </button>
                  </div>
                  <!-- Display error message for order number -->
                </td>
              </tr>
              <!-- Display error message for order number -->
              <tr v-if="message">
              <td colspan="3">
                <span class="bg-green-100 border border-green-300 text-green-800 text-center font-semibold py-2 px-4 rounded-md shadow-md mb-4">
                  {{ message.errorMessage }}
              </span>
              </td>
            </tr>

            <!-- Added horizontal line before Total Amount -->
            <tr>
              <td colspan="3">
                <hr class="my-4 mb-2 border-t-2 border-gray-900 dark:border-gray-200" />
              </td>
            </tr>

            <!-- Display Weekend Fee if applicable -->
            <tr v-if="props.bookDate.totalWeekendFee > 0">
              <td class="font-semibold text-gray-900 dark:text-white">Weekend Fee:</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ props.bookDate.weekendFee }} per unit</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.bookDate.totalWeekendFee).toFixed(2) }}</td>
            </tr>
            
            <!-- Cost per unit -->
            <tr>
              <td class="font-semibold text-gray-900 dark:text-white">Cost:
                <span v-if="props.bookDate.discountCode" class="text-red-600"> 
                  <sup class="text-xs">({{ props.bookDate.discountPercentage }}% off)</sup> 
                </span>
              </td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.bookDate.basePrice).toFixed(2) }} per unit</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.bookDate.totalBasePrice).toFixed(2) }}</td>
            </tr>

            <!-- Conditionally display the XRF service row if 'includeXrf' is true -->
            <tr v-if="props.bookDate.includeXrf">
              <td class="font-semibold text-gray-900 dark:text-white">Include XRF Service:</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.bookDate.baseXrf).toFixed(2) }} per unit</td>
              <td class="font-semibold text-gray-900 dark:text-white">${{ parseFloat(props.bookDate.totalXrf).toFixed(2) }}</td>
            </tr>

            <tr>
              <td class="font-semibold text-gray-900 dark:text-white"></td>
              <td class="font-semibold text-gray-900 dark:text-white"></td>
            </tr>
            <!-- Message optional-->
            <tr v-if="props.bookDate.optionalMessage">
              <td class="font-semibold text-gray-900 dark:text-white">Optional Message:</td>
              <td colspan="2" class="text-s text-gray-900 dark:text-white">{{ getFirstTenWords }}</td>
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
              <td class="font-semibold text-gray-900 dark:text-white text-2xl">${{ parseFloat(props.bookDate.NewSubTotal).toFixed(2) }}</td> <!-- Display SubTotal from backend -->
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
  
      <!-- Terms of Service Section -->
      <div>
        <Title class="text-center mt-6 text-gray-900 dark:text-white">Terms of Service (TOS)</Title>
        <div class="p-4 max-h-96 overflow-y-auto border border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-600">
          <p class="text-sm font-bold">Right-of-Entry</p>
          The undersigned (Applicant) hereby unconditionally authorizes {{ companyName }} and their respective assigns, employees, agents, and contractors (collectively, the “Lead-Safe Inspector/Risk Assessor”) to have the right of access  and  to  enter  in  and  onto  the  property  described  above  for  the  purpose  of  performing  property  and environmental and historic preservation review inspections, taking sample materials for specialized testing for the  purposes  of  participating  in  the  NJ  Lead-Safe Program. 

          <p class="text-sm font-bold mt-4">No Show/No Entry Fee</p>
          The Applicant will be responsible for paying a No-Show/No-Entry fee to {{ companyName }} company of
          <strong>$150</strong> if the lead inspector/risk assessor or contractor arrives onsite for a scheduled site visit at the subject
          property and testing or other evaluation services cannot be conducted or completed due to *no fault of the lead 
          inspector/risk assessor or contractor or Pledge's employees <strong>But</strong> due to faults arising from the tenant or landlord/owner.

          <p class="text-sm font-bold mt-4">Pets</p>
          The property owner is responsible for ensuring that no unleashed or unrestrained dogs, or other potentially dangerous pets, are present at the property in a way that prevents full access for scheduled testing or observation of building conditions. If {{ companyName }} is unable to complete the scheduled on-site testing due to the presence of such pets, the owner will be required to pay a no-show/no-entry fee as described above. If partial access is provided that prevents {{ companyName }} company from completing the required NJ Lead-Safe Program inspection or testing, it will be considered as no access, and a No-Show/No-Entry Fee will apply.

          <p class="text-sm font-bold mt-4">Photos</p>
          The Applicant understands and authorizes {{ companyName }}, along with its contractors, employees, agents, and representatives, to take photos, digital likenesses, and audio/video recordings of the Applicant, property, and damages. The Applicant also authorizes the use of these items for the purpose of the Lead Evaluation Report.
  
          <p class="text-sm font-bold mt-4">Warranty</p>
          {{ companyName }} guarantees that its services will meet the highest industry standards and comply with applicable laws and regulations. Except for this standard of care, no other warranties, express or implied, are provided, and all implied warranties are specifically excluded.
  
          <p class="text-sm font-bold mt-4">Guarantee</p>
          {{ companyName }}'s inspection services do not guarantee the issuance of lead-free, lead-safe, lead-hazard, or similar regulatory certificates or forms. The issuance of such certificates or forms is contingent upon the results of the field inspections conducted during the course of the services.

          <p class="text-sm font-bold mt-4">Indemnity</p>
          The Applicant agrees to indemnify, defend, and hold harmless {{ companyName }}, its officers, directors, employees, agents, and contractors from and against any and all claims, demands, losses, costs, expenses, obligations, liabilities, damages, recoveries, and deficiencies, including interest, penalties, and reasonable attorney fees, that {{ companyName }} may incur or suffer, that arise, result from, or relate to the Applicant’s breach of this Agreement, except in cases where the injury or loss is solely caused by the negligence or willful misconduct of {{ companyName }}, or its employees, officers, or agents.<br>
          {{ companyName }} is not responsible for the creation or presence of any hazardous, toxic, or dangerous substances or conditions on the property, and its compensation is not based on the risks associated with such substances or conditions.
          
          <p class="text-sm font-bold mt-4">Governing Law</p>
          This Agreement will be governed by and interpreted in accordance with the laws of the State of New Jersey.

          <p class="text-sm font-bold mt-4">Tenant Notification</p>
          The responsibility for notifying tenants or occupants of the property lies with the Client, or with the Client's management, owner, or designated staff. {{ companyName }} is not responsible for tenant notification.

          <p class="text-sm font-bold mt-4">Payment Obligation</p>
          The Client agrees to pay {{ companyName }} for services rendered, even if the findings or results in {{ companyName }} report or testing are unfavorable to the Client’s interests.

          <p class="text-sm font-bold mt-4">Arbitration of Disputes</p>
          The Client agrees that any disputes related to this Agreement, including those concerning breach of contract, warranties, misrepresentations, injuries, or other legal matters, will be settled through binding arbitration under the rules of the American Arbitration Association (AAA) or another organization chosen by {{ companyName }}. The Client must notify {{ companyName }} in writing of any claims at their address [648 Newark Ave, Elizabeth NJ 07208] and allow a reasonable time for resolution before initiating arbitration. This arbitration requirement is governed by the Federal Arbitration Act (9 U.S.C. § 1 et seq.) and will remain in effect even after the services under this Agreement are completed.

          <p class="text-sm font-bold mt-4">Liability Limits and Disclaimer</p>
          The Client agrees that {{ companyName }}'s total liability for any claims, losses, damages, or expenses, including attorney’s fees, arising from or related to this Project or Agreement, regardless of the cause (such as {{ companyName }}'s negligence, errors, omissions, strict liability, breach of contract, statute violations, or breach of warranty), shall not exceed the total fees paid by the Client to {{ companyName }} under this Agreement. {{ companyName }} will not be liable for any indirect, incidental, special, or consequential damages, including but not limited to lost profits or business interruptions, arising from or related to the services provided under this Agreement.

        </div>
        <hr>
  
        <div class="flex flex-col items-center p-8 mb-4">
          <div class="font-medium text-center mb-2 text-gray-900 dark:text-white">
            By clicking this check box and proceeding to Checkout you agree to all Terms of Service stated above.
          </div>
          <div class="flex items-center">
            <input 
              type="checkbox" 
              id="agree-checkbox" 
              v-model="form.agreedToTerms" 
              class="mr-2 h-4 w-4 text-green-600 focus:ring-0"
            >
            <label for="agree-checkbox" class="text-medium text-gray-900 dark:text-white">I Agree to the Terms of Service</label>
          </div>
        </div>
      </div>
      <hr>
  
      <div class="p-6 flex justify-between md:grid-cols-2 gap-4">
        <button type="button" @click="refreshPage" class="text-lg py-3 px-6 mx-auto block bg-red-500 text-white hover:bg-red-700 rounded">
          Cancel Payment
        </button>
        <button @click="submitForm" class="bg-black text-white rounded text-lg py-3 px-6 mx-auto block">
          <i class="fas fa-arrow-circle-o-right text-white mr-2" aria-hidden="true"></i>
          Proceed to Checkout
        </button>
        
      </div>
    </Container>
  </template>
  
  