<script setup>
import { Link } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'

defineProps({
    user: {
        type: Object
    }
})

defineEmits(['approve'])

</script>
<template>
    <div class="mb-3 bg-white border-2 border-transparent hover:border-indigo-500 cursor-pointer">
        <Link :href="route('profile.home', user.username)" class="flex items-center gap-2 py-2 px-3">
            <img :src="user.avatar_url" alt="An image" class="w-8 rounded-full">
            <div class="flex justify-between flex-1">
                <h3 class="font-black">{{user.name}}</h3>
                <PrimaryButton @click.prevent.stop="$emit('approve', user)">Approve</PrimaryButton>
            </div>
        </Link>
    </div>
</template>
<style scoped>

</style>
