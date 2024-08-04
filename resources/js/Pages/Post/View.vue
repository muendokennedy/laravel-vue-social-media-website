<script setup>
import PostItem from '@/Components/app/PostItem.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    post: {
        type: Object
    }
})
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-8">
            <PostItem :post="post"/>
        </div>
    </AuthenticatedLayout>
</template>
