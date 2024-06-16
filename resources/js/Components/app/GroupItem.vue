<script setup>
defineProps({
    image: String,
    title: String,
    description: String
})
</script>
<template>
    <div class="mb-3 hover:bg-gray-100 cursor-pointer">
        <div class="flex items-start gap-1 py-2 px-3">
            <img :src="image" alt="An image" class="w-8 rounded-full">
            <div>
                <h3 class="font-black text-xl">{{title}}</h3>
                <p class="text-xs text-gray-400">{{ description }}</p>
            </div>
        </div>
    </div>
</template>
<style scoped>

</style>
