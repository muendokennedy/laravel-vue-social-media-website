<script setup>
defineProps({
    group: Object
})
</script>
<template>
    <div class="mb-3 hover:bg-gray-100 cursor-pointer">
        <div class="flex items-start gap-1 py-2 px-3">
            <img :src="group.thumbnail_url" alt="An image" class="w-8 rounded-full">
            <div>
                <h3 class="font-black text-xl">{{group.name}}</h3>
                <p class="text-xs text-gray-400">{{ group.description }}</p>
            </div>
        </div>
    </div>
</template>
<style scoped>

</style>
