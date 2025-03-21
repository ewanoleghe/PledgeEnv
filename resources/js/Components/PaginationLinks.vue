<script setup>
defineProps({
    paginator: {
        type: Object,
        required: true,
    },
});

const makeLabel = (label) => {
    if (label.includes("Previous")) {
        return "<<";
    } else if (label.includes("Next")) {
        return ">>";
    } else {
        return label.replace(/<\/?[^>]+(>|$)/g, ""); // Remove any HTML tags
    }
};
</script>

<template>
    <div class="flex justify-between items-start mt-1">
        <div class="flex items-center rounded-md overflown-hidden shadow-lg">
            <div v-for="link in paginator.links" :key="link.url">
                <component
                :is="link.url ? 'Link' : 'span'"
                :href="link.url"
                class="border-x border-slate-50 w-12 h-12 grid place-items-center bg-gray-200"
                :class="{
                    'hover:bg-slate-300' : link.url,
                    'text-zinc-400' : !link.url,
                    'font-bold text-red-700 text-2xl' : link.active 
                }"
                >{{ makeLabel(link.label) }}</component>

            </div>

        </div>
        <p class="text-skate-600 text-sm">Showing {{ paginator.from }} to {{ paginator.to }} of {{ paginator.total }} results</p>

    </div>
</template>