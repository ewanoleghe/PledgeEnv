<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the Inertia page props
const props = defineProps({
    service: Object,
    message: String,    // Add the message prop
});

</script>

<template>
    <Container>
        <Title>{{ service.service_name }}</Title>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                
                <h2 class="mt-6 text-lg font-semibold">service Offered</h2>
                <div>
                </div>
                 <!-- Add New Code Button as Router Link -->
     <!-- Button Container -->
     <div class="flex justify-between mt-2 mb-4">
                    <!-- Back Button -->
                    <Link :href="route('admin.service.index')" 
                        as="button"
                        type="button"
                        class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-600">
                        Back
                    </Link>

                    <!-- Update Service Button -->
                    <Link :href="route('admin.service.update', { id: props.service.id })" 
                        as="button"
                        type="button"
                        class="px-4 py-2 text-white bg-red-500 rounded hover:bg-blue-700">
                        + Update Service
                    </Link>
                </div>
                <!-- Display All service -->
                <table class="min-w-full mt-4 border border-gray-300">
                    <thead class="text-sm">
                        <tr class="bg-gray-200">
                            <th colspan="2" class="px-4 py-2 border text-xl font-bold text-center">Service Info</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Service Type</td>
                            <td class="px-4 py-2 border text-lg font-bold">{{ service.service_name }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Price (NY)</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.price }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Visual Inspection</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.price_visual }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Dust Wipe Inspection</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.price_dust }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">XRF</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.xrf }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">XRF Re-Inspection</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.xrf_reinspection_price }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Visual  Re-Inspection</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.visual_reinspection_price }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Dust Wipe  Re-Inspection</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.dust_wipe_reinspection_price }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Weekend Charge</td>
                            <td class="px-4 py-2 border text-lg font-bold">${{ service.weekendFee }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Maximum Daily Booking </td>
                            <td class="px-4 py-2 border text-lg font-bold">{{ service.bookMax }}</td>
                        </tr>
                        <tr >
                            <td class="px-4 py-2 border text-lg font-bold">Service Descriptipn </td>
                            <td class="px-4 py-2 border text-lg font-bold">{{ service.description }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
        </div>
    </Container>
</template>
