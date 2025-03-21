<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import { useForm } from '@inertiajs/vue3';
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";
import { defineProps } from 'vue';

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Access the Inertia page props (inspectors list)
const props = defineProps({
  inspectors: Array,  // List of inspectors passed from the backend
  message: String,    // Add the message prop
  errors: Object,     // Add errors prop
});

// Initialize the form data
const form = useForm({
  inspector_id: '',
  inspector_name: '',  // Will hold the selected inspector's name
  discount_percentage: '',
  code: '',
  expiration_date: '',
});

// Function to update form.inspector_name when an inspector is selected
const updateInspectorName = () => {
  // Find the selected inspector from the inspectors array based on the selected ID
  const selectedInspector = props.inspectors.find(inspector => inspector.id === form.inspector_id);
  
  // If an inspector is selected, update the form.inspector_name field with their name
  form.inspector_name = selectedInspector ? selectedInspector.name : '';
};

// Function to get inspector name by ID (optional, for displaying the selected inspector)
const getInspectorName = (inspectorId) => {
  const selectedInspector = props.inspectors.find(inspector => inspector.id === inspectorId);
  return selectedInspector ? selectedInspector.name : '';
};

</script>

<template>
    <Container>
        <h1 class="text-2xl font-bold text-center mb-4">Discount Codes</h1>

        <!-- Success Message -->
        <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
        </div>

        <div class="flex flex-col md:flex-row gap-6 items-center justify-center">
            <!-- Form Section -->
            <div class="md:w-full md:flex-1 flex justify-center">
                <div class="mb-4 w-full max-w-md">
                    <h2 class="text-xl font-semibold text-center mb-4">Create New Code</h2>

                    <!-- Form Submission -->
                    <form @submit.prevent="form.post(route('admin.newcode.create'), { preserveScroll: true })" class="space-y-4">

                        <!-- Inspector Dropdown -->
                        <div>
                            <label for="inspector" class="block font-medium">Inspector</label>
                            <div class="relative w-full">
    <i class="fas fa-users absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
    <select 
        v-model="form.inspector_id" 
        id="inspector" 
        class="mt-1 p-2 pl-10 block w-full rounded-md font-bold text-sm dark:text-slate-900 border-slate-300 outline-0 focus:ring-1 focus:ring-inset focus:ring-indigo-400 focus:border-indigo-400 placeholder:text-slate-400" 
        @change="updateInspectorName"
    >
        <option value="" disabled>Select Inspector</option>
        <option v-for="inspector in inspectors" :key="inspector.id" :value="inspector.id">
            {{ inspector.name }}
        </option>
    </select>
</div>

                            <p v-if="form.errors.inspector_id" class="text-red-500 text-sm mt-1">
                                {{ form.errors.inspector_id }}
                            </p>

                            <!-- Display the selected inspector's name -->
                            <p v-if="form.inspector_id">
                                Selected Inspector: {{ getInspectorName(form.inspector_id) }}
                            </p>
                        </div>

                        <!-- Discount Code Field -->
                        <div>
                            <InputField 
                                label="Discount Code" 
                                v-model="form.code" 
                                icon="user-secret"
                                class="w-full" 
                            />
                            <p v-if="form.errors.code" class="text-red-500 text-sm mt-1">
                                {{ form.errors.code }}
                            </p>
                        </div>

                        <!-- Discount Percentage Field -->
                        <div>
                            <InputField 
                                label="Discount %" 
                                v-model="form.discount_percentage" 
                                icon="percentage"
                                class="w-full" 
                            />
                            <p v-if="form.errors.discount_percentage" class="text-red-500 text-sm mt-1">
                                {{ form.errors.discount_percentage }}
                            </p>
                        </div>

                        <!-- Expiration Date Field -->
                        <div>
                            <InputField 
                                label="Expiration Date" 
                                type="date" 
                                v-model="form.expiration_date" 
                                icon="calendar"
                                class="w-full" 
                            />
                            <p v-if="form.errors.expiration_date" class="text-red-500 text-sm mt-1">
                                {{ form.errors.expiration_date }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <PrimaryBtn class="w-full">Create Discount Code</PrimaryBtn>
                    </form>
                </div>
            </div>
        </div>
    </Container>
</template>
