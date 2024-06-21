<script setup>
import {ref } from 'vue'
import InputTextarea from '@/Components/InputTextarea.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    placeHolder: String
})

const newPostForm = useForm({
    body: null
})

const postCreating = ref(false)

const submitForm = () => {
    newPostForm.post(route('post.store'), {
        onSuccess: () => {
            newPostForm.reset()
        }
    })
}

</script>
<template>
    <div class="p-4 bg-white rounded-lg border mb-3">
        <InputTextarea v-model="newPostForm.body" @click="postCreating = true" class="mb-3 w-full" placeholder="Click here to create new post"/>
        <div v-if="postCreating" class="flex justify-between">
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 relative">Attach Files
                <input type="file" class="absolute inset-0 opacity-0">
            </button>
            <button @click="submitForm" type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
        </div>
    </div>
</template>
<style scoped>

</style>
