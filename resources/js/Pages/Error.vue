<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    errorTitle: String,
    body: String
})
</script>
<template>
    <AuthenticatedLayout>
        <div class="py-16 text-center">
            <h2 class="text-2xl">{{ errorTitle }}</h2>
            <div>{{ body }}</div>
        </div>
    </AuthenticatedLayout>
</template>
