<script setup>

defineProps({
    form: {
        type: Object
    }
})
</script>
<template>
<div class="mb-3">
    <label for="name" class="font-bold">Group name:</label>
    <div>{{ form.name }}</div>
</div>
<div class="mb-3">
    <label for="about" class="font-bold">Group Description:</label>
    <div>{{ form.about }}</div>
</div>
</template>
