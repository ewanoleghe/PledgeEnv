<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
  bookings: {
    type: Array,
    default: () => []
  },
});

const selectedDate = ref(null);

const handleDateClick = async (date) => {
    selectedDate.value = date;

    try {
        // Sending the request to your Laravel backend
        const response = await router.post(route('admin.viewdate_booking'), { 
            selected_date: date
        });

        // Ensure a successful response
        if (response.status === 200) {
            // Now process the data returned
            const dateRequests = response.data.dateRequests; // Accessing your date requests

            // Example: Update your UI or state with the fetched data
            console.log('Date Requests:', dateRequests);
            
            // Optionally visit a new route or update UI
            router.visit(route('admin.viewdate_booking')); // Use this if you want to navigate
        } else {
            console.error('Error with response:', response.errors);
        }
    } catch (error) {
        console.error('Error submitting the selected date:', error);
    }
};

// Ensure the current date is in Eastern Time (New York)
const getEasternTimeDate = () => {
  const now = new Date();
  const utc = now.getTime() + now.getTimezoneOffset() * 60000;
  return new Date(utc + (-5 * 3600000)); // Convert to Eastern Time
};

// Always get the Sunday of the current week at page load
const getStartOfWeek = () => {
  let date = getEasternTimeDate();
  let dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
  date.setDate(date.getDate() - dayOfWeek); // Move back to Sunday
  return new Date(date.getFullYear(), date.getMonth(), date.getDate());
};

const currentStartDate = ref(getStartOfWeek()); // This initializes correctly now

const goToToday = () => {
  let now = new Date();
  let easternTime = new Date(now.toLocaleString("en-US", { timeZone: "America/New_York" }));
  
  // Get the current date as Eastern Time
  const today = new Date(easternTime.getFullYear(), easternTime.getMonth(), easternTime.getDate());

  // Set currentStartDate to the most recent Sunday (moving back to Sunday)
  let dayOfWeek = today.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
  today.setDate(today.getDate() - dayOfWeek); // Move back to Sunday
  currentStartDate.value = today; // Set currentStartDate to the most recent Sunday
};

// Computed to get current and next 14 days (in Eastern Time)
const currentDates = computed(() => {
  const dates = [];
  for (let i = 0; i < 30; i++) {  
    const date = new Date(currentStartDate.value);
    date.setDate(date.getDate() + i);
    dates.push(date.toISOString().split('T')[0]); // Format YYYY-MM-DD
  }
  return dates;
});

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

// Group bookings by selected_date
const groupedBookings = computed(() => {
  const grouped = {};
  
  if (Array.isArray(props.bookings)) {
    props.bookings.forEach(booking => {
      if (!grouped[booking.selected_date]) {
        grouped[booking.selected_date] = [];
      }
      grouped[booking.selected_date].push(booking.order_id);
    });
  }
  
  return grouped;
});

const startDate = computed(() => currentDates.value[0]);
const endDate = computed(() => currentDates.value[currentDates.value.length - 1]);

// Go to the next 14 days
const goToNextTwoWeeks = () => {
  const newStartDate = new Date(currentStartDate.value);
  newStartDate.setDate(newStartDate.getDate() + 7);
  currentStartDate.value = newStartDate;
};

// Go to the previous 14 days
const goToPreviousTwoWeeks = () => {
  const newStartDate = new Date(currentStartDate.value);
  newStartDate.setDate(newStartDate.getDate() - 7);
  currentStartDate.value = newStartDate;
};

</script>

<template>
  <Head title="- All Booking" />
  <Container class="w-full max-w-4xl mx-auto">
    <div class="mb-8 text-center w-full">
      <Title>Lead Bookings - Calendar</Title>
    </div>
    <hr class="border-t-2 border-black w-full pb-3" />

    <div class="flex justify-between items-center mb-4">
      <button class="bg-gray-500 text-white text-xl px-4 py-2 rounded hover:bg-gray-600" 
        @click="goToPreviousTwoWeeks">
        Previous
      </button>

      <h2 class="text-xl text-center">
        Calendar from {{ startDate }} to {{ endDate }}
      </h2>

      <button class="bg-blue-500 text-white text-xl px-4 py-2 rounded hover:bg-blue-600" 
        @click="goToNextTwoWeeks">
        Next
      </button>
    </div>

    <div class="grid grid-cols-7 gap-4">
      <div v-for="day in weekDays" :key="day" 
          class="text-center font-bold text-lg"
          :class="{'text-red-500': day === 'Sun' || day === 'Sat'}">
          <span>{{ day }}</span>
      </div>

      <div v-for="date in currentDates" :key="date">
        <button
          v-if="groupedBookings[date]"
          @click="handleDateClick(date)"
          class="w-full text-center p-4 border rounded-md hover:shadow-lg transition duration-200 cursor-pointer bg-gray-100"
        >
          <span class="block text-lg font-medium">{{ date.split('-')[2] }}</span>
          <p class="text-sm text-gray-600">
            {{ monthNames[parseInt(date.split('-')[1]) - 1] }} {{ date.split('-')[0] }}
          </p>
          <ul class="mt-2 text-sm text-gray-800">
            <li v-for="orderId in groupedBookings[date]" :key="orderId" class="text-sm font-bold text-gray-600">
             . {{ orderId }}
            </li>
          </ul>
        </button>

        <div v-else class="text-center p-4 border rounded-md bg-white">
          <span class="block text-lg font-medium">{{ date.split('-')[2] }}</span>
          <p class="text-sm text-gray-600">
            {{ monthNames[parseInt(date.split('-')[1]) - 1] }} {{ date.split('-')[0] }}
          </p>
        </div>
      </div>
    </div>

    <div class="mt-4 text-center">
      <button 
          @click="goToToday"
          class="bg-green-500 text-white text-xl px-4 py-2 rounded hover:bg-green-600"
      >
        Back to Today
      </button>
    </div>
  </Container>
</template>
