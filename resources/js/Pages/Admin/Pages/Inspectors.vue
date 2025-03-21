<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the Inertia page props
const props = defineProps({
    inspectors: Object,
    message: String,    // Add the message prop
});

// Function to format the expiration date
const formatExpirationDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <Container>
        <Title>All Registered Inspectors/Agents</Title>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                
                <h2 v-if="props.inspectors && props.inspectors.length" class="mt-6 text-lg font-semibold">Inspectors/Agents</h2>
                <div>
                </div>
                <!-- Display All Inspectors -->
                <table v-if="props.inspectors && props.inspectors.length" class="min-w-full mt-4 border border-gray-300">
                    <thead class="text-sm">
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border">Name</th>
                            <th class="px-4 py-2 border">Email</th>
                            <th class="px-4 py-2 border">Phone #</th>
                            <th class="px-4 py-2 border">Permit #</th>
                            <th class="px-4 py-2 border">License</th>
                            <th class="px-4 py-2 border">Dis. Codes</th>
                            <th class="px-4 py-2 border">Jobs</th>
                            <th colspan="2" class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(eData, index) in props.inspectors" 
                            :key="eData.code" 
                            :class="{ 'bg-green-100': eData.adminApprove, 'bg-red-100': !eData.adminApprove }">
                            <td class="px-4 py-2 border">{{ index + 1 }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.name }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.email }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.phone_number }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">{{ eData.license_number }}</td>
                            <td class="px-4 py-2 border text-xs font-bold">
                                {{ eData.license_copy ? 'Uploaded' : 'Pending' }}
                            </td>
                                    <!-- Show count of discount codes -->
                                    <td class="px-4 py-2 border text-xs font-bold text-center">{{ eData.discount_codes_count }}</td>
                                    <!-- Show count of booking requests -->
                                    <td class="px-4 py-2 border text-xs font-bold text-center">{{ eData.booking_requests_count }}</td>
                                    <td class="bg-orange-200 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.inspector.view', { id: eData.id })" 
                                as="button"
                                    type="button"
                                    class="text-black">
                                    View &#129195;
                                </Link>
                            </td>
                            <td v-if="eData.adminApprove === 0" class="bg-blue-500 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.inspector.deactivate', { id: eData.id })" 
                                    as="button"
                                    type="button"
                                    class="text-white">
                                    Activate &#129195;
                                </Link>
                            </td>
                            <td v-if="eData.adminApprove === 1" class="bg-red-500 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.inspector.deactivate', { id: eData.id })" 
                                    as="button"
                                    type="button"
                                    class="text-white">
                                    Suspend &#129195;
                                </Link>
                            </td>
                            

                            
                        </tr>
                    </tbody>
                </table>
                    <p v-else class="text-gray-500 mt-4">No discount codes available.</p>
                </div>
        </div>
    </Container>
</template>