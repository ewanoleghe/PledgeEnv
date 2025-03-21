<script setup>
import { ref } from 'vue';
import Container from "../Components/Container.vue";
import Title from "../Components/Title.vue";
import { useForm } from "@inertiajs/vue3";

// Company details
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name';
const companyEmail = import.meta.env.VITE_COMPANY_EMAIL || 'contact@example.com';
const companyPhone = import.meta.env.VITE_COMPANY_PHONE || '123-456-7890';
const companyAddress = import.meta.env.VITE_COMPANY_ADDRESS || '123 Main St, City, State, ZIP';
const companyCity = import.meta.env.VITE_COMPANY_CITY || '123 Main St, City, State, ZIP';
const companyState = import.meta.env.VITE_COMPANY_STATE || '123 Main St, City, State, ZIP';
const companyZip = import.meta.env.VITE_COMPANY_ZIP || '123 Main St, City, State, ZIP';

const companyAddress_NY = import.meta.env.VITE_COMPANY_ADDRESS_NY || '123 Main St, City, State, ZIP';
const companyCity_NY = import.meta.env.VITE_COMPANY_CITY_NY || '123 Main St, City, State, ZIP';
const companyState_NY = import.meta.env.VITE_COMPANY_STATE_NY || '123 Main St, City, State, ZIP';
const companyZip_NY = import.meta.env.VITE_COMPANY_ZIP_NY || '123 Main St, City, State, ZIP';

// Math challenge setup
const num1 = ref(Math.floor(Math.random() * 10)); // Random number between 0-9
const num2 = ref(Math.floor(Math.random() * 10)); // Random number between 0-9
const userAnswer = ref('');
const correctAnswer = ref(num1.value + num2.value);
const validationError = ref('');

// Form setup
const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const submit = () => {
    // Validate the math challenge
    if (parseInt(userAnswer.value) !== correctAnswer.value) {
        validationError.value = `Incorrect answer. Please solve: ${num1.value} + ${num2.value} = ?`;
        return;
    }

    // If validation passes, submit the form
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            userAnswer.value = ''; // Reset the answer field
            resetMathChallenge(); // Reset the math challenge
            validationError.value = ''; // Clear validation error
        },
        onError: (errors) => {
            // Errors are automatically available in form.errors
        },
    });
};

// Reset math challenge after successful submission
const resetMathChallenge = () => {
    num1.value = Math.floor(Math.random() * 10);
    num2.value = Math.floor(Math.random() * 10);
    correctAnswer.value = num1.value + num2.value;
};
</script>

<template>
    <Head title="Contact Us" />
    <Container class="w-3/5">
        <div class="mb-8 text-center">
            <Title>Contact Us</Title>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Success Message -->
            <div v-if="$page.props.flash.message" class="col-span-full p-4 bg-green-200">
                {{ $page.props.flash.message }}
                <div v-if="$page.props.flash.message_body">{{ $page.props.flash.message_body }}</div>
            </div>

            <!-- Validation Errors -->
            <div v-if="form.errors && Object.keys(form.errors).length > 0" class="col-span-full p-4 bg-red-200">
                <div v-for="(error, field) in form.errors" :key="field" class="text-red-800">
                    {{ error }}
                </div>
            </div>

            <!-- Contact Form -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
                <form @submit.prevent="submit" class="flex flex-col space-y-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Your Name*</label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            :disabled="form.processing"
                            class="mt-1 w-full p-2 border rounded-md dark:bg-gray-900 dark:border-gray-700"
                            :class="{ 'border-red-500': form.errors.name }"
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email*</label>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            required 
                            :disabled="form.processing"
                            class="mt-1 w-full p-2 border rounded-md dark:bg-gray-900 dark:border-gray-700"
                            :class="{ 'border-red-500': form.errors.email }"
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Phone*</label>
                        <input 
                            type="tel" 
                            v-model="form.phone" 
                            required 
                            :disabled="form.processing"
                            class="mt-1 w-full p-2 border rounded-md dark:bg-gray-900 dark:border-gray-700"
                            :class="{ 'border-red-500': form.errors.phone }"
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Subject*</label>
                        <input 
                            type="text" 
                            v-model="form.subject" 
                            required 
                            :disabled="form.processing"
                            class="mt-1 w-full p-2 border rounded-md dark:bg-gray-900 dark:border-gray-700"
                            :class="{ 'border-red-500': form.errors.subject }"
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Your Message*</label>
                        <textarea 
                            v-model="form.message" 
                            required 
                            :disabled="form.processing"
                            rows="4" 
                            class="mt-1 w-full p-2 border rounded-md dark:bg-gray-900 dark:border-gray-700"
                            :class="{ 'border-red-500': form.errors.message }"
                        ></textarea>
                    </div>

                    <!-- Math Challenge -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Verification: What is {{ num1 }} + {{ num2 }}?*
                        </label>
                        <input 
                            type="number" 
                            v-model="userAnswer" 
                            required 
                            :disabled="form.processing"
                            class="mt-1 w-full p-2 border rounded-md dark:bg-gray-900 dark:border-gray-700"
                            :class="{ 'border-red-500': validationError }"
                        >
                        <div v-if="validationError" class="text-red-800 mt-1">{{ validationError }}</div>
                    </div>

                    <button 
                        type="submit"
                        class="font-bold py-2 px-4 w-full rounded text-center relative flex items-center justify-center"
                        :class="form.processing ? 'bg-gray-400 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white'"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="absolute flex items-center">
                            <svg class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v2a6 6 0 100 12v2a8 8 0 01-8-8z"></path>
                            </svg>
                        </span>
                        <span v-if="!form.processing">Send It</span>
                        <span v-if="form.processing" class="ml-8">Sending...</span>
                    </button>
                </form>
            </div>

            <!-- Company Address -->
            <div class="relative p-6 bg-gray-100 dark:bg-gray-900 shadow-md rounded-lg overflow-hidden">
                <div style="overflow:hidden; position:relative; width:100%; height:0; padding-bottom:77.25%;">
                    <iframe 
                        title="Company Location" 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3025.803708715832!2d-74.21388122397606!3d40.678293771398614!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c252ae1cd18317%3A0x2361a7a1287f8f31!2s648%20Newark%20Ave%2C%20Elizabeth%2C%20NJ%2007208!5e0!3m2!1sen!2sus!4v1742117665497!5m2!1sen!2sus" 
                        width="600" 
                        height="450" 
                        style="border:0; position:absolute; top:0; left:0; width:100%; height:100%;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
                <div class="relative z-10 bg-white/80 dark:bg-gray-900/80 p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">NJ Office</h2>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Company:</strong> {{ companyName }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Address:</strong> {{ companyAddress }}, {{ companyCity }} {{ companyState }} {{ companyZip }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Phone:</strong> <a :href="'tel:' + companyPhone" class="text-blue-500">{{ companyPhone }}</a></p>
                </div>
                <div class="relative z-10 bg-white/80 dark:bg-gray-900/80 p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">NY Office</h2>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Company:</strong> {{ companyName }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Address:</strong> {{ companyAddress_NY }}, {{ companyCity_NY }} 
                        {{ companyState_NY }} {{ companyZip_NY }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Phone:</strong> <a :href="'tel:' + companyPhone" class="text-blue-500">{{ companyPhone }}</a></p>
                </div>
            </div>
        </div>
    </Container>
</template>