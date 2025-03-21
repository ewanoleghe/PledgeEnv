<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the Inertia page props
const props = defineProps({
    services: Object,
    message: String,    // Add the message prop
});

</script>

<template>
    <Container>
        <Title>All Services Offered & Price</Title>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                
                <h2 v-if="props.services && props.services.length" class="mt-6 text-lg font-semibold">Services Offered</h2>
                <div>
                </div>
                 <!-- Add New Code Button as Router Link -->
    <div class="flex justify-end mt-2 mb-4">
        <Link :href="route('admin.service.create')" 
                                    as="button"
                                    type="button"
                                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                    + New Service
                                </Link>
    </div>
                <!-- Display All services -->
                <table v-if="props.services && props.services.length" class="min-w-full mt-4 border border-gray-300">
                    <thead class="text-sm">
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border">Service Name</th>
                            <th class="px-4 py-2 border">Price [NY]</th>
                            <th class="px-4 py-2 border">Price Visual</th>
                            <th class="px-4 py-2 border">Price Dust</th>
                            <th class="px-4 py-2 border">XRF</th>
                            <th class="px-4 py-2 border">Weekend Fee</th>
                            <th class="px-4 py-2 border">Max Booking</th>
                            <th colspan="2" class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(eData, index) in props.services" 
                            :key="eData.code" 
                            >
                            <td class="px-4 py-2 border">{{ index + 1 }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.service_name }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.price }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.price_visual }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.price_dust }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.xrf }}</td>
                                    <!-- Show count of discount codes -->
                                    <td class="px-4 py-2 border text-xs font-bold text-center">{{ eData.weekendFee }}</td>
                                    <!-- Show count of booking requests -->
                                    <td class="px-4 py-2 border text-xs font-bold text-center">{{ eData.bookMax }}</td>
                                    <td class="bg-orange-200 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.service.view', { id: eData.id })" 
                                as="button"
                                    type="button"
                                    class="text-black">
                                    View &#129195;
                                </Link>
                            </td>
                            <td class="bg-red-600 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.service.update', { id: eData.id })" 
                                    as="button"
                                    type="button"
                                    class="text-white">
                                    Update &#129195;
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    <p v-else class="text-gray-500 mt-4">No Services & Price available.</p>
                </div>
        </div>
    </Container>
</template>
