<script setup>
import { Link } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'

defineProps({
    user: {
        type: Object
    },
    forApprove: {
        type: Boolean,
        default: false
    }
})

defineEmits(['approve', 'reject'])

</script>
<template>
    <div class="mb-3 bg-white border-2 border-transparent hover:border-indigo-500 cursor-pointer">
        <Link :href="route('profile.home', user.username)" class="flex items-center gap-2 py-2 px-3">
            <img :src="user.avatar_url" alt="An image" class="w-8 rounded-full">
            <div class="flex justify-between flex-1">
                <h3 class="font-black">{{user.name}}</h3>
                <div class="flex gap-1">
                    <button class="text-xs py-1 px-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white capitalize" v-if="forApprove" @click.prevent.stop="$emit('approve', user)">Approve</button>
                    <button class="text-xs py-1 px-2 rounded bg-red-500 hover:bg-red-600 text-white capitalize" v-if="forApprove" @click.prevent.stop="$emit('reject', user)">reject</button>
                </div>
            </div>
        </Link>
    </div>
</template>
<style scoped>

</style>
