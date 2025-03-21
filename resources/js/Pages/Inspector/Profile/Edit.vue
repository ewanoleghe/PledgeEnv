<script setup>
import InspectorLayout from '@/Layouts/InspectorLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";
import ErrorMessages from "../../../Components/ErrorMessages.vue";
import SessionMessages from "../../../Components/SessionMessages.vue";
import { router, useForm } from "@inertiajs/vue3";
import UpdatePassword from "./UpdatePassword.vue";


// Set the layout for this component
defineOptions({ layout: InspectorLayout });

const props = defineProps({
    eUser: Object,
    status: String,
});

const form = useForm({
    phone_number: props.eUser.phone_number,
    email: props.eUser.email,
});

const resendEmail = (e) => {
    router.post(
        route("verification.send"),
        {},
        {
            onStart: () => (e.target.disabled = true),
            onFinish: () => (e.target.disabled = false),
        }
    );
};
</script>

<template>
    <Container class="mb-6">
        <div class="mb-6">
            <Title>Update Information</Title>
            <p>Update your account's profile information and email address.</p>
        </div>

        <ErrorMessages :errors="form.errors" />

        <form
            @submit.prevent="form.patch(route('profile.info'))"
            class="space-y-6"
        >
            <InputField
                label="Phone Number"
                icon="id-badge"
                class="w-1/2"
                v-model="form.phone_number"
            />

            <InputField
                label="Email"
                icon="at"
                class="w-1/2"
                v-model="form.email"
            />

            <div v-if="eUser.email_verified_at === null">
                <SessionMessages :status="status" />

                <p>
                    Your email address is unverified.
                    <button
                        @click="resendEmail"
                        class="text-indigo-500 font-medium underline dark:text-indigo-400 disabled:text-slate-400 disabled:cursor-wait"
                    >
                        Click here to re-send the verification email.
                    </button>
                </p>
            </div>

            <PrimaryBtn :disabled="form.processing">Save</PrimaryBtn>
        </form>
    </Container>
    <UpdatePassword />

</template>
