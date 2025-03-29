<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import PaginationLinks from '../../../Components/PaginationLinks.vue';  
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
    bookings: Object,
    searchTerm: String,
});

// Reactive search term
const search = ref(props.searchTerm);

// Watch for changes in the search term
watch(
    search, 
    debounce((q) => {
        // Use Inertia to make the request and stay on the same page
        router.get(window.location.pathname, { search: q }, { preserveState: true });
    }, 500)
);

// Update Payment Status
const updatePaymentStatus = async (id) => {
    try {
        // Send a POST request to update the payment status while preserving scroll
        await router.post('bookings/update_pay_status', { id }, { preserveScroll: true });
    } catch (error) {
        console.error('Error updating payment status:', error);
    }
};


</script>

<template>
    <Head title="- Select a payment method" />
    <Container class="w-full max-w-4xl mx-auto">
        BOOKING
    </Container>
</template>
