<template>
    <Head title=" | 404 - Not Found" />
    <div class="bg-gray-200 text-center mb-3 -mt-3">
    </div>

    <div class="flex flex-wrap mx-2 justify-center">
        <!-- 404 - Not Found Header -->
        <div class="flex-none w-full md:w-1/2 mb-2 md:mb-4 p-4 rounded-lg min-w-[220px]">
        </div>

        <!-- Update User Details -->
        <div class="flex-none w-full md:w-4/6 mt-6 md:mt-4 p-4 mx-2 bg-orange-300 rounded-lg shadow-md min-w-[220px]">
            <div class="not-found justify-center p-8 mt-8 mb-8">
        <h1 class="text-4xl font-bold text-center mt-5">404 - Page Not Found</h1>
        <p class="text-2xl font-bold text-center">Sorry, the page you are looking for does not exist.</p>
        <h1 class="text-5xl font-bold text-center mt-5">{{ companyName.toUpperCase() }}.</h1>

        <div class="flex-none w-full md:w-1/2 mb-2 md:mb-4 p-4 rounded-lg min-w-[220px]">
        </div>

    </div>


        </div>
    </div>
</template>

<script setup>
import Main from '@/Layouts/Main.vue';

// Access the APP_NAME environment variable directly
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name'; // Default if not set

// No export default needed
// You can define the component name using defineComponent if necessary, but it's not required for script setup
</script>
