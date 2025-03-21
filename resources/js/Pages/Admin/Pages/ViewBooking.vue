<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';


// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    bookings: Object,
    siteContactInfo: Array,
    message: String,    // Add the message prop
});

// Update Payment Status
const updatePaymentStatusBookingView = async (id) => {
    try {
        // Send a POST request to update the payment status while preserving scroll
        await router.post(route('admin.bookings.update_payment_status'), { id }, { preserveScroll: true });
        // Optionally handle UI changes here, like success messages
        console.log('Payment status updated');
    } catch (error) {
        console.error('Error updating payment status:', error);
        // You can show a user-friendly error message if needed
    }
};


</script>

<template>
    <Head title="- Lead Bookings " />
    <Container class="w-full max-w-4xl mx-auto">
        <div class="mb-8 text-center w-full">
            <Title>Lead Bookings - {{ bookings.name }}</Title>
            <p class="text-4xl font-bold">
            <span v-if="bookings.payment_status === 'pending'" class="text-red-500">This booking has pending Payment</span>
            <span v-else>Paid</span>
            </p>
        </div>
        <hr class="border-t-2 border-black w-full"> <!-- Added w-full here -->

        <div class="flex flex-col md:flex-row justify-center space-x-0 md:space-x-4 h-full w-full"> <!-- Added w-full here -->
            <div class="flex flex-col md:flex-row justify-center space-x-0 md:space-x-4 h-full w-full"> <!-- Added w-full here -->
                <div class="w-full flex flex-col justify-between mb-4 md:mb-0">
                    <div class="p-1 flex-grow">
                        <h2 class="text-xl font-bold p-1">{{ bookings.name }} - Lead-based Paint Inspection</h2>
                        <hr class="border-t-2 border-black p-1 w-full"> <!-- Added w-full here to match the hr width -->

                        <!-- Form Goes Here -->

                        <div class="overflow-x-auto p-2">
                            
                            <!-- FLASH MESSAGE -->
                            <div class="p-4 flex-grow">
                                <p v-if="$page.props.flash.message" class="p-4 bg-green-200"> {{$page.props.flash.message}} </p>
                            </div>

                            <!-- Two-column layout -->
                            <div class="pb-5 flex">
                                <!-- Left Column -->
                                <div class="w-1/2 pr-4"> <!-- Add padding to separate columns -->
                                    <p class="text-3xl font-bold">Inspection Info:</p>
                                    <p class="text-2xl font-bold">Method: {{ bookings.methodology }}</p>
                                    <p v-if="bookings.includeXrf" class="text-2xl font-bold text-green-500">XRF: {{ bookings.includeXrf ? 'Yes' : 'No' }}</p>
                                    <p class="text-2xl font-bold">No of Units: {{ bookings.units }}</p>
                                    <p class="text-2xl font-bold">Type: {{ bookings.InspectionType == 'newInspection' ? 'New Inspection' : 'Re-Inspection' }}</p>
                                    <p class="text-xl">
                                        <strong>Street #: </strong>
                                        <a 
                                            target="_blank" 
                                            :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(bookings.address)}, ${encodeURIComponent(bookings.municipality)}, ${encodeURIComponent(bookings.city)}${bookings.state ? ', ' + encodeURIComponent(bookings.state) : ''}${bookings.zipCode ? ', ' + encodeURIComponent(bookings.zipCode) : ''}`">
                                            {{ bookings.address }}, <br>
                                            {{ bookings.municipality }}, {{ bookings.city }}<br>
                                            {{ bookings.county }} County
                                        </a>
                                    </p>
                                </div>

                                <!-- Right Column -->
                                <div class="w-1/2 pl-4"> <!-- Add padding to separate columns -->
                                    <div v-if="bookings.payment_status === 'pending'" class="mt-4"><p class="text-3xl font-bold">Payment Pending</p>
                                        <p class="text-lg">The payment for this booking is currently pending. Please ensure that the payment is received and available. Once the payment has been processed, set the payment to paid.</p>
                                        <button 
                                            v-if="bookings.payment_status === 'pending'"
                                            @click="updatePaymentStatusBookingView(bookings.id)"
                                            class="bg-red-500 text-white text-xl px-4 py-2 rounded hover:bg-green-600">
                                            Set Paid
                                        </button>
                                    </div>

                                                <div class="mt-4">
                                                    <div class="text-gray-500 text-3xl font-bold pt-4">
                                                        Job Status: 
                                                        <!-- If the job status is 'completed' -->
                                                        <span v-if="bookings.jobStatus === 'completed'" class="inline-block w-8 h-8 bg-green-500 rounded-full ml-1"></span>
                                                        <sup v-if="bookings.jobStatus === 'completed'" class="text-4xl text-green-500 pl-2">Completed</sup>
                                                        
                                                        <!-- If the job status is 'failed' -->
                                                        <span v-if="bookings.jobStatus === 'failed'" class="inline-block w-8 h-8 bg-red-500 rounded-full ml-1"></span>
                                                        <span v-if="bookings.jobStatus === 'failed'" class="text-4xl text-red-500 pl-2">Failed</span>
                                                        
                                                        <!-- If the job status is 'assigned' or 'ongoing' -->
                                                        <span v-if="bookings.jobStatus === 'assigned' || bookings.jobStatus === 'ongoing'" class="text-3xl text-black pl-2">Assign & Ongoing</span>
                                                        
                                                        <!-- Else, show 'Assign to an Inspector' -->
                                                        <span v-if="bookings.jobStatus == null" class="text-3xl text-black pl-2">Pending</span>    </div>
                                                </div>
                                </div>
                
                            </div>
                            </div> 

                            <hr class="border-t-2 border-gray-200 w-full"> <!-- Added w-full here -->
                            <!-- SITE Contact Goes Here -->
                         <div>
                            <!-- <h2>Payment Status: {{ bookings.payment_status }}</h2> -->
                            <p class="text-xl font-bold p-3">Unit Contacts</p>

                            <!-- Check if 'useSameContactForAll' is true -->
                            <div v-if="bookings.useSameContactForAll">
                                <h3>Site Contact Information</h3>
                                <p>Name: {{ bookings.siteContact }}</p>
                                <p>Email: {{ bookings.siteContactEmail }}</p>
                                <p>Phone: {{ bookings.siteContactPhone }}</p>
                            </div>
                            <div v-else>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div v-for="(contact, index) in siteContactInfo" :key="index" class="border p-4 rounded">
                                        <p>Street # {{ bookings.address }}</p>
                                        <h4>Unit Number: {{ contact.unit_number }}</h4>
                                        <p>Name: {{ contact.site_contact }}</p>
                                        <p>Email: {{ contact.site_contact_email }}</p>
                                        <p>Phone: {{ contact.site_contact_phone }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="border-t-2 border-black w-full mb-5 mt-2"> <!-- Added w-full here -->

                         <!-- Table Goes Here -->
                         <table class="min-w-full bg-white border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-200">
                                            <th class="px-4 py-2 text-left text-3xl" colspan="2">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        Order ID: {{ bookings.order_id }}
                                                        <span v-if="bookings.jobStatus === 'completed'" class="inline-block w-5 h-5 bg-green-500 rounded-full ml-1"></span> 
                                                        <sup v-if="bookings.jobStatus === 'completed'" class="text-sm text-red-500 pl-2">Completed</sup>
                                                        <br>
                                                        <span v-if="bookings.old_order_id" class="text-xl text-black-900">[ {{ bookings.old_order_id }} ]</span>
                                                    </div>
                                                    <Link v-if="bookings.jobStatus !== 'succeeded' && bookings.jobStatus !== 'fail'"
                                                        :href="route('admin.bookings.get_update', { id: props.bookings.id })" 
                                                        as="button"
                                                        type="button"
                                                        class="px-4 py-2 text-white bg-red-500 rounded hover:bg-blue-700">
                                                        + Update Booking
                                                    </Link>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Date</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.selected_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Time Slot</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.selectedTime == 'morning' ? 'Morning' : 'Afternoon' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Street</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.address }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">City</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.municipality }}, {{ bookings.city }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">County</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.county }} County</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Customer Details</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.name }}
                                                <br>
                                                {{ bookings.email }}
                                                <br>
                                                {{ bookings.phone }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Service type</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.service }} - *{{ bookings.methodology }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold"></td>
                                            <td class="border px-4 py-2 font-bold"></td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Payment Method</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.selectedPaymentMethod == 'MANUAL' ? 'Check - Invoice Generated' : 'Electronic -Receipt'  }}</td>
                                        </tr>
                                        <tr v-if="bookings.weekendFee">
                                            <td class="border px-4 py-2 text-right font-bold">Total Weekend Fee</td>
                                            <td class="border px-4 py-2 font-bold">${{ bookings.totalWeekendFee }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Subtotal</td>
                                            <td class="border px-4 py-2 font-bold">${{ bookings.NewSubTotal }}
                                                <span v-if="bookings.discountCode" class="text-xl text-black-900">
                                                    <sup>({{ bookings.discountPercentage }}%)</sup></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Invoice Total</td>
                                            <td class="border px-4 py-2 font-bold">${{ bookings.totalPay }}
                                            </td>
                                        </tr>
                                        <tr v-if="bookings.discountCode">
                                            <td class="border px-4 py-2 text-right font-bold">Discount Code Applied</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.discountCode }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right font-bold">Asign To</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.assignTo }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-4 py-2 text-right ">Customers Message*</td>
                                            <td class="border px-4 py-2 font-bold">{{ bookings.optionalMessage }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="pt-4 mt-5 pb-4 flex">
                                    <p class="text-3xl font-bold">Customers Info</p>
                                </div>
                                <table class="min-w-[70%] bg-white border border-gray-300 max-w-sm">
                                    <thead>
                                        <tr>
                                            <td class="border px-2 py-2 text-left font-bold">Property Owner</td>
                                            <td class="border px-2 py-2 font-bold">{{ bookings.name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border px-2 py-2 text-left font-bold">Property Owner Phone</td>
                                            <td class="border px-2 py-2 font-bold">{{ bookings.phone }}</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border px-2 py-2 text-left font-bold">Property Owner Email</td>
                                            <td class="border px-2 py-2 font-bold">{{ bookings.email }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>
                </div>
    </Container>
</template>
