<script setup>
import { ref, onMounted  } from 'vue';
import Container from "../../Components/Container.vue";

const props = defineProps({
  message: String,  // Expecting the message prop
  eData: Object,
  paymentIntent: String,
  status: String
});

onMounted(() => {
  if (props.status === 'succeeded') {
    // Optional: Confirm payment status with your backend
    fetch(`/api/confirm-payment/${props.paymentIntent}`)
      .then(response => response.json())
      .then(data => {
        console.log('Payment confirmed:', data);
      });
  }
});

// Declare reactive data using `ref`
const successMessage = ref(props.message || 'Your payment was processed successfully. Thank you for your purchase!');
</script>

<template>
  <Head title="- Payment Successful" />
  <Container class="w-4/5">
    <div class="p-5 bg-green-100 text-green-600 text-center font-bold p-10 border-2 border-green-600 rounded-lg">
      <h2>Payment Status</h2>
      <!-- Check if successMessage is 'unpaid' and display the custom message -->
      <p v-if="successMessage === 'unpaid'">
        Your order has been placed and the invoice has been generated.
        <br />
        An email with your invoice has been sent to your inbox. If you do not see it there, please check your spam or junk folder. To ensure future emails are delivered correctly, we recommend whitelisting our email address.
      </p>
      <!-- Check if STRIPE is succeeded -->
      <p v-if="paymentIntent === 'succeeded'">
        Your order has been placed and the Receipt has been generated.
        <br />
        An email with your invoice has been sent to your inbox. If you do not see it there, please check your spam or junk folder. To ensure future emails are delivered correctly, we recommend whitelisting our email address.
      </p>
      <!-- Default message if successMessage is not 'unpaid' -->
      <p v-else>
        Your payment was processed successfully. Thank you for your purchase!
      </p>
    </div>
  </Container>
</template>


