<script setup>
defineProps({
    image: String,
    title: String,
})
</script>
<template>
    <div class="mb-3 hover:bg-gray-100 cursor-pointer">
        <div class="flex items-center gap-2 py-2 px-3">
            <img :src="image" alt="An image" class="w-8 rounded-full">
            <div>
                <h3 class="font-black">{{title}}</h3>
            </div>
        </div>
    </div>
</template>
<style scoped>

</style>
