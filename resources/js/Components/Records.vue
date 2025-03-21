<script setup>
import InputField from '@/Components/InputField.vue';
import { ref, watch, computed, onMounted, onUnmounted } from "vue";
import { useForm, router } from "@inertiajs/vue3";

// Emit event to the parent
const emit = defineEmits();

const props = defineProps({
    bookings: Object,
    siteContactInfo: Array,
    records: Array,
    unitForms: Array,
    errors: Object,
    message: String,
});

const clearFilePreviews = () => {
    // Clear the file previews
    filePreviews = {};  // or use a more specific logic to clear the previews if needed
};

const reloadPage = () => {
    // Reload the page after submission
    location.reload();
};

const form = useForm({
  bookingId: props.bookings.id, // assuming 'bookings.id' is available
});

const maxPhotos = 8; // Maximum number of images allowed
const showPhotoInputs = ref({}); // Controls visibility of photo inputs per unit
const photoFields = ref({}); // Stores photo data per unit

const record = ref({}); // Initialize as an empty object
const filePreviews = ref({}); // Ensure it's an empty object at first

onMounted(async () => {
    try {
        const response = await fetch("your-api-endpoint");
        record.value = await response.json();
    } catch (error) {
        // console.error("Error fetching record:", error);
    }
});

// Generate form instances based on the number of units
const unitForms = ref([]);

for (let i = 0; i < props.bookings.units; i++) {
    unitForms.value.push(useForm({
        order_id: props.bookings.order_id,
        booking_id: props.bookings.id,
        unit_number: i + 1,
        comments: '', // Ensure this is reactive
        floor_plan: null,
        xrf_report: null,
        chain_custody: null,
        photos: [],
    }));
}

const commentWordCount = ref({});

// Function to limit comments to 20 words
const limitComments = (unitIndex) => {
    let words = unitForms.value[unitIndex].comments.split(/\s+/).filter(Boolean);
    if (words.length > 20) {
        unitForms.value[unitIndex].comments = words.slice(0, 20).join(" ");
    }
    commentWordCount.value[unitIndex] = words.length;
};

// Function to handle file input changes
const handleFileChange = (form, field, event) => {
    const file = event.target.files[0];
    if (file) {
        // Store the file reference
        form[field] = file;

        // Remove previous preview URL if exists
        if (filePreviews.value[field]) {
            URL.revokeObjectURL(filePreviews.value[field]);
        }

        // Create a new preview URL
        filePreviews.value[field] = URL.createObjectURL(file);
    }
};

// Cleanup URLs to avoid memory leaks
onUnmounted(() => {
    Object.values(filePreviews.value).forEach(url => URL.revokeObjectURL(url));
});

// Function to toggle photo inputs for a unit
const togglePhotoInputs = (unitIndex) => {
    showPhotoInputs.value[unitIndex] = !showPhotoInputs.value[unitIndex];
    if (!photoFields.value[unitIndex]) {
        photoFields.value[unitIndex] = [];
    }
};

// Function to add new photo field
const addNewPhotoField = (unitIndex) => {
    if (photoFields.value[unitIndex].length < maxPhotos) {
        // Ensure that each new photo field has default values for room, component, hazard, substrate, and value
        photoFields.value[unitIndex].push({
            description: '',
            file: null,
            room: '',
            component: '',
            hazard: '',
            substrate: '',
            value: ''
        });
    }
};

// Function to handle photo file input change
const handlePhotoFileChange = (unitIndex, index, event) => {
    const file = event.target.files[0];
    if (!photoFields.value[unitIndex]) {
        photoFields.value[unitIndex] = [];
    }
    if (!photoFields.value[unitIndex][index]) {
        photoFields.value[unitIndex][index] = {};
    }
    
    if (file) {
        photoFields.value[unitIndex][index].file = file;
        filePreviews.value[`photo-${unitIndex}-${index}`] = URL.createObjectURL(file);
    }
};

onUnmounted(() => {
    // Revoke all image URLs to prevent memory leaks
    Object.values(filePreviews.value).forEach(url => URL.revokeObjectURL(url));
});

// Function to check if a unit has already been recorded
const isUnitRecorded = (unitNumber) => {
    if (!props.records || typeof props.records !== 'object') {
        return false; // Return false if records are not available
    }

    // Loop through records grouped by order_id
    for (const orderId in props.records) {
        // Check if the unit number exists within the current order_id's records
        if (props.records[orderId].some(record => record.unit_number === unitNumber)) {
            return true;
        }
    }

    return false;
};

// Refactored function to optimize getPassFailStatus
const getPassFailStatus = (unitNumber) => {
    // Check if props.records is an object and contains the expected keys
    if (!props.records || typeof props.records !== 'object') {
        console.error('props.records is not a valid object:', props.records);
        return ''; // Return empty string if it's not an object
    }

    // Assuming props.records is a dictionary where keys might represent different orders or units
    // Convert it to an array of records (if needed)
    const recordsArray = Object.values(props.records); // Convert object to an array of records

    // Find the record for the unitNumber
    for (const recordGroup of recordsArray) {
        // Assuming each record group is an array of records
        const record = recordGroup.find(r => r.unit_number === unitNumber);
        if (record) {
            return record.pass_fail; // Return the pass/fail status if found
        }
    }

    return ''; // Return empty string if no record found for the unit
};

// Computed property to handle pass/fail class for each unit
const passFailStatusClass = (unitNumber) => {
    const status = getPassFailStatus(unitNumber);
    if (status === 'pass') return 'bg-blue-500'; // Blue for pass
    if (status === 'fail') return 'bg-red-500'; // Red for fail
    return 'bg-gray-300'; // Default (gray) if no status or unknown
};

const units = ref([]); // Initialize units array

// Initialize units based on the number of units in `props.bookings`
watch(() => props.bookings.units, (newUnitsCount) => {
    if (newUnitsCount) {
        units.value = Array.from({ length: newUnitsCount }, () => ({ designation: '' }));
    }
}, { immediate: true });

// Form submission logic
const submitForm = (form, unitIndex, passFailStatus) => {
    const formData = new FormData();

    // **Floor Plan Validation**
    if (!form.floor_plan) {
        alert('Floor plan is required.');
        return;
    }

    // Validate file type (allowing only images or PDFs)
    const allowedFileTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!allowedFileTypes.includes(form.floor_plan.type)) {
        alert('Invalid file type. Please upload a JPEG, PNG, or PDF file.');
        return;
    }

    // Validate file size (e.g., max 5MB)
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    if (form.floor_plan.size > maxFileSize) {
        alert('File size exceeds 5MB. Please upload a smaller file.');
        return;
    }
    
    // Append basic form data (booking details, customer info, etc.)
    formData.append('booking_id', props.bookings.id);
    formData.append('order_id', props.bookings.order_id);
    formData.append('unit_number', form.unit_number);
    formData.append('comments', form.comments || '');
    
    // Booking Details
    formData.append('selected_date', props.bookings.selected_date);
    formData.append('selected_time', props.bookings.selectedTime);
    formData.append('address', props.bookings.address);
    formData.append('municipality', props.bookings.municipality);
    formData.append('city', props.bookings.city);
    formData.append('county', props.bookings.county);
    formData.append('block', props.bookings.block);
    formData.append('lot', props.bookings.lot);
    formData.append('service', props.bookings.service);
    formData.append('methodology', props.bookings.methodology);
    formData.append('includeXrf', props.bookings.includeXrf);
    formData.append('optional_message', props.bookings.optionalMessage || '');
    
    // Customer Info
    formData.append('customer_name', props.bookings.name);
    formData.append('customer_email', props.bookings.email);
    formData.append('customer_phone', props.bookings.phone);
    formData.append('property_owner_name', props.bookings.name);
    formData.append('property_owner_phone', props.bookings.phone);
    formData.append('property_owner_email', props.bookings.email);
    formData.append('pass_fail', passFailStatus);
    formData.append('designation', form.designation || '');
    formData.append('xrf_pass_fail', form.xrf_pass_fail || '');

    // Attachments (if available)
    if (form.floor_plan) formData.append('floor_plan', form.floor_plan);
    if (form.lab_report) formData.append('lab_report', form.lab_report);
    if (form.xrf_report) formData.append('xrf_report', form.xrf_report);
    if (form.xrf_csv) formData.append('xrf_csv', form.xrf_csv);
    if (form.chain_custody) formData.append('chain_custody', form.chain_custody);

    // Ensure Photo Fields are Submitted Even if Null
    if (!photoFields.value[unitIndex]) {
        photoFields.value[unitIndex] = []; // Ensure array exists
    }

        // Loop through the photos for the unit
        photoFields.value[unitIndex].forEach((photo, index) => {
        formData.append(`photo_descriptions[${index}]`, photo.description || '');
        formData.append(`photo_rooms[${index}]`, photo.room || '');
        formData.append(`photo_components[${index}]`, photo.component || '');
        formData.append(`photo_hazards[${index}]`, photo.hazard || '');
        formData.append(`photo_substrates[${index}]`, photo.substrate || '');
        formData.append(`photo_values[${index}]`, photo.value || '');
        
        if (photo.file) {
            formData.append(`photos[${index}]`, photo.file);
        }
    });

    // Submit the form data
    router.post(route('records.store'), formData, {
        forceFormData: true,
        onSuccess: () => {
            alert(`Inspection for Unit ${form.unit_number} submitted successfully!`);
            resetForm(form, unitIndex);
            clearFilePreviews();  // Function to clear file previews
            reloadPage();         // Function to reload the page
        },
        onError: (error) => {
            if (error.response && error.response.data.errors) {
                const errors = error.response.data.errors;
                for (const field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        form.errors[field] = errors[field][0];
                    }
                }
                alert('There are validation errors in your submission.');
            }
        }
    });
};

// Function to reset all form fields
const resetForm = (form, unitIndex) => {
    // Reset the form fields
    form.unit_number = '';
    form.comments = '';
    form.designation = '';
    
    // Reset the file inputs
    form.floor_plan = null;
    form.lab_report = null;
    form.xrf_report = null;
    form.chain_custody = null;

    // Reset photo fields
    if (photoFields.value[unitIndex]) {
        photoFields.value[unitIndex].forEach(photo => {
            photo.file = null; // Reset the file field
            photo.description = '';
            photo.room = '';
            photo.component = '';
            photo.hazard = '';
            photo.substrate = '';
            photo.value = '';
        });
    }
};

</script>

<template>
    <Head title="- Lead Bookings " />
    <div class="mb-8 text-justify w-full">
        
        <div class="flex items-center mt-5">
            <p class="flex items-center ml-1 mr-3">
                <span class="w-4 h-4 bg-green-500 rounded-full inline-block mr-2"></span> Completed Inspection
            </p>
            <p class="flex items-center ml-1 mr-3">
                <span class="w-4 h-4 bg-blue-500 rounded-full inline-block mr-2"></span> Pass Inspection
            </p>
            <p class="flex items-center ml-1 mr-3">
                <span class="w-4 h-4 bg-red-500 rounded-full inline-block mr-2"></span> Fail Inspection
            </p>
            <p class="flex items-center ml-1 mr-3">
                <span class="w-4 h-4 bg-gray-300 rounded-full inline-block mr-2"></span> Pending Inspectors Decisions
            </p>
        </div>

        <div class="pt-4 mt-5 pb-4 flex">
            <p class="text-3xl font-bold">Submit Report</p>
        </div>

        <div v-for="(form, unitIndex) in unitForms" :key="unitIndex" class="mb-10 border-b pb-6">
            <h2 class="text-xl font-bold">
                Unit {{ form.unit_number }}
                <sup
                    class="inline-block w-5 h-5 rounded-full"
                    :class="passFailStatusClass(form.unit_number)"
                    :title="`Unit ${form.unit_number}: ${getPassFailStatus(form.unit_number)}`"
                ></sup>
            </h2>
            <h2 class="text-xl font-bold">{{ bookings.address }}</h2>
            <h2 class="text-xl font-bold">{{ bookings.municipality }}, {{ bookings.state }}, {{ bookings.city }}</h2>

            <!-- If form is submitted, show "Document Submitted" -->
            <div v-if="isUnitRecorded(form.unit_number)" class="text-green-600 font-semibold">
                ✅ Documents Submitted
            </div>

            <!-- Show links for uploaded documents before submission -->
            <!-- Document Preview -->
                <div v-if="filePreviews?.floor_plan && !isUnitRecorded(form?.unit_number)" class="mt-5">
                    <p>Floor Plan: <a :href="filePreviews.floor_plan" target="_blank" class="text-blue-500">View Uploaded Floor Plan</a></p>
                </div>
                <div v-if="filePreviews?.lab_report && !isUnitRecorded(form?.unit_number)">
                    <p>Lab Report: <a :href="filePreviews.lab_report" target="_blank" class="text-blue-500">View Uploaded Lab Report</a></p>
                </div>
                <div v-if="filePreviews?.chain_custody && !isUnitRecorded(form?.unit_number)">
                    <p>Lab Report: <a :href="filePreviews.chain_custody" target="_blank" class="text-blue-500">View Uploaded Chain of Custody</a></p>
                </div>
                <div v-if="filePreviews?.xrf_report && !isUnitRecorded(form?.unit_number)" class="mb-5">
                    <p>XRF Report: <a :href="filePreviews.xrf_report" target="_blank" class="text-blue-500">View Uploaded XRF Report</a></p>
                </div>


            <!-- Hide the form if the unit has been recorded -->
            <form v-if="!isUnitRecorded(form.unit_number)" @submit.prevent="submitForm(form, unitIndex)" class="space-y-6 mt-2">
                <InputField 
                    label="* Optional (If unit designation is a letter e.g. Unit 'A' or 'B' ...'E' ...)" 
                    type="text" 
                    v-model="form.designation" 
                    icon="house" 
                    class="w-1/4"
                    :error="form.errors.designation"
                />
                <InputField 
                    label="Upload Floor Plan" 
                    type="file" 
                    @change="handleFileChange(form, 'floor_plan', $event)" 
                    icon="file" 
                    class="w-full"
                    :error="form.errors.floor_plan"
                />
                <p v-if="form.errors.floor_plan" class="text-red-500 text-sm">{{ form.errors.floor_plan }}</p>

                <InputField v-if="props.bookings.methodology === 'Dust Wipe Sampling'"
                    label="Lab Report" 
                    type="file" 
                    @change="handleFileChange(form, 'lab_report', $event)" 
                    icon="wallet" 
                    class="w-full" 
                    :error="form.errors.lab_report"
                    required
                />
                <p v-if="form.errors.lab_report" class="text-red-500 text-sm">{{ form.errors.lab_report }}</p>
                
                <InputField v-if="props.bookings.methodology === 'Dust Wipe Sampling'"
                    label="Chain of Custody" 
                    type="file" 
                    @change="handleFileChange(form, 'chain_custody', $event)" 
                    icon="wallet" 
                    class="w-full" 
                    :error="form.errors.chain_custody"
                    required
                />
                <p v-if="form.errors.chain_custody" class="text-red-500 text-sm">{{ form.errors.chain_custody }}</p>

                <InputField v-if="props.bookings.includeXrf"
                    label="XRF Report" 
                    type="file" 
                    @change="handleFileChange(form, 'xrf_report', $event)" 
                    icon="wallet" 
                    class="w-full" 
                    :error="form.errors.xrf_report"
                    required
                />
                <p v-if="form.errors.xrf_report" class="text-red-500 text-sm">{{ form.errors.xrf_report }}</p>

                <InputField v-if="props.bookings.includeXrf"
                    label="XRF Report (CSV only)" 
                    type="file" 
                    @change="handleFileChange(form, 'xrf_csv', $event)" 
                    icon="wallet" 
                    class="w-full" 
                    :error="form.errors.xrf_csv"
                    required
                />
                <p v-if="form.errors.xrf_csv" class="text-red-500 text-sm">{{ form.errors.xrf_csv }}</p>

                <!-- XRF Pass or Fail -->
                <div v-if="props.bookings.includeXrf" class="w-full">
                    <label class="block mb-2">XRF Report Status</label>
                    <div class="flex items-center">
                        <label class="mr-4">
                            <input 
                                type="radio" 
                                v-model="form.xrf_pass_fail" 
                                value="fail" 
                                :required="props.bookings.includeXrf"
                                class="mr-2"
                            />
                            FAIL
                        </label>
                        <label>
                            <input 
                                type="radio" 
                                v-model="form.xrf_pass_fail" 
                                value="pass" 
                                :required="props.bookings.includeXrf"
                                class="mr-2"
                            />
                            PASS
                        </label>
                    </div>
                    <div v-if="form.errors.xrf_report" class="text-red-500 text-sm mt-1">
                        {{ form.errors.xrf_report }}
                    </div>
                </div>

                <!-- Photo Upload Section -->
                <div @click="togglePhotoInputs(unitIndex)" class="cursor-pointer text-blue-500">
                    <span v-if="!showPhotoInputs[unitIndex]">+ Add Description or Photos/Images</span>
                    <span v-else>- Hide Description & Photos/Images</span>
                </div>

                <div v-if="showPhotoInputs[unitIndex]">
                    <div v-for="(photo, index) in photoFields[unitIndex]" :key="index" class="space-y-4">
                        <!-- Image Preview -->
                        <div v-if="filePreviews[`photo-${unitIndex}-${index}`]" class="mt-5 pl-60">
                            <img 
                                :src="filePreviews[`photo-${unitIndex}-${index}`]" 
                                alt="Preview" 
                                class="w-32 h-32 object-cover border border-gray-300 rounded"
                            />
                        </div>
                        
                        <!-- File Upload Field -->
                        <InputField 
                            label="Upload Image" 
                            type="file" 
                            @change="handlePhotoFileChange(unitIndex, index, $event)" 
                            icon="image" 
                            class="w-full" 
                        />
                        
                        <!-- Image Description Fields -->
                        <InputField 
                            label="Description" 
                            v-model="photoFields[unitIndex][index].description"
                            placeholder="Enter photo description" 
                            class="w-full" 
                        />
                        
                        <InputField 
                            label="Room Equivalent" 
                            v-model="photoFields[unitIndex][index].room"
                            placeholder="Enter room" 
                            class="w-full" 
                        />
                        
                        <InputField 
                            label="Component" 
                            v-model="photoFields[unitIndex][index].component"
                            placeholder="Enter component" 
                            class="w-full" 
                        />
                        
                        <InputField v-if="props.bookings.methodology === 'Visual Inspection'"
                        label="Hazard" 
                            v-model="photoFields[unitIndex][index].hazard"
                            placeholder="Enter hazard" 
                            class="w-full" 
                        />
                        <InputField v-if="props.bookings.methodology === 'Dust Wipe Sampling'"
                            label="Substrate" 
                            v-model="photoFields[unitIndex][index].substrate"
                            placeholder="Enter Substrate" 
                            class="w-full" 
                        />
                        <InputField v-if="props.bookings.methodology === 'Dust Wipe Sampling'"
                            label="Value( mg/cm2 )" 
                            v-model="photoFields[unitIndex][index].value"
                            placeholder="Enter Value" 
                            class="w-full" 
                        />

                        <hr class="border-t-2 border-gray-300 w-full pb-5 mb-5 mt-2">
                    </div>

                    <!-- Add New Photo Button -->
                    <button 
                        v-if="photoFields[unitIndex]?.length < maxPhotos" 
                        @click.prevent="addNewPhotoField(unitIndex)" 
                        class="text-blue-500"
                    >
                        + Add Another Description/Image
                    </button>
                </div>

                <p class="text-lg font-bold">Comments</p>
                <textarea 
                    placeholder="Enter your comments here (Max: 20 words)" 
                    v-model="unitForms[unitIndex].comments" 
                    @input="limitComments(unitIndex)" 
                    class="w-full h-15 border border-gray-300 p-2 resize-none"
                ></textarea>
                <p class="text-sm text-gray-500">Word count: {{ commentWordCount[unitIndex] }}/20</p>

                <div class="p-6 flex justify-between md:grid-cols-2 gap-4">
                    <button 
                        type="button" 
                        @click="submitForm(form, unitIndex, 'fail')" 
                        class="text-lg py-3 px-6 mx-auto block bg-red-500 text-white hover:bg-red-700 rounded"
                    >
                        Submit as Failed Inspection
                    </button>

                    <button 
                        type="button" 
                        @click="submitForm(form, unitIndex, 'pass')" 
                        class="text-lg py-3 px-6 mx-auto block bg-green-500 text-white hover:bg-green-700 rounded"
                    >
                        <i class="fa fa-arrow-circle-o-right text-white mr-2"></i>
                        Submit as Pass Inspection
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

