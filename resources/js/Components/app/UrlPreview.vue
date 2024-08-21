<script setup>
defineProps({
    preview: Object,
    url: String
})
</script>
<template>
<a :href="url" v-if="preview && preview.title" target="_blank" class="block mt-4 border border-indigo-200 bg-indigo-50">
    <img :src="preview.image" class="max-w-full" :alt="preview.title">
    <div class="p-2">
        <h3 class="font-semibold">{{ preview.title }}</h3>
        <p class="text-sm">{{ preview.description }}</p>
    </div>
</a>
<a :href="url" v-else-if="url" target="_blank" class="block mt-4 border border-indigo-200 bg-indigo-50">
    <div class="p-2">
        {{ url }}
    </div>
</a>
</template>
