<script setup>
import { ref } from 'vue';
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import TextLink from "../../Components/TextLink.vue";
import InputField from "../../Components/InputField.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import TermsPolicy from '../../Components/TermsPolicy.vue';
import { useForm } from "@inertiajs/vue3";
import ErrorMessages from "../../Components/ErrorMessages.vue";


// Initialize form data
const form = useForm({
    name: '',
    email: '',
    phone_number: '',
    team_code: '',
    license_number: '',
    license_copy: null,
    signature: null,
    password: '',
    password_confirmation: '', // Correct field name for password confirmation
    tos: false,
});


// Show/Hide password indicators
const showPassword = ref(false);
const showConfirmPassword = ref(false);

// Modal states
const modalOpen = ref(false);
const modalType = ref('');
const modalContent = ref('');

const openModal = (type) => {
    modalType.value = type;
    modalContent.value = getMarkdown(type);
    modalOpen.value = true; // Open the modal
};

const getMarkdown = (type) => {
    const markdowns = {
        terms: `## Terms of Service\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in est vitae metus auctor scelerisque.`,
        privacy: `## Privacy Policy\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean at nisi in dui accumsan cursus.`,
    };
    return markdowns[type] || 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
};

const handleFileChange = (field, event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Allowed file types (PDF and image formats)
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
    if (allowedTypes.includes(file.type) && file.size <= 5242880) {
        form[field] = file; // ✅ Correctly assign file to form
    } else {
        alert('File must be a PDF or an image (JPEG, PNG) and less than 5MB.');
    }
};


const submit = () => {
    form.post(route("register"), {
        forceFormData: true, // Ensures the form is submitted as multipart/form-data
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

</script>

<template>
    <Container class="w-full max-w-lg mx-auto px-4 sm:px-6">
        <div class="mb-8 text-center">
            <Title>Register a new Lead Inspector Risk Assessor account</Title>
            <p class="text-sm sm:text-base">
                Already have an account?
                <TextLink routeName="inspector.login" label="Login" />
            </p>
        </div>

        <!-- Errors messages -->
        <ErrorMessages :errors="form.errors || {}"/>

        <form @submit.prevent="submit" class="space-y-6">
            <InputField 
                label="Full Name" 
                v-model="form.name" 
                icon="user" 
                class="w-full" 
            />
            <InputField 
                label="Email" 
                type="email" 
                v-model="form.email" 
                icon="at" class="w-full" 
            />
            <InputField 
                label="Phone Number" 
                v-model="form.phone_number" 
                icon="phone" 
                class="w-full" 
            />
            <InputField 
                label="Team Code" 
                v-model="form.team_code" 
                icon="user-secret" 
                class="w-full" 
            />
            <InputField 
                label="Lead Identification Permit Number (L.I.R.A)" 
                v-model="form.license_number" 
                icon="sign" 
                class="w-full" 
            />
            <InputField 
                label="Upload copy of Lead Identification Permit (L.I.R.A)" 
                :type="'file'" 
                @change="handleFileChange('license_copy', $event)" 
                icon="vcard" 
                class="w-full" 
            />
            <InputField 
                label="Upload Signature (upload your signature in black ink on a white paper)" 
                :type="'file'" 
                @change="handleFileChange('signature', $event)" 
                icon="signature" 
                class="w-full" 
            />
            <InputField 
                label="Password" 
                :type="showPassword ? 'text' : 'password'" 
                v-model="form.password" 
                icon="key" 
                class="w-full">
                <template #append>
                    <button type="button" @click="showPassword = !showPassword" class="focus:outline-none px-2" :aria-label="showPassword ? 'Hide password' : 'Show password'">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </template>
            </InputField>


                        <InputField 
                label="Confirm Password" 
                :type="showConfirmPassword ? 'text' : 'password'" 
                v-model="form.password_confirmation" 
                icon="key" 
                class="w-full">
                <template #append>
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="focus:outline-none px-2" :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'">
                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </template>
            </InputField>

            <p class="text-slate-500 text-sm dark:text-slate-400 flex items-start">
                <input type="checkbox" name="tos" v-model="form.tos" class="mt-1">
                <span class="ml-2">
                    By creating an account, you agree to our 
                    <a href="#" @click.prevent="openModal('terms')" class="text-blue-600 hover:underline">Terms of Service</a> and 
                    <a href="#" @click.prevent="openModal('privacy')" class="text-blue-600 hover:underline">Privacy Policy</a>.
                </span>
            </p>

            <PrimaryBtn class="w-full">Register</PrimaryBtn>
        </form>

        <TermsPolicy :isOpen="modalOpen" :type="modalType" :content="modalContent" @close="modalOpen = false" />
    </Container>
</template>
