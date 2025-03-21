<script setup>
import InspectorLayout from '@/Layouts/InspectorLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";
import { router } from '@inertiajs/vue3'; // Import the router from inertia

// Set the layout for this component
defineOptions({ layout: InspectorLayout });

const props = defineProps({
    eUser: Object,
});

// Method to handle the edit button click
const editUser = async () => {
    // Redirect to the edit route (replace 'edit-route-name' with your actual route)
    await router.get(route('edit-inspector', { id: props.eUser.id }));
};

</script>

<template>
    <Head title="- Settings" />
    <Container class="w-full max-w-4xl mx-auto">
        <div class="mb-8 text-center w-full">
            <Title>Inspector's Settings</Title>
        </div>
        <hr class="border-t-2 border-black w-full">

        <div class="flex flex-col md:flex-row justify-center space-x-0 md:space-x-4 h-full w-full">
            <div class="flex flex-col md:flex-row justify-center space-x-0 md:space-x-4 h-full w-full">
                <div class="w-full flex flex-col justify-between mb-4 md:mb-0">
                    <div class="p-1 flex-grow">
                        <h2 class="text-xl font-bold p-1">All Lead Booking Jobs </h2>
                        <hr class="border-t-2 border-black p-1 w-full">

                        <!-- Your component code here -->
                    </div>
                    <div>
                        <p class="mt-5"><strong>Lead Inspector Risk Assessor</strong></p>
                        <hr>
                        <p><strong>Name:</strong> {{ eUser.name }}</p>
                        <p><strong>Email:</strong> {{ eUser.email }}</p>
                        <p><strong>Mobile:</strong> {{ eUser.phone_number }}</p>
                        <p><strong>License #: {{ eUser.license_number }}</strong></p>
                        <hr>
                        <p class="mt-5"><strong>Signature</strong></p>
                        <img
                            :src="eUser.signature ? `/file/signature/${eUser.signature}` : '/file/signature/default.jpg'"
                            class="w-4/14 h-auto max-h-20 object-contain bg-slate-100 border-[2%] border-solid border-black p-[2%]"
                            alt="Signature"
                        />

                        <p class="mt-4"><strong>License copy</strong></p>
                        <img
                            :src="eUser.license_copy ? `/file/license/${eUser.license_copy}` : '/storage/app/private/license/default.jpg'"
                            class="w-4/12 h-auto object-cover object-center bg-slate-900 border-[2%] border-solid border-black p-[2%]"
                            alt="License"
                        />
                        <hr>
                        <p class="break-words pt-5"><strong>Additional License Info:</strong></p>
                        <p class="break-words">
                            <strong>Agreement with {{ companyName }}</strong> Extra details about the agents/inspectors agreement.
                        </p>
                        <div class="mt-6"><strong>Downloads (Template)</strong> 
                            <ul>
                                <li><a href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" target="_blank" rel="noopener noreferrer">Tax Doc: <strong>W-9</strong></a></li>
                                <li><a href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" target="_blank" rel="noopener noreferrer"><strong>Floor Plan</strong></a></li>
                                <li><a href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" target="_blank" rel="noopener noreferrer"><strong>Chain-of-Custody</strong></a></li>
                            </ul>
                        </div>

                        <!-- Edit button below the content -->
                        <div class="mt-6 flex justify-center">
                            <PrimaryBtn 
                                @click="editUser" 
                                class=" text-white font-bold py-2 px-8 rounded hover:bg-red-500"
                            >
                                Edit
                            </PrimaryBtn>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </Container>
</template>
