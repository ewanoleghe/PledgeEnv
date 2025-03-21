<!-- resources/js/Layouts/AdminLayout.vue -->
<script setup>
import { ref } from "vue";
// import { Head, Link } from '@inertiajs/vue3';
import NavLink from "../Components/NavLink.vue";

const isMobileMenuOpen = ref(false);
const logoImage = "/images/logo.png";

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};


</script>

<template>
  <Head>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico" />
    <title>Admin Dashboard</title>
  </Head>

  <div class="flex flex-col min-h-screen bg-gray-50">
    <header class="bg-white h-20 w-full shadow fixed top-0 z-50 flex justify-between items-center px-3">
      <nav class="container mx-auto px-4 py-3 flex justify-between items-center">
        <Link :href="route('admin.dashboard')">
          <img :src="logoImage" alt="Admin Logo" class="h-12">
        </Link>
        
        <div class="hidden md:flex space-x-6">
          <NavLink routeName="admin.dashboard">Dashboard</NavLink>
          <NavLink routeName="admin.inspectors.index">Inspectors</NavLink>
          <NavLink routeName="admin.bookings">Bookings</NavLink>
          <NavLink routeName="admin.scheduled_bookings">Calendar</NavLink>
          <NavLink routeName="admin.dashboard">Settings</NavLink>
          <NavLink
                            :href="route('admin.logout')"
                            method="post"
                            as="button"
                            >Logout</NavLink
                        >
        </div>

        <button @click="toggleMobileMenu" class="md:hidden">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </nav>
    </header>

    <!-- Mobile Menu -->
    <div v-show="isMobileMenuOpen" class="md:hidden bg-white shadow-lg transition duration-300 ease-in-out">
      <div class="px-4 py-2 space-y-2">
        <NavLink routeName="admin.dashboard" class="block">Dashboard</NavLink>
        <NavLink routeName="admin.inspectors.index">Inspectors</NavLink>
        <NavLink routeName="admin.dashboard"  class="block">Bookings</NavLink>
        <NavLink routeName="admin.dashboard"  class="block">Settings</NavLink>
        <NavLink
                            :href="route('admin.logout')"
                            method="post"
                            as="button"
                            >Logout</NavLink
                        >
      </div>
    </div>

    <main class="flex-1 container mx-auto px-4 py-6 mt-20">
      <slot />
    </main>
  </div>
</template>