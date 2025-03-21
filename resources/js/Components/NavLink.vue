<script setup>
defineProps({
    routeName: String,
    componentName: String,
});
</script>

<template>
    <Link
        :href="route(routeName)"
        class="bg-white h-20 text-indigo-700 font-bold flex items-center px-2 transition-colors hover:text-indigo-600 hover:bg-gray-100 "
        :class="{ 'text-indigo-700 font-bold flex items-center ': $page.component === componentName }"
    >
        <slot />
    </Link>
</template>
