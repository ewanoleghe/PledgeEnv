<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the Inertia page props
const props = defineProps({
    discountCodes: Object,
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
        <Title>Discount Codes</Title>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                
                <h2 v-if="props.discountCodes && props.discountCodes.length" class="mt-6 text-lg font-semibold">Discount Codes</h2>
                <div>
                </div>
                <!-- Add New Code Button -->
                <!-- Add New Code Button as Router Link -->
    <div class="flex justify-end mt-2 mb-4">
        <Link :href="route('admin.code.create')" 
                                    as="button"
                                    type="button"
                                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                    + New 
                                </Link>
    </div>
    

                <table v-if="props.discountCodes && props.discountCodes.length" class="min-w-full mt-4 border border-gray-300">
                    <thead class="text-sm">
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border">Assign to</th>
                            <th class="px-4 py-2 border">Code</th>
                            <th class="px-4 py-2 border">Discount %</th>
                            <th class="px-4 py-2 border">Is Active</th>
                            <th class="px-4 py-2 border">Expiration Date</th>
                            <th class="px-4 py-2 border">Usage Limit</th>
                            <th class="px-4 py-2 border">Usage Count</th>
                            <th colspan="2" class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(discountCode, index) in props.discountCodes" 
                            :key="discountCode.code" 
                            :class="{ 'bg-green-100': discountCode.is_active, 'bg-red-100': !discountCode.is_active }">
                            <td class="px-4 py-2 border">{{ index + 1 }}</td>
                            <td class="px-4 py-2 border">{{ discountCode.inspector_name }}</td>
                            <td class="px-4 py-2 border">{{ discountCode.code }}</td>
                            <td class="px-4 py-2 border">{{ discountCode.discount_percentage }}%</td>
                            <td class="px-4 py-2 border">{{ discountCode.is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-2 border">
                                {{ discountCode.expiration_date ? formatExpirationDate(discountCode.expiration_date) : 'N/A' }}
                            </td>
                            <td class="px-4 py-2 border">{{ discountCode.usage_limit || 'Unlimited' }}</td>
                            <td class="px-4 py-2 border">{{ discountCode.used_count }}</td>
                            <td class="bg-orange-200 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.code.reset', { id: discountCode.id })" 
                                as="button"
                                    type="button"
                                    class="text-black">
                                    Reset &#129195;
                                </Link>
                            </td>
                            <td v-if="discountCode.is_active === 0" class="bg-blue-500 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.code.deactivate', { id: discountCode.id })" 
                                    as="button"
                                    type="button"
                                    class="text-white">
                                    Activate &#129195;
                                </Link>
                            </td>
                            <td v-if="discountCode.is_active === 1" class="bg-red-500 text-black text-center text-xs font-bold border p-2 border-black">
                                <Link :href="route('admin.code.deactivate', { id: discountCode.id })" 
                                    as="button"
                                    type="button"
                                    class="text-white">
                                    Cancel &#129195;
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