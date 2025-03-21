<script setup>
import { ref, watch, computed } from 'vue';  // Add this import
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import { useForm } from '@inertiajs/vue3';
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

// Initialize the form data
const form = useForm({
  team_code: '',
  team_name: '',
});

// Error handling
const errors = ref({}); // Stores validation errors from the backend
const message = ref(''); // Stores success message (if any)

// Watch for changes in form errors
watch(() => form.errors, (newErrors) => {
  errors.value = newErrors;
}, { immediate: true });

// Method to submit the form
const submitForm = () => {
  form.post(route('admin.regcode.store'), {
    preserveScroll: true,
    onSuccess: () => {
      message.value = 'Service created successfully';
    },
    onError: (errors) => {
      errors.value = errors;
    }
  });
};
</script>

<template>
    <Container>
      <Title>Create New Inspector Sign-up Code</Title>
  
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Column 1: 70% -->
        <div class="md:w-full">
          <h2 class="mt-6 text-lg font-semibold">New Inspector Sign-up Code Creation</h2>
  
          <!-- Success Message -->
          <div v-if="message" class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ message }}
          </div>
  
          <!-- Error Messages -->
          <div v-if="errors && Object.keys(errors).length" class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>
  
          <!-- Button Container -->
          <div class="flex justify-between mt-2 mb-4">
            <!-- Back Button -->
            <Link :href="route('admin.service.sign_code')" 
              as="button"
              type="button"
              class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-600">
              Back
            </Link>
          </div>
  
          <!-- Form -->
          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Display All service -->
            <table class="min-w-full mt-4 border border-gray-300">
              <thead class="text-sm">
                <tr class="bg-gray-200">
                  <th colspan="2" class="px-4 py-2 border text-xl font-bold text-center">Service Info</th>
                </tr>
              </thead>
              <tbody class="text-sm">
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Sign Up Code</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    <InputField 
                      v-model="form.team_code" 
                      class="w-full" 
                    />
                    <!-- Validation Error for team_code -->
                    <div v-if="errors['team_code']" class="text-red-600 text-xs mt-1">
                      {{ errors['team_code'] }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="px-4 py-2 border text-lg font-bold">Code Name</td>
                  <td class="px-4 py-2 border text-lg font-bold">
                    <InputField 
                      v-model="form.team_name" 
                      class="w-full" 
                    />
                    <!-- Validation Error for Visual Inspection -->
                    <div v-if="errors['team_name']" class="text-red-600 text-xs mt-1">
                      {{ errors['team_name'] }}
                    </div>
                  </td>
                </tr>
                
              </tbody>
            </table>
  
            <!-- Submit Button -->
            <div class="flex justify-end mt-6">
              <PrimaryBtn type="submit">Add New Access Code</PrimaryBtn>
            </div>
          </form>
        </div>
      </div>
    </Container>
  </template>