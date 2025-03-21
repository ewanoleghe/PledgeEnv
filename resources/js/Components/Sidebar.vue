<!-- resources/js/Components/Sidebar.vue -->
<template>
    <div class="bg-gray-800 text-white w-64 h-full">
      <div class="flex items-center justify-center py-6">
        <img :src="logoImage" alt="Logo" class="h-12">
      </div>
  
      <nav class="px-4">
        <ul class="space-y-4">
          <li>
            <NavLink routeName="admin.dashboard" class="block py-2">Dashboard</NavLink>
          </li>
          <li>
            <NavLink routeName="admin.dashboard" class="block py-2">Users</NavLink>
          </li>
          <li>
            <NavLink routeName="admin.dashboard" class="block py-2">Bookings</NavLink>
          </li>
          <li>
            <NavLink routeName="admin.dashboard" class="block py-2">Settings</NavLink>
          </li>
          <li>
            <NavLink
                            :href="route('admin.logout')"
                            method="post"
                            as="button"
                            >Logout</NavLink
                        >
          </li>
        </ul>
      </nav>
    </div>
  </template>
  
  <script setup>
  import NavLink from './NavLink.vue';
  
  const logoImage = "/images/logo.png";
  </script>
  
  <style scoped>
  /* Add custom styles for your sidebar if necessary */
  </style>
  