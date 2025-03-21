<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import { ref, watch, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TextLink from "../../../Components/TextLink.vue";
import Title from "../../../Components/Title.vue";
import PaginationLinks from '../../../Components/PaginationLinks.vue';  
import { debounce } from 'lodash';

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the APP_NAME environment variable directly
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name';

// Access the Inertia page props
const props = defineProps({
    eUser: Object,
    discountCodes: Array,
    bookingRequests: Object,
    records: Array,
    countInsBookings: Number,
    countIncRec: Number,
    countIncCompleted: Number,
    countIncPending: Number,
    overDue: Number,
    upComing: Number,
    reInspectionBook: Number,

    weekStart: String,
    weekEnd: String,
    message: String,
    records: Array,
    bookings: Object,
    searchTerm: String,
    records: Object,
    signatureUrl: String,
    licenseUrl: String,
});

// Reactive search term
const search = ref(props.searchTerm);

// Watch for changes in the search term
watch(
    search, 
    debounce((q) => {
        // Use Inertia to make the request and stay on the same page
        router.get(window.location.pathname, { search: q }, { preserveState: true });
    }, 500)
);

// Function to format the expiration date
const formatExpirationDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Store user data and message
const eUser = ref(props.eUser || {});
const message = ref(props.message || '');

// Week related variables and methods
const currentWeekStart = ref(getSunday(new Date())); // Start on Sunday
const currentWeekEnd = ref(getSaturday(new Date())); // End on Saturday

// Function to get the Sunday of the week for a given date
function getSunday(date) {
    const day = date.getUTCDay();
    // Adjust to get Sunday
    date.setUTCDate(date.getUTCDate() - day);
    return new Date(date); // Return a new date
}

// Function to get the following Saturday for a given date (same week)
function getSaturday(sunday) {
    const saturday = new Date(sunday);
    saturday.setUTCDate(saturday.getUTCDate() + 6); // Move forward to Saturday
    return saturday; // Return a new date
}

// Method to calculate the week dates based on the current week start
const getWeekDates = (startDate) => {
    const weekDates = [];
    for (let i = 0; i < 7; i++) {
        const date = new Date(startDate);
        date.setUTCDate(startDate.getUTCDate() + i);
        weekDates.push(date.toISOString().split('T')[0]); // Format to YYYY-MM-DD
    }
    return weekDates;
};

// Get the current week dates
const weekDates = computed(() => getWeekDates(currentWeekStart.value));

// Method to navigate to the previous week
const goToPreviousWeek = () => {
    currentWeekStart.value = new Date(currentWeekStart.value);
    currentWeekStart.value.setUTCDate(currentWeekStart.value.getUTCDate() - 7);
    currentWeekEnd.value = getSaturday(currentWeekStart.value);
};

// Method to navigate to the next week
const goToNextWeek = () => {
    currentWeekStart.value = new Date(currentWeekStart.value);
    currentWeekStart.value.setUTCDate(currentWeekStart.value.getUTCDate() + 7);
    currentWeekEnd.value = getSaturday(currentWeekStart.value);
};

// Method to go back to today's week
const goBackToToday = () => {
    const today = new Date();
    currentWeekStart.value = getSunday(today); // Reset to this week's Sunday
    currentWeekEnd.value = getSaturday(currentWeekStart.value); // Reset end to this week's Saturday
};

// Get today's date for highlighting
const today = new Date().toISOString().split('T')[0];

const formatDate = (date) => {
    // Create date object in the New York timezone
    const options = {
        weekday: 'short', // Short weekday name (Sun, Mon, etc.)
        month: 'short',   // Short month name (Jan, Feb, etc.)
        day: '2-digit',   // Two-digit day
    };

    // Create Date object and format it
    return new Intl.DateTimeFormat('en-US', options).format(new Date(date + ' GMT-0500')); // GMT-0500 is Eastern Standard Time
};

onMounted(() => {
    if (message.value) {
        console.log(message.value); 
    }
    if (eUser.value) {
        console.log(eUser.value); 
    }
});

const getDayClass = (date) => {
    const day = date.getUTCDay();
    return day === 0 || day === 6 ? 'text-red-500' : 'text-gray-800'; // Tailwind color classes
};

const viewBooking = (booking) => {
    console.log("Booking details:", booking);
};

// Define a method to handle clicking the booking button
const handleBookingClick = (bookingId) => {
    // Use Inertia to send a GET request to the correct URL
    router.get(route('admin.viewBooking', { id: bookingId }), {
        onSuccess: (page) => {
            // Handle the success response, you can navigate or display a message
            console.log('Success:', page);
        },
        onError: (errors) => {
            // Handle any errors from the server
            console.error('Error:', errors);
        }
    });
}

// Update Payment Status
const updatePaymentStatus = async (id) => {
    try {
        // Send a POST request to update the payment status while preserving scroll
        await router.post('bookings/update_payment_status', { id }, { preserveScroll: true });
    } catch (error) {
        console.error('Error updating payment status:', error);
    }
};


</script>

<template>
    <Container>
        <Title>View Inspector :: {{ eUser.name }}
            <sup :class="{'text-sm': true, 'bg-green-400': eUser.adminApprove, 'bg-red-400': !eUser.adminApprove}">
    {{ eUser.adminApprove ? 'Active' : 'Suspended' }}
  </sup>
        </Title>
        
        <div v-if="message" class="alert alert-success">
            {{ message }}
        </div>

        <div class="md:w-full">
                <p class="mt-5"><strong>Lead Inspector Risk Assessor</strong></p>
                <hr>
                <p><strong>Name:</strong> {{ eUser.name }}</p>
                <p><strong>Email:</strong> {{ eUser.email }}</p>
                <p><strong>Mobile:</strong> {{ eUser.phone_number }}</p>
                <p><strong>License #: {{ eUser.license_number }}</strong></p>
                <hr>
                <p class="mt-5"><strong>Signature</strong></p>
                <!-- Inspector Signature -->
<!-- Inspector Signature -->
<img 
      :src="signatureUrl" 
      alt="Inspector Signature" 
      style="width: auto; height: auto; max-height: 3rem; object-fit: contain;">

    <!-- License Copy -->
    <p class="mt-4"><strong>License copy</strong></p>
    <img
      :src="eUser.license_copy ? licenseUrl : '/storage/license/default.jpg'"
      class="w-full h-auto object-cover object-center bg-slate-900 border-[2%] border-solid border-black p-[2%]"
      alt="License"
    />

                <hr>
                
            </div>

        <div v-if="eUser && Object.keys(eUser).length > 0" class="flex flex-col md:flex-row gap-6 mt-8">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                <p><strong>Activities Today:</strong> Show a list of all bookings for morning and afternoon.</p>
                
                <h2 v-if="props.discountCodes && props.discountCodes.length" class="mt-6 text-lg font-semibold">Discount Codes</h2>

                <table v-if="props.discountCodes && props.discountCodes.length" class="min-w-full mt-4 border border-gray-300">
                    <thead class="text-sm">
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border">Code</th>
                            <th class="px-4 py-2 border">Discount %</th>
                            <th class="px-4 py-2 border">Is Active</th>
                            <th class="px-4 py-2 border">Expiration Date</th>
                            <th class="px-4 py-2 border">Usage Limit</th>
                            <th class="px-4 py-2 border">Usage Count</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(discountCode, index) in props.discountCodes" 
                            :key="discountCode.code" 
                            :class="{ 'bg-green-100': discountCode.is_active, 'bg-red-100': !discountCode.is_active }">
                            <td class="px-4 py-2 border">{{ index + 1 }}</td>
                            <td class="px-4 py-2 border">{{ discountCode.code }}</td>
                            <td class="px-4 py-2 border">{{ discountCode.discount_percentage }}%</td>
                            <td class="px-4 py-2 border">{{ discountCode.is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-2 border">
                                {{ discountCode.expiration_date ? formatExpirationDate(discountCode.expiration_date) : 'N/A' }}
                            </td>
                            <td class="px-4 py-2 border">{{ discountCode.usage_limit || 'Unlimited' }}</td>
                            <td class="px-4 py-2 border">{{ discountCode.used_count }}</td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="text-gray-500 mt-4">No discount codes available.</p>                
                <!-- Responsive Grid for Activities -->
                
            </div>

        </div>
    </Container>

    <Container>
       <div v-if="eUser && Object.keys(eUser).length > 0" class="flex flex-col md:flex-row gap-6 mt-8">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                <h2 class="mt-6 text-lg font-semibold">Summary of Inspection</h2>
                
                <!-- Responsive Grid for Activities -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
                    <!-- Large cell spanning two rows -->
                    <span  class="text-gray-900 col-span-1 lg:col-span-1 row-span-2">
                        <div class="bg-orange-200 font-bold text-center p-4 shadow rounded h-full flex flex-col justify-center">
                            All to Date
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countInsBookings || '--' }}</p>
                            </button>
                        </div>
                    </span>

                    <!-- Remaining cells, adjusted to fit -->
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Inspected
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countIncCompleted || '--' }}</p>
                            </button>
                        </div>

                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Pending Ins.
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countIncPending || '--' }}</p>
                            </button>
                        </div>

                    <!-- Solid line before this column -->
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded border-l-8 border-gray-700">Re-Inspection  <!-- Added border-l-2 -->
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl text-indigo-500 ">{{ reInspectionBook || '--' }}</p>
                            </button>
                        </div>
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">In queue Ins
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ upComing || '--' }}</p>
                            </button>
                        </div>

                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Doc. Uploaded
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countIncRec || '--' }}</p>
                            </button>
                        </div>

                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded border-l-8 border-gray-700">Overdue
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-red-500 text-5xl">{{ overDue || '--' }}</p>
                            </button>
                        </div>
                </div>

                <h2 class="text-xl font-semibold mt-12">📅 Assigned Lead Inspection Overview - Weekly</h2>
                <!-- Show Calendar with Previous/Next buttons -->
                <div class="flex justify-between items-center mt-2">
                    <button @click="goToPreviousWeek" class="bg-gray-500 text-white text-xl px-4 py-2 rounded hover:bg-gray-600">Previous</button>
                    <button @click="goBackToToday" class="bg-green-500 text-white text-xl px-4 py-2 rounded hover:bg-green-600">Reset</button>
                    <button @click="goToNextWeek" class="bg-blue-500 text-white text-xl px-4 py-2 rounded hover:bg-blue-600">Next</button>
                </div>
                <div class="flex items-center mt-5">
                    <p class="flex items-center ml-1 mr-3">
                        <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-2"></span> Completed Inspection
                    </p>
                    <p class="flex items-center ml-1 mr-3">
                        <span class="w-2 h-2 bg-blue-500 rounded-full inline-block mr-2"></span> Pass Inspection
                    </p>
                    <p class="flex items-center ml-1 mr-3">
                        <span class="w-2 h-2 bg-red-500 rounded-full inline-block mr-2"></span> Fail Inspection
                    </p>
                </div>

                <table class="w-full mt-4 border border-gray-300 table-responsive">
                    <thead class="text-sm">
                        <tr class="bg-gray-200">
                            <th v-for="date in weekDates" :key="date" class="px-4 py-2 border border-gray-300"
                                :class="{ 'bg-blue-200': date === today, 'text-red-500': (new Date(date).getUTCDay() === 0 || new Date(date).getUTCDay() === 6) }">
                                {{ formatDate(date) }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr>
                            <td v-for="date in weekDates" :key="date" class="px-3 py-2 border border-gray-300 align-top" 
                                :class="{ 'text-red-500': (new Date(date).getUTCDay() === 0 || new Date(date).getUTCDay() === 6) }">
                                <ul v-if="props.bookingRequests[date]">
                                    <li v-for="booking in props.bookingRequests[date]" :key="booking.id" class="my-1">
                                        
                                        <button
                                            @click="handleBookingClick(booking.id)"
                                            class="w-full text-left p-1 border bg-gray-200 text-black text-xs rounded hover:bg-gray-400 transition"
                                        >
                                            <div v-if="booking.jobStatus === 'completed'" class="flex items-center mt-2"> <!-- Increased margin-top here -->
                                                <!-- Completed status indicator -->
                                                <sup v-if="booking.jobStatus === 'completed'" class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1 mr-1"></sup>

                                                <!-- Records pass/fail indicators with increased space between them -->
                                                <div v-if="props.records[booking.order_id] && props.records[booking.order_id].length" class="flex items-center space-x-1"> <!-- Increased space between circles -->
                                                    <template v-for="record in props.records[booking.order_id]" :key="record.id">
                                                        <sup
                                                            class="inline-block w-2 h-2 rounded-full"
                                                            :class="{
                                                                'bg-blue-500': record.pass_fail === 'pass',
                                                                'bg-red-500': record.pass_fail === 'fail',
                                                            }"
                                                            :title="`Unit ${record.unit_number}: ${record.pass_fail}`"
                                                        ></sup>
                                                    </template>
                                                </div>
                                            </div>

                                            <br>
                                            # {{ booking.order_id }} <br> <strong>{{ booking.methodology }}
                                            <span v-if="booking.includeXrf">+ XRF</span></strong> <hr>
                                            <span class="text-lg font-bold">•</span>{{ booking.selectedTime === 'morning' ? 'Morning' : 'Afternoon' }}
                                            <hr>
                                            <span>{{ booking.address }}, {{ booking.municipality }} - [{{ booking.county }} Cnty]</span><hr>
                                            <span>
                                                {{ booking.InspectionType === 'newInspection' ? 'New Inspection' : '' }}
                                                <span v-if="booking.InspectionType !== 'newInspection'" class="text-red-400">Re-Inspect</span>
                                                <span v-if="booking.InspectionType === 'reInspection'"><br>({{ booking.old_order_id }})</span><hr>
                                            </span>
                                            <span>Units # {{ booking.units }} </span>
                                        </button>

                                    </li>
                                </ul>
                                <span v-else class="text-gray-400">No Inspection</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Container>

    <Container>
        <div class="overflow-x-auto p-2">
                            <!-- Search Bar Goes Here -->
                            <div class="flex justify-end mb-2 mt-5">
                                <div class="w-2/8">
                                    <input 
                                        type="search" 
                                        placeholder="Search" 
                                        class="h-12 px-3 border border-gray-400 rounded" 
                                        v-model="search" />
                                </div>
                            </div>

                            <!-- Search and color code -->
                            <div class="overflow-x-auto">
                                <div class="flex items-center">
                            <div class="flex items-center mr-6">
                                <input class="w-2 h-4 bg-red-400 text-red-300 border-red-700" />
                                <label for="job-status" class="ml-2 text-black">Job =: <span class="text-black">Failed</span></label>
                            </div>

                            <div class="flex items-center mr-6">
                                <input id="payment-status" class="form-checkbox w-4 h-4 bg-indigo-200 text-indigo-100 border-indigo-700" />
                                <label for="payment-status" class="ml-2 text-black">Pay =: <span class="text-black">Pending</span></label>
                            </div>

                            <div class="flex items-center mr-6">
                                <input id="payment-success" class="form-checkbox w-4 h-4 bg-white text-white border-gray-700" />
                                <label for="payment-success" class="ml-2 text-black">Pay =: <span class="text-black">Succeeded</span></label>
                            </div>
                            <div class="flex items-center">
                                <input id="completed" class="form-checkbox w-4 h-4 bg-green-500 text-white border-gray-700 rounded-full" />
                                <label for="completed" class="ml-2 text-black">Completed & Documents Uploaded</label>
                            </div>

                            </div> 

                         <!-- Table Goes Here -->
                                <table class="min-w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th class="text-black text-center text-x border border-black">OrderID</th>
                                            <th colspan="2" class="text-black text-x text-center border border-black">Inspection Info</th>
                                            <th class="text-black text-center text-x border border-black">Address</th>
                                            <th class="text-black text-center text-x border border-black">Date</th>
                                            <th class="text-black text-center text-sm border border-black">Property Owner</th>
                                            <th class="text-black text-center text-x border border-black">Email</th>
                                            <th class="text-black text-center text-x border border-black">Agent</th>
                                            <th colspan="3" class="text-black text-x text-center font-bold border border-black">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- <tr v-for="eData in bookings.data" :key="eData.id"> -->
                                            
                                            <tr 
                                            v-for="eData in bookings.data" 
                                                :key="eData.id"
                                                :class="{
                                                    'bg-red-300': eData.jobStatus === 'failed', 
                                                    'bg-indigo-100': eData.payment_status === 'pending'
                                            }">
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.order_id }}</span><br>
                                                <span v-if="eData.old_order_id" class="text-[0.7rem] text-black-900">({{ eData.old_order_id }})</span>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900 font-bold">
                                                    {{ eData.methodology }}<span v-if="eData.includeXrf"><br> + XRF</span>
                                                </span><br>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.municipality }}</span><br>
                                                <span class="text-xs text-black-900">{{ eData.county }}</span>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs font-bold text-black-900">{{ eData.address }}</span><br>
                                                <span class="text-xs text-black-900">Zip: {{ eData.city }}</span>
                                            </td>
                                            <td class="text-black justify-center border border-black pl-2">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.selected_date }}</span><br>
                                                <span class="text-xs text-black-900 font-bold">{{ eData.selectedTime == 'morning' ? 'Morning' : 'Afternoon' }}</span>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.name }}</span><br>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900">{{ eData.email }}</span><br>
                                                <span class="text-xs text-black-900">Ph: {{ eData.phone }}</span>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900">{{ eData.assignTo }} 
                                                    <sup v-if="eData.jobStatus === 'completed'" class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></sup>
                                                </span>
                                            </td>
                                            <td 
                                                :class="[eData.payment_status === 'succeeded' ? 'bg-white' : 'bg-blue-300', 
                                                        'text-black', 'text-center', 'font-bold', 'text-xs', 'border', 'p-2', 'border-black']">
                                                <button 
                                                    @click="updatePaymentStatus(eData.id)"
                                                    class="text-black">
                                                    {{ eData.payment_status === 'succeeded' ? 'Set pending' : 'Set Paid' }}
                                                </button>
                                            </td>
                                            <td class="bg-orange-200 text-black text-center text-xs font-bold border p-2 border-black">
                                                <Link :href="route('admin.viewBooking', { id: eData.id })" 
                                                    as="button"
                                                    type="button"
                                                    class="text-black">
                                                    View &#129195;
                                                </Link>
                                            </td>

                                            <td class="bg-gray-100 text-red-500 text-center text-xs font-bold border p-2 border-black">
                                                <Link :href="route('admin.delete_application')" 
                                                    method="post"
                                                    :data="{ id: eData.id }"
                                                    as="button"
                                                    type="button"
                                                    class="text-red-500">
                                                    Cancel
                                                </Link>
                                            </td>
                                        </tr>
                                        <!-- Additional rows as needed -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Links-->
                            <div>
                                <PaginationLinks :paginator="bookings" />
                            </div>
                        </div>
    </Container>
</template>
