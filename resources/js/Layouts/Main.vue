<script setup>
import { ref } from "vue";
import { switchTheme } from "../theme";
import NavLink from "../Components/NavLink.vue";
import Footer from "../Components/Footer.vue";  

const favicon = new URL('../assets/images/favicon.ico', import.meta.url).href;

// Define image reference
// const homeImg = "/images/home.jpg";  // Direct reference to the public folder image

// Main Logo
const logoImage = "/images/logo.png";

// Reactive state to control mobile menu visibility and dropdown visibility
const isMobileMenuOpen = ref(false);
const isServicesDropdownOpen = ref(false);

// Toggle functions
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

// const toggleServicesDropdown = () => {
//   isServicesDropdownOpen.value = !isServicesDropdownOpen.value;
// };

const toggleServicesDropdown = (event) => {
  event.preventDefault();  // Prevents page navigation when using href="#"
  isServicesDropdownOpen.value = !isServicesDropdownOpen.value;  // Toggle the state
};

</script>

<template>
  <Head>
        <!-- <link rel="icon" type="image/x-icon" :href="favicon" /> -->
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico" />
  </Head>

  <div class="flex flex-col min-h-screen">
    <header class="bg-white h-20 w-full shadow fixed top-0 z-50 flex justify-between items-center px-3">
      <!-- Logo -->
      <NavLink routeName="home" componentName="Home" class="ml-1">
        <img :src="logoImage" alt="Logo" class="h-12 sm:h-8 md:h-12 lg:h-16">
      </NavLink>
      
      <!-- Navigation Menu Centered with spaced links -->
      <div class="hidden sm:flex flex-1 justify-center space-x-6">
        <NavLink routeName="home" componentName="Home">Home</NavLink>
        <NavLink routeName="about" componentName="About">About</NavLink>

        <!-- Services Link with Dropdown -->
        <div class="relative">
            <NavLink 
            @click="toggleServicesDropdown"
            href="#"  >
            Services 
            <!-- Conditional rendering for the icon based on dropdown state -->
                <i :class="[
                isServicesDropdownOpen ? 'fa-solid fa-angles-up' : 'fa-solid fa-angles-down',
                'ml-2', 
                isServicesDropdownOpen ? 'text-red-600' : ''
                ]">
                </i>
            </NavLink>
          <!-- Dropdown Menu (Visible when isServicesDropdownOpen is true) -->
          <div v-if="isServicesDropdownOpen" class="absolute left-0 mt-2 bg-white shadow-lg rounded-md w-40 z-50">
            <NavLink routeName="home" componentName="Lead Inspection" class="block px-4 py-2">Lead Inspection</NavLink>
            <NavLink routeName="home" componentName="Asbestos Management" class="block px-4 py-2">Asbestos Management</NavLink>
            <NavLink routeName="home" componentName="Mold Assessment" class="block px-4 py-2">Mold Assessment</NavLink>
            <NavLink routeName="home" componentName="Radon Control" class="block px-4 py-2">Radon Control</NavLink>
            <NavLink routeName="home" componentName="Sewer Inspection & Cleaning" class="block px-4 py-2">Sewer Inspection & Cleaning</NavLink>
            <NavLink routeName="home" componentName="Residential Tank Sweep" class="block px-4 py-2">Residential Tank Sweep</NavLink>
            <NavLink routeName="home" componentName="Remediation & Consulting" class="block px-4 py-2">Remediation & Consulting</NavLink>
          </div>
        </div>

        <NavLink routeName="contact" componentName="Contact Us">Contact Us</NavLink>
      </div>

      <!-- Register and Theme switcher -->
      <div class="flex items-center space-x-5 -ml-4">
        <NavLink routeName="home" componentName="Customer Portal" class="text-xs sm:text-base">Track Order</NavLink>
        <NavLink routeName="schedule.index" componentName="Booking / Schedule" class="text-xs sm:text-base">Booking & Schedule</NavLink>
        <button 
          @click="switchTheme"
          class="hover:bg-slate-700 w-6 h-6 grid place-items-center rounded-full hover:outline outline-1 outline-white dark:text-gray-800 dark:hover:bg-gray-500"
          aria-label="Switch Theme">
          <i class="fa-solid fa-circle-half-stroke"></i>
        </button>
      </div>

      <!-- Mobile Menu Button -->
      <div class="sm:hidden h-full flex items-center -ml-1">
        <button 
          @click="toggleMobileMenu"
          class="flex items-center justify-center h-8 w-8 rounded transition-colors hover:bg-indigo-100 hover:text-indigo-600 dark:bg-gray-100 dark:text-gray-600">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
      </div>
    </header>

    <main class="flex-1 p-4 mt-20">
      <!-- Mobile Menu (Visible when isMobileMenuOpen is true) -->
      <div v-if="isMobileMenuOpen" class="sm:hidden flex flex-col items-center space-y-4 bg-white py-4 shadow-md mb-6">
        <NavLink routeName="home" componentName="Home">Home</NavLink>
        <NavLink routeName="about" componentName="About">About</NavLink>

        <!-- Mobile Services Dropdown -->
        <div class="relative">
            <NavLink 
            @click="toggleServicesDropdown"
            href="#" >
            Services <!-- Conditional rendering for the icon based on dropdown state -->
                <i :class="[
                isServicesDropdownOpen ? 'fa-solid fa-angles-up' : 'fa-solid fa-angles-down',
                'ml-2', 
                isServicesDropdownOpen ? 'text-red-600' : ''
                ]"></i>
            </NavLink>
          <div v-if="isServicesDropdownOpen" class="absolute left-0 mt-2 bg-white shadow-lg rounded-md w-40 z-50">
            <NavLink routeName="home" componentName="Lead Inspection" class="block px-4 py-2">Lead Inspection</NavLink>
            <NavLink routeName="home" componentName="Asbestos Management" class="block px-4 py-2">Asbestos Management</NavLink>
            <NavLink routeName="home" componentName="Mold Assessment" class="block px-4 py-2">Mold Assessment</NavLink>
            <NavLink routeName="home" componentName="Radon Control" class="block px-4 py-2">Radon Control</NavLink>
            <NavLink routeName="home" componentName="Sewer Inspection & Cleaning" class="block px-4 py-2">Sewer Inspection & Cleaning</NavLink>
            <NavLink routeName="home" componentName="Residential Tank Sweep" class="block px-4 py-2">Residential Tank Sweep</NavLink>
            <NavLink routeName="home" componentName="Remediation & Consulting" class="block px-4 py-2">Remediation & Consulting</NavLink>
          </div>
        </div>

        <NavLink routeName="contact" componentName="Contact Us">Contact Us</NavLink>
      </div>

      <slot />
    </main>

     <!-- Footer Component -->
     <Footer /> <!-- Include Footer here -->
  </div>
</template>
