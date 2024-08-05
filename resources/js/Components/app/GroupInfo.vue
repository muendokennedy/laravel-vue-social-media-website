<script setup>

defineProps({
    group: {
        type: Object
    }
})
</script>
<template>
<div class="mb-3">
    <label for="name" class="font-bold">Group name:</label>
    <div>{{ group.name }}</div>
</div>
<div class="mb-3">
    <label for="about" class="font-bold">Group Description:</label>
    <div v-html="group.about"></div>
</div>
</template>
