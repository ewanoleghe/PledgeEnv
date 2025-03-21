<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the Inertia page props
const props = defineProps({
    teamCode: Object,
    message: String,    // Add the message prop
});

</script>

<template>
    <Container>
        <Title>All New Inspectors Sign Up Code</Title>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Column 1: 70% -->
            <div class="md:w-full">
                
                <h2 v-if="props.teamCode && props.teamCode.length" class="mt-6 text-lg font-semibold">New Inspectors Registration Code</h2>
                <div>
                </div>
                 <!-- Add New Code Button as Router Link -->
    <div class="flex justify-end mt-2 mb-4">
        <Link :href="route('admin.regcode.create')" 
                                    as="button"
                                    type="button"
                                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                    + New Sign Up Code
                                </Link>
    </div>
                <!-- Display All teamCode -->
                <table v-if="props.teamCode && props.teamCode.length" class="min-w-full mt-4 border border-gray-300">
                    <thead class="text-sm">
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border text-xl font-bold">#</th>
                            <th class="px-4 py-2 border text-xl font-bold">Sign Up Code</th>
                            <th class="px-4 py-2 border text-xl font-bold">Code Name</th>
                            <th colspan="2" class="px-4 py-2 border text-xl font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(eData, index) in props.teamCode" 
                            :key="eData.code" 
                            >
                            <td class="px-4 py-2 border text-xl font-bold">{{ index + 1 }}</td>
                            <td class="px-4 py-2 border text-xl font-bold">{{ eData.team_code }}</td>
                            <td class="px-4 py-2 border text-xl font-bold">{{ eData.team_name }}</td>
                            <td class="bg-red-600 text-black text-center text-xl font-bold border p-2 border-black">
                                <Link :href="route('admin.service.kill_code', { id: eData.id })" 
                                method="post"
                                :data="{ id: eData.id }"
                                as="button"
                                    type="button"
                                    class="text-white">
                                    Delete &#129195;
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    <p v-else class="text-gray-500 mt-4">No teamCode available.</p>
                </div>
        </div>
    </Container>
</template>
