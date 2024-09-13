<script setup>
import {ref } from 'vue'
import InputTextarea from '@/Components/InputTextarea.vue';
import PostModal from '@/Components/app/PostModal.vue'
import { usePage } from '@inertiajs/vue3';

defineProps({
    placeHolder: String,
    group: {
        type: Object,
        default: null
    }
})

const authUser = usePage().props.auth.user

const showModal = ref(false)

const newPost = ref({
    id: null,
    body: '',
    user: authUser
})

const showCreatePostModal = () => {
    showModal.value = true
}

</script>
<template>
    <div class="p-4 bg-white dark:bg-slate-950 dark:border-slate-900 rounded-lg border mb-3">
        <div @click="showCreatePostModal" class="py-2 px-3 border-2 rounded-md border-gray-200 text-gray-500 dark:border-slate-900 cursor-pointer mb-3 w-full">Click here to create new post</div>
        <PostModal :post="newPost" :group="group" v-model="showModal"/>
    </div>
</template>
<style scoped>

</style>
