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
    inspectors: Array,  // Add inspectors array to props
});

// Form data with proper initialization
const form = ref({
    bookings: {
        selected_date: props.bookings?.selected_date || '',
        selectedTime: props.bookings?.selectedTime || '',
        address: props.bookings?.address || '',
        name: props.bookings?.name || '', // Change this
        email: props.bookings?.email || '',
        phone: props.bookings?.phone || '',
        assignTo: props.bookings?.assignTo || '',
        optionalMessage: props.bookings?.optionalMessage || '',
    },
    errors: {}
});

// Function to update booking
const updateBooking = async () => {
    try {
        // Send a PATCH request using Inertia.js router
        await router.patch(route('admin.update_postBooking', { id: props.bookings.id }), form.value.bookings, {
            preserveScroll: true,
            onError: (errors) => {
                form.value.errors = errors; // Update form errors
            }
        });
        console.log('Booking updated successfully');
    } catch (error) {
        console.error('Error updating booking:', error);
    }
};
</script>

<template>
    <Head title="Lead Bookings" />
    <Container class="w-full max-w-4xl mx-auto">
        <div class="mb-8 text-center w-full">
            <Title>Lead Bookings - {{ bookings.name }}</Title>
            <p class="text-4xl font-bold">
                <span v-if="bookings.payment_status === 'pending'" class="text-red-500">This booking has pending payment</span>
                <span v-else>Paid</span>
            </p>
        </div>

        <hr class="border-t-2 border-black w-full mb-4">

        <div class="flex flex-col md:flex-row justify-center space-x-4 w-full">
            <div class="w-full">
                <h2 class="text-xl font-bold p-1">{{ bookings.name }} - Lead-based Paint Inspection</h2>
                <hr class="border-t-2 border-black mb-2">

                <!-- FLASH MESSAGE -->
                <div class="p-4">
                    <p v-if="$page.props.flash.message" class="p-4 bg-green-200">{{ $page.props.flash.message }}</p>
                    <p v-if="$page.props.flash.message_body" class="p-4 bg-green-200">{{ $page.props.flash.message_body }}</p>
                </div>

                <hr class="border-t-2 border-black w-full mb-4">

                <!-- Form to update the booking -->
                <form @submit.prevent="updateBooking" class="space-y-4">
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
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Date</td>
                                <td class="border px-4 py-2">
                                    <input type="date" v-model="form.bookings.selected_date" class="mt-1 p-2 w-full border rounded-md" required />
                                </td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Time Slot</td>
                                <td class="border px-4 py-2">
                                    <select v-model="form.bookings.selectedTime" class="mt-1 p-2 w-full border rounded-md">
                                        <option value="morning">Morning</option>
                                        <option value="afternoon">Afternoon</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Street</td>
                                <td class="border px-4 py-2">
                                    <input type="text" v-model="form.bookings.address" placeholder="Enter your street address"
                                        class="mt-1 p-2 w-full border rounded-md" required />
                                    <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">City</td>
                                <td class="border px-4 py-2">{{ bookings.municipality }}, {{ bookings.city }}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">County</td>
                                <td class="border px-4 py-2">{{ bookings.county }} County</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Service Type</td>
                                <td class="border px-4 py-2">{{ bookings.service }} - *{{ bookings.methodology }}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Payment Method</td>
                                <td class="border px-4 py-2">
                                    {{ bookings.selectedPaymentMethod === 'MANUAL' ? 'Check - Invoice Generated' : 'Electronic - Receipt' }}
                                </td>
                            </tr>
                            <tr v-if="bookings.weekendFee">
                                <td class="border px-4 py-2 text-right font-bold">Total Weekend Fee</td>
                                <td class="border px-4 py-2">${{ bookings.totalWeekendFee }}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Invoice Total</td>
                                <td class="border px-4 py-2">${{ bookings.totalPay }}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Customer Name</td>
                                <td class="border px-4 py-2">
                                    <input type="text" v-model="form.bookings.name" placeholder="Enter customer name"
                                        class="mt-1 p-2 w-full border rounded-md" required />
                                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Email</td>
                                <td class="border px-4 py-2">
                                    <input type="email" v-model="form.bookings.email" placeholder="Enter email"
                                        class="mt-1 p-2 w-full border rounded-md" required />
                                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                                </td>
                            </tr>
                            <tr>
    <td class="border px-4 py-2 text-right font-bold">Phone</td>
    <td class="border px-4 py-2">
        <input type="text" v-model="form.bookings.phone" placeholder="Enter phone number"
            class="mt-1 p-2 w-full border rounded-md" required />
        <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
    </td>
</tr>
                            <tr>
                                <td class="border px-4 py-2 text-right font-bold">Assign To</td>
                                <td class="border px-4 py-2">
                                    <select v-model="form.bookings.assignTo" class="mt-1 p-2 w-full border rounded-md">
                                        <option value="" disabled>Select an Inspector</option>
                                        <option 
                                            v-for="inspector in props.inspectors" 
                                            :key="inspector.name" 
                                            :value="inspector.name">
                                            {{ inspector.name }}
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 text-right">Customer's Message</td>
                                <td class="border px-4 py-2">
                                    <textarea v-model="form.bookings.optionalMessage" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter an optional message"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-5">
                        <button 
                            v-if="bookings.jobStatus !== 'succeeded' && bookings.jobStatus !== 'fail'"
                            type="submit"
                            class="bg-blue-500 text-white text-xl px-4 py-2 rounded hover:bg-blue-600">
                            UPDATE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Container>
</template>
