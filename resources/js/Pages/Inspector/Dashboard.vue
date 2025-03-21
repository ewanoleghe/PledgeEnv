<script setup>
import InspectorLayout from '@/Layouts/InspectorLayout.vue';
import { ref, onMounted, computed } from 'vue';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import TextLink from "../../Components/TextLink.vue";
import { router } from '@inertiajs/vue3';

// Access the APP_NAME environment variable directly
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name';

// Set the layout for this component
defineOptions({ layout: InspectorLayout });

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
});

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
    // Use Inertia to post data to your backend controller
    router.get('view_inspection', { id: bookingId }, {
        onSuccess: (page) => {
            // Handle the success response, you can navigate or display a message
            console.log('Success:', page);
        },
        onError: (errors) => {
            // Handle any errors from the server
            console.error('Error:', errors);
        }
    });
};

// Method to generate the download link
const getDownloadLink = (filename) => {
  return `/docs/${filename}`;
};
</script>

<template>
    <Container>
        <Title>Inspectors Dashboard</Title>
        <h2 class="mt-5 mb-5 font-bold text-2xl">Welcome, {{ eUser.name }}.</h2>

        <div v-if="message" class="alert alert-success">
            {{ message }}
        </div>

        <div v-if="eUser && Object.keys(eUser).length > 0" class="flex flex-col md:flex-row gap-6">
            <!-- Column 1: 70% -->
            <div class="md:w-9/12">
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

                <h2 class="mt-6 text-lg font-semibold">Summary of Inspection</h2>
                
                <!-- Responsive Grid for Activities -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
                    <!-- Large cell spanning two rows -->
                    <Link :href="route('booking.list')" class="text-gray-900 col-span-1 lg:col-span-1 row-span-2">
                        <div class="bg-orange-200 font-bold text-center p-4 shadow rounded h-full flex flex-col justify-center">
                            All to Date
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countInsBookings || '--' }}</p>
                            </button>
                        </div>
                    </Link>

                    <!-- Remaining cells, adjusted to fit -->
                    <Link :href="route('dashboard.inspected')" class="text-gray-900">
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Inspected
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countIncCompleted || '--' }}</p>
                            </button>
                        </div>
                    </Link>

                    <Link :href="route('dashboard.pending')" class="text-gray-900">
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Pending Ins.
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countIncPending || '--' }}</p>
                            </button>
                        </div>
                    </Link>

                    <!-- Solid line before this column -->
                    <Link :href="route('dashboard.reinspection')" class="text-gray-900">
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded border-l-8 border-gray-700">Re-Inspection  <!-- Added border-l-2 -->
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl text-indigo-500 ">{{ reInspectionBook || '--' }}</p>
                            </button>
                        </div>
                    </Link>
                    <Link :href="route('dashboard.queued')" class="text-gray-900">
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">In queue Ins
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ upComing || '--' }}</p>
                            </button>
                        </div>
                    </Link>

                    <Link :href="route('dashboard.uploaded')" class="text-gray-900">
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Doc. Uploaded
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-5xl">{{ countIncRec || '--' }}</p>
                            </button>
                        </div>
                    </Link>

                    <Link :href="route('dashboard.overdue')" class="text-gray-900">
                        <div class="bg-gray-100 font-bold text-center p-4 shadow rounded border-l-8 border-gray-700">Overdue
                            <button class="mt-0 mb-0 bg-gray-200 py-4 px-4 rounded w-full">
                                <p class="text-center text-red-500 text-5xl">{{ overDue || '--' }}</p>
                            </button>
                        </div>
                    </Link>
                </div>

                <h2 class="text-xl font-semibold mt-6">📅 Assigned Lead Inspection Overview - Weekly</h2>
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

            <!-- Column 2: 30% -->
            <div class="md:w-4/12">
                <p class="mt-5"><strong>Lead Inspector Risk Assessor</strong></p>
                <hr>
                <p><strong>Name:</strong> {{ eUser.name }}</p>
                <p><strong>Email:</strong> {{ eUser.email }}</p>
                <p><strong>Mobile:</strong> {{ eUser.phone_number }}</p>
                <p><strong>License #: {{ eUser.license_number }}</strong></p>
                <hr>
                <p class="mt-5"><strong>Signature</strong></p>
                <img
                    :src="eUser.signature ? `/file/signature/${eUser.signature}` : '/file/signature/default.jpg'"
                    class="w-full h-auto max-h-20 object-contain bg-slate-100 border-[2%] border-solid border-black p-[2%]"
                    alt="Signature"
                />

                <p class="mt-4"><strong>License copy</strong></p>
                <img
                    :src="eUser.license_copy ? `/file/license/${eUser.license_copy}` : '/storage/app/private/license/default.jpg'"
                    class="w-full h-auto object-cover object-center bg-slate-900 border-[2%] border-solid border-black p-[2%]"
                    alt="License"
                />
                <hr>
                <p class="break-words pt-5"><strong>Additional License Info:</strong> </p>
                <p class="break-words">
                    <strong>Agreement with {{ companyName }}</strong> Extra details about the agents/inspectors agreement.
                </p>
                <div class="mt-6"><strong>Downloads (Template)</strong> 
                    <ul>
                        <li><a href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" target="_blank" rel="noopener noreferrer">Tax Doc: <strong>W-9</strong></a></li>

                        <li>
                            <a 
                            :href="getDownloadLink('Contractor_Information_Form.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
                            <strong>Contractor Information Form</strong>
                            </a>
                        </li>
                        <li>
                            <a 
                            :href="getDownloadLink('Inspector_Contract_Policy.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
                            <strong>Inspector Contract Policy</strong>
                            </a>
                        </li>
                        <li>
                            <a 
                            :href="getDownloadLink('Floor__Plan.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
                            <strong>Floor Plan</strong>
                            </a>
                        </li>
                        <li>
                            <a 
                            :href="getDownloadLink('Chain__of__Custody.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
                            <strong>Chain-of-Custody</strong>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center justify-center space-y-4 mt-6">
            <TextLink href="#">Register as Inspector</TextLink>
            <TextLink href="#">Login as Inspector</TextLink>
        </div>
    </Container>
</template>
