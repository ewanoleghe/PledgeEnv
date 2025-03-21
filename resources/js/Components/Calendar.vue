<script setup>
import { ref, computed, onMounted } from "vue";

// Emit event to the parent
const emit = defineEmits();

// Receive `datesWithBookings` from the parent component (no more `similarDates`)
const props = defineProps({
  datesWithBookings: {
    type: Array,
    default: () => [],
  },
  weekendFee: {
    type: [Number, null],
    default: null,
  },
});

const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const selectedDate = ref(null); // Track the selected date

const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

// Computed property for the month name
const monthName = computed(() => {
  const date = new Date(currentYear.value, currentMonth.value);
  return date.toLocaleString("default", { month: "long" });
});


// // Debug: Manually simulate similarDates if it's empty
// if (props.similarDates.length === 0) {
//   // Simulating a "fully booked" date for testing
//   props.similarDates.push('2024-12-28');
//   console.log('Simulated similarDates:', props.similarDates); // Debug log
// }

// Function to generate days of the current month
const generateDays = (month, year) => {
  const days = [];
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  // Get today's date
  const today = new Date();

  // Add empty slots for previous month
  for (let i = 0; i < firstDay.getDay(); i++) {
    days.push({ date: null, isCurrent: false, isPast: false });
  }

  // Add days for the current month
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(year, month, i);
    const isPast = date < today;  // Check if the date is in the past

    days.push({
      date,
      isCurrent: date.toDateString() === today.toDateString(),
      isPast: isPast,  // Mark as past if the date is before today
    });
  }

  return days;
};

// Computed property for the days of the current month
const days = computed(() => generateDays(currentMonth.value, currentYear.value));

// Computed property to check if the current month is the current month
const isCurrentMonth = computed(() => {
  const today = new Date();
  return today.getMonth() === currentMonth.value && today.getFullYear() === currentYear.value;
});

// Handle the click event on a day
const handleDayClick = (day) => {
  if (day.date && !day.isPast && !isFullyBooked(day)) {
    selectedDate.value = day.date;

    // Convert selected date to ISO 8601 format before emitting
    const isoDate = selectedDate.value.toISOString(); // Convert date to ISO format

    // Emit the selected date in ISO 8601 format
    emit('update:selectedDate', isoDate);
  }
};

// Go to previous month
const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
};

// Go to next month
const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
};

// Set the calendar to today's date
const setToday = () => {
  currentMonth.value = new Date().getMonth();
  currentYear.value = new Date().getFullYear();
  selectedDate.value = null; // Reset the selected date when going to today's date
};

// Normalize the date to 'YYYY-MM-DD' format for comparison
const normalizeDate = (date) => {
  return date.toISOString().split('T')[0];  // Convert to YYYY-MM-DD
};

// Function to check if a date is fully booked
const isFullyBooked = (day) => {
  if (day.date) {
    const normalizedDate = normalizeDate(day.date); // Normalize the date to 'YYYY-MM-DD'
    console.log('Checking date:', normalizedDate); // Debugging log
    console.log('Dates With Bookings:', props.datesWithBookings); // Debugging log

    // Check if the normalized date exists in the datesWithBookings array
    return props.datesWithBookings.includes(normalizedDate);
  }
  return false;
};

// Ensure this function is defined in the script setup section
const hasBookings = (day) => {
  if (day.date) {
    const normalizedDate = normalizeDate(day.date);
    return props.datesWithBookings.includes(normalizedDate);
  }
  return false;
};
</script>


<template>
  <header>
    <div class="calendar w-full max-w-full sm:max-w-3xl md:max-w-4xl lg:max-w-4xl mx-auto text-center font-sans relative p-4 sm:p-6 lg:p-8">
      <!-- Calendar Header -->
      <div class="header flex justify-between items-center mt-8">
        <button
          @click="prevMonth"
          :disabled="isCurrentMonth"
          class="px-4 py-2 text-sm text-white bg-gray-500 rounded-md hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Prev
        </button>
        <h2 class="text-lg sm:text-xl md:text-2xl font-semibold">{{ monthName }} {{ currentYear }}</h2>
        <button
          @click="nextMonth"
          class="px-4 py-2 text-sm text-white bg-gray-500 rounded-md hover:bg-gray-600"
        >
          Next
        </button>
      </div>

      <!-- Weekdays -->
      <div class="weekdays grid grid-cols-7 mt-4 text-xs sm:text-sm font-medium">
        <div v-for="day in weekdays" :key="day" class="weekday">
          {{ day }}
        </div>
      </div>

      <!-- Days Grid -->
      <div class="days grid grid-cols-7 gap-0 mt-4 border-t border-l">
        <div
          v-for="(day, index) in days"
          :key="index"
          :class="[ 
            'flex flex-col justify-center items-center p-4 sm:p-6 cursor-pointer border-b border-r relative', 
            day.isCurrent ? 'bg-blue-100 text-gray-900' : '', 
            day.isPast ? 'text-gray-200' : '',
            selectedDate && day.date && selectedDate.toDateString() === day.date.toDateString() ? 'bg-red-500 text-white' : '',
            !day.date ? 'text-gray-300' : '',
            hasBookings(day) ? 'bg-yellow-200' : '',  // Highlight dates with bookings
            isFullyBooked(day) ? 'bg-gray-300 text-white cursor-not-allowed' : ''  // Fully booked date style
          ]"
          @click="handleDayClick(day)"
        >
          <!-- "Fully Booked" label just above the date -->
          <p v-if="isFullyBooked(day)" class="absolute top-[10px] text-xs font-bold text-slate-900">
            Fully Booked
          </p>

          <!-- Date -->
          <span v-if="day.date" class="text-sm sm:text-lg md:text-xl font-semibold">
            {{ day.date.getDate() }}
          </span>

            <!-- Special Booking label for Saturday and Sunday -->
            <p v-if="day.date && !day.isPast && (day.date.getDay() === 6 || day.date.getDay() === 0) && props.weekendFee !== null" class="text-xs font-bold text-red-600 text-left">
              +${{ props.weekendFee }} more
            </p>
        </div>
      </div>

      <!-- Today Button -->
      <div class="mt-4 text-left">
        <button
          @click="setToday"
          class="w-auto px-3 py-2 text-xs text-white bg-blue-500 rounded-md hover:bg-blue-600"
        >
          Today
        </button>
      </div>
    </div>
  </header>
</template>
