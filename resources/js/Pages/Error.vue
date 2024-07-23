<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    title: String,
    body: String
})
</script>
<template>
    <AuthenticatedLayout>
        <div class="py-16 text-center">
            <h2 class="text-2xl">{{ title }}</h2>
            <div>{{ body }}</div>
        </div>
    </AuthenticatedLayout>
</template>
