<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import PaginationLinks from '../../../Components/PaginationLinks.vue';  
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    bookings: Object,
    searchTerm: String,
    pageName: String,
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
    <Head title="- Select a payment method" />
    <Container class="w-full max-w-4xl mx-auto">
        <div class="mb-8 text-center w-full">
            <Title>All Lead Bookings: <span v-if="props.pageName" >{{ pageName }}</span></Title>
        </div>
        <hr class="border-t-2 border-black w-full"> <!-- Added w-full here -->

        <div class="flex flex-col md:flex-row justify-center space-x-0 md:space-x-4 h-full w-full"> <!-- Added w-full here -->
            <div class="flex flex-col md:flex-row justify-center space-x-0 md:space-x-4 h-full w-full"> <!-- Added w-full here -->
                <div class="w-full flex flex-col justify-between mb-4 md:mb-0">
                    <div class="p-1 flex-grow">
                        <h2 class="text-xl font-bold p-1">All Lead Booking <span v-if="props.pageName" >{{ pageName }}</span></h2>
                        <hr class="border-t-2 border-black p-1 w-full"> <!-- Added w-full here to match the hr width -->

                        <!-- Form Goes Here -->

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

                            <!-- FLASH MESSAGE -->
                            <div class="p-4 flex-grow">
                                <p v-if="$page.props.flash.message" class="p-4 bg-green-200">{{ $page.props.flash.message }} </p>
                                <p v-if="$page.props.flash.message_body" class="p-4 bg-green-200">  {{ $page.props.flash.message_body }}  </p>
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

                    </div>
                </div>
            </div>
        </div>
    </Container>
</template>
