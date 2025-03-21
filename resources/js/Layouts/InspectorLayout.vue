<!-- resources/js/Layouts/InspectorLayout.vue -->
<script setup>
import { ref } from "vue";
import NavLink from "../Components/NavLink.vue";

const isMobileMenuOpen = ref(false);
const logoImage = "/images/logo.png";
const favicon = new URL('../assets/images/favicon.ico', import.meta.url).href;

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<template>
  <Head>
        <!-- <link rel="icon" type="image/x-icon" :href="favicon" /> -->
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico" />
  </Head>
  
  <div class="flex flex-col min-h-screen">
    <header class="bg-white h-20 w-full shadow fixed top-0 z-50 flex justify-between items-center px-3">
      <nav class="container mx-auto px-4 py-3 flex justify-between items-center">
        <NavLink routeName="dashboard">
          <img :src="logoImage" alt="Admin Logo" class="h-12">
        </NavLink>
        
        <div class="hidden md:flex space-x-6">
          <NavLink routeName="dashboard">Dashboard</NavLink>
          <NavLink routeName="booking.list">Booking List</NavLink>
          <NavLink routeName="dashboard.settings">Settings</NavLink>
          <NavLink
            routeName="logout"
            method="post"
            as="button"
        >Logout</NavLink>
        </div>

        <button @click="toggleMobileMenu" class="md:hidden">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </nav>
    </header>

    <!-- Mobile Menu -->
    <div v-if="isMobileMenuOpen" class="md:hidden bg-white shadow-lg">
      <div class="px-4 py-2 space-y-2">
        <NavLink routeName="dashboard">Dashboard</NavLink>
        <NavLink routeName="dashboard">Booking List</NavLink>
        <NavLink routeName="dashboard">Settings</NavLink>
        <NavLink
            routeName="logout"
            method="post"
            as="button"
        >Logout</NavLink>
      </div>
    </div>

    <main class="flex-1 container mx-auto px-4 py-6 mt-20">
      <slot />
    </main>
  </div>
</template>
