<!-- resources/js/Pages/Inspector/Login.vue -->
<script setup>
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import TextLink from "../../Components/TextLink.vue";
import InputField from "../../Components/InputField.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import { useForm } from "@inertiajs/vue3";
import ErrorMessages from "../../Components/ErrorMessages.vue";
import SessionMessages from "../../Components/SessionMessages.vue";
import CheckBox from "../../Components/CheckBox.vue";

// For demo purposes, you can add a theme toggle here
const logoImage = "../../images/login__logo.png";

const form = useForm({
    email: "",
    password: "",
    remember: null,
});

const submit = () => {
    form.post(route("login.handler"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Container 
        class="relative mt-16 w-full max-w-sm sm:w-2/5 mx-auto bg-cover bg-center bg-no-repeat bg-opacity-10 dark:bg-opacity-60" 
        :style="{ backgroundImage: `url(${logoImage})` }"
    >
        <Head title="- Login" />
        <div class="mb-8 text-center">
            <Title class="bg-white p-2 rounded dark:bg-gray-800 dark:text-white">Lead Inspector Risk Assessor Login</Title>
        </div>

        <!-- Errors messages -->
        <ErrorMessages :errors="form.errors" />
        <SessionMessages :status="status" />


        <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded shadow-sm dark:bg-gray-800 dark:text-white">
            <InputField
                label="Email"
                icon="at"
                v-model="form.email"
                class="w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />

            <InputField
                label="Password"
                type="password"
                icon="key"
                v-model="form.password"
                class="w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />

            <div class="flex flex-col sm:flex-row items-center justify-between">
                <CheckBox 
                    name="remember" 
                    v-model="form.remember" 
                    class="dark:text-white">
                    Remember me
                </CheckBox>

                <TextLink 
                    routeName="password.request" 
                    label="Forgot Password?" 
                    class="dark:text-white mt-2 sm:mt-0"
                />
            </div>

            <PrimaryBtn :disabled="form.processing" class="w-full dark:bg-blue-600 dark:hover:bg-blue-500 mb-5">Login</PrimaryBtn>

            <!-- Signup Link -->
            <div class="text-center mt-4">
                <span class="dark:text-white">Need an account?</span>
                <TextLink 
                    routeName="register" 
                    label="Sign Up" 
                    class="ml-1 text-blue-600 dark:text-blue-400"
                />
            </div>
        </form>
    </Container>
</template>

