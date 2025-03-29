<script setup>
  import NavLink from "../Components/NavLink.vue";

  // Reactive variable to store the current year
  const currentYear = new Date().getFullYear();

  // Access the APP_NAME environment variable directly
  const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name'; // Default if not set

  // Main Logo
  const logoImage = "/images/logo.png";

  // GitHub link (you can modify this to point to your GitHub profile)
  // const githubLink = "https://github.com/yourusername";
</script>

<template>
  <footer class="bg-gray-700 text-white py-2 mt-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-2">
      <div class="flex justify-between items-center">
        <!-- Logo or Branding -->
        <div>
          <NavLink routeName="home" componentName="Home" class="text-sm font-bold">
            <img :src="logoImage" alt="Logo" class="h-4 sm:h-2 md:h-4 lg:h-6">
          </NavLink>
        </div>

        <!-- Copyright -->
        <div class="flex text-center mt-4 text-sm">
          <Link :href="route('login')" componentName="Contact" class="mr-2">Account</Link> | 
          <Link :href="route('contact')" componentName="Contact" class="mr-2 ml-2">Contact</Link> | 
          <Link :href="route('home')" componentName="Privacy Policy" class="ml-2">Privacy Policy</Link>
        </div> 

        <div class="text-center mt-4 text-sm">
          <p>&copy; {{ currentYear }} {{ companyName }}. All rights reserved.</p>
        </div>

        <!-- Social Media Icons -->
        <div class="flex space-x-4">
          <a href="#" target="_blank" class="text-white hover:text-indigo-300">
            <i class="fa-brands fa-facebook"></i>
            <span class="sr-only">Facebook</span>
          </a>
          <a href="#" target="_blank" class="text-white hover:text-indigo-300">
            <i class="fa-brands fa-twitter"></i>
            <span class="sr-only">Twitter</span>
          </a>
          <a href="#" target="_blank" class="text-white hover:text-indigo-300">
            <i class="fa-brands fa-instagram"></i>
            <span class="sr-only">Instagram</span>
          </a>
          <a href="#" target="_blank" class="text-white hover:text-indigo-300">
            <i class="fa-brands fa-linkedin"></i>
            <span class="sr-only">LinkedIn</span>
          </a>
        </div>
      </div>

      <!-- Developer Section -->
      <div class="text-center mt-4 text-sm">
        <p><a href="https://ewanoleghe.github.io/" target="_blank" class="text-white hover:text-indigo-300">Design and Development: EnY 
          <i class="fa-brands fa-github"></i> GitHub</a></p>
      </div>
    </div>
  </footer>
</template>

