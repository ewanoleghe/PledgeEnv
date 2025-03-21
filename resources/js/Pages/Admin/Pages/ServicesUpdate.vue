<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import { useForm } from '@inertiajs/vue3';
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the Inertia page props
const props = defineProps({
    service: Object,
    message: String,    // Add the message prop
});

// Initialize the form data
const form = useForm({
    service_name: props.service.service_name,
    price: props.service.price,
    price_visual: props.service.price_visual,
    price_dust: props.service.price_dust,
    xrf: props.service.xrf,
    xrf_reinspection_price: props.service.xrf_reinspection_price,
    visual_reinspection_price: props.service.visual_reinspection_price,
    dust_wipe_reinspection_price: props.service.dust_wipe_reinspection_price,
    weekendFee: props.service.weekendFee,
    bookMax: props.service.bookMax,
    description: props.service.description,
});
</script>

<template>
    <Container>
        <Title>Update {{ service.service_name }}</Title>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                <h2 class="mt-6 text-lg font-semibold">Service Offered</h2>

                <!-- Success Message -->
        <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
        </div>
        
                <!-- Button Container -->
                <div class="flex justify-between mt-2 mb-4">
                    <!-- Back Button -->
                    <Link :href="route('admin.service.index')" 
                        as="button"
                        type="button"
                        class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-600">
                        Back
                    </Link>
                </div>

                <!-- Form -->
                <form @submit.prevent="form.patch(route('admin.post.update', { id: props.service.id }), { preserveScroll: true })" class="space-y-4">
                    <!-- Display All service -->
                    <table class="min-w-full mt-4 border border-gray-300">
                        <thead class="text-sm">
                            <tr class="bg-gray-200">
                                <th colspan="2" class="px-4 py-2 border text-xl font-bold text-center">Service Info</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Service Type</td>
                                <td class="px-4 py-2 border text-lg font-bold">{{ service.service_name }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Price (NY)</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="Price" 
                                        v-model="form.price" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Visual Inspection</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="Visual Inspection Price" 
                                        v-model="form.price_visual" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Dust Wipe Inspection</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="Dust Wipe Price" 
                                        v-model="form.price_dust" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">XRF</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="XRF Price" 
                                        v-model="form.xrf" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">XRF Re-Inspection</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="XRF Re-Inspection Price" 
                                        v-model="form.xrf_reinspection_price" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Visual Re-Inspection</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="Visual Re-Inspection Price" 
                                        v-model="form.visual_reinspection_price" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Dust Wipe Re-Inspection</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="Dust Wipe Re-Inspection Price" 
                                        v-model="form.dust_wipe_reinspection_price" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Weekend Charge</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    $<InputField 
                                        label="Weekend Charge" 
                                        v-model="form.weekendFee" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Maximum Daily Booking</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    <InputField 
                                        label="Maximum Daily Booking" 
                                        v-model="form.bookMax" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border text-lg font-bold">Service Description</td>
                                <td class="px-4 py-2 border text-lg font-bold">
                                    <InputField 
                                        label="Service Description" 
                                        v-model="form.description" 
                                        class="w-full" 
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Update Button -->
                    <div class="flex justify-end mt-6">
                        <PrimaryBtn type="submit">Update Service</PrimaryBtn>
                    </div>
                </form>
            </div>
        </div>
    </Container>
</template>
