<!-- resources/js/Pages/Admin/Pages/Dashboard.vue -->
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import PaginationLinks from '../../../Components/PaginationLinks.vue';  
import { ref, watch } from 'vue';
import { useForm } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    bookings: Object,
    bookingRequests: Object,
    searchTerm: String,
    pageName: String,
});

const form = useForm({
  id: props.bookings.id, // assuming 'bookings.id' is available
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

// Re-Send the Report
const sendReport = async (id) => {
    try {
        // Send a POST request to update the payment status while preserving scroll
        await router.post('admin.send_report_status', { id }, { preserveScroll: true });
    } catch (error) {
        console.error('Error Sending Report:', error);
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
                        <h2 class="text-xl font-bold p-1">All Lead Booking: <span v-if="props.pageName" >{{ pageName }}</span></h2>
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
                                <input id="completed" class="form-checkbox w-4 h-4 bg-blue-500 text-white border-gray-700 rounded-full" />
                                <label for="completed" class="ml-2 text-black">Completed & Documents Uploaded</label>
                            </div>

                            </div> 

                         <!-- Table Goes Here -->
                                <table class="min-w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th class="text-black text-center text-x border border-black">OrderID</th>
                                            <th class="text-black text-x text-center border border-black">Certificate #</th>
                                            <th class="text-black text-center text-x border border-black">Address</th>
                                            <th class="text-black text-center text-sm border border-black">Property Owner</th>
                                            <th class="text-black text-center text-x border border-black">Date Issued</th>
                                            <th class="text-black text-center text-x border border-black">Inspector</th>
                                            <th class="text-black text-center text-x border border-black">Admin Rev</th>
                                            <th class="text-black text-center text-x border border-black">Report</th>
                                            <th colspan="3" class="text-black text-x text-center font-bold border border-black">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- <tr v-for="eData in bookings.data" :key="eData.id"> -->
                                            
                                            <tr 
                                            v-for="eData in bookings.data" 
                                                :key="eData.id"
                                                :class="{
                                                    'bg-red-300': eData.pass_fail === 'fail', 
                                                    'bg-indigo-100': bookingRequests.find(b => b.order_id === eData.order_id)?.payment_status === 'pending'
                                            }">
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.order_id }}</span><br>
                                                <span v-if="eData.old_order_id" class="text-[0.7rem] text-black-900">({{ eData.old_order_id }})</span>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span v-if="eData.pass_fail === 'pass'" class="text-x text-black-900 font-bold">
                                                    {{ eData.certificate_number }}
                                                </span><br>
                                                <span class="text-xs text-black-900 font-bold">
                                                    {{ eData.methodology }}<span v-if="eData.includeXrf"><br> + XRF</span>
                                                </span><br>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.address }},</span><br>
                                                <span class="text-xs text-black-900 font-bold">{{ eData.municipality }}, {{ eData.city }}.</span><br>
                                                <span class="text-xs text-black-900">{{ eData.county }} County</span>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs font-bold text-black-900">{{ eData.property_owner_name }}</span><br>
                                                <span class="text-xs font-bold text-black-900">{{ eData.property_owner_phone }}</span><br>
                                                <span class="text-xs text-black-900">{{ eData.property_owner_email }}</span>
                                            </td>
                                            <td class="text-black justify-center border border-black pl-2">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.date_issued || '--' }}</span><br>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900 font-bold">{{ eData.inspector_name }}</span><br>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900">
                                                    {{ eData.admin_review === 'pending' ? 'Pending' : 'Complete' }}
                                                </span><br>
                                            </td>
                                            <td class="text-black text-center border border-black">
                                                <span class="text-xs text-black-900">
                                                    {{ eData.report_status === 'pending' ? 'Pending' : 'Report Sent' }}
                                                    <sup v-if="eData.pass_fail === 'pass'" class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-1"></sup>
                                                </span>
                                            </td>
                                            <td v-if="eData.date_issued !== 'NULL' && eData.admin_review === 'completed' && bookingRequests.find(b => b.order_id ===    eData.order_id)?.payment_status !== 'pending'"
                                                    :class="[eData.payment_status === 'succeeded' ? 'bg-white' : 'bg-blue-300', 
                                                            'text-black', 'text-center', 'font-bold', 'text-xs', 'border', 'p-2', 'border-black']">
                                                <button 
                                                    @click="sendReport(eData.id)"
                                                    class="text-black">
                                                    Re-Send <br>Report
                                                </button>
                                            </td>
                                            <td class="bg-orange-200 text-black text-center text-xs font-bold border p-2 border-black">
                                                <Link :href="route('admin.viewRecord', { id: eData.id })"
                                                    as="button"
                                                    type="button"
                                                    class="text-black">
                                                    View &#129195;
                                                </Link>
                                            </td>

                                            <td class="bg-gray-100 text-red-500 text-center text-xs font-bold border p-2 border-black">
                                                <Link :href="route('admin.report.submit', { id: eData.id, action: 'reset' })"
                                                    as="button"
                                                    method="post"
                                                    type="button"
                                                    class="text-red-500">
                                                    Cancel<br>or<br>Reset
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
