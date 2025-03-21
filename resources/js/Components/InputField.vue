<script setup>
const model = defineModel();
defineProps({
    label: String,
    icon: String,
    placeholder: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "text",
    },
});
</script>

<template>
    <div>
        <label
            :for="label"
            class="block text-sm font-medium text-slate-700 dark:text-slate-300"
        >
            {{ label }}
        </label>

        <div class="relative mt-1 rounded-md input-field">
            <!-- Icon positioned on the left -->
            <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
            >
                <span class="grid place-content-center text-sm text-slate-400">
                    <i :class="`fa-solid fa-${icon}`"></i>
                </span>
            </div>

            <!-- Input field with padding on the left to avoid overlapping -->
            <input
                :type="type"
                :name="label"
                :placeholder="placeholder"
                v-model="model"
                class="mt-1 p-2 pl-10 block w-full rounded-md text-sm dark:text-slate-900 border-slate-300 outline-0 focus:ring-1 focus:ring-inset focus:ring-indigo-400 focus:border-indigo-400 placeholder:text-slate-400"
            />
        </div>
    </div>
</template>
