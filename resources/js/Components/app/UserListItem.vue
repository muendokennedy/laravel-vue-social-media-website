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
    },
    showRoleDropdown: {
        type: Boolean,
        default: false
    },
    disableRoleDropdown: {
        type: Boolean,
        default: false
    }
})

defineEmits(['approve', 'reject', 'roleChange'])

</script>
<template>
    <div class="mb-3 bg-white border-2 border-transparent hover:border-indigo-500">
        <div class="flex items-center gap-2 py-2 px-3">
            <Link :href="route('profile.home', user.username)">
                <img :src="user.avatar_url" alt="An image" class="w-8 rounded-full">
            </Link>
            <div class="flex justify-between flex-1">
                <Link :href="route('profile.home', user.username)">
                    <h3 class="font-black hover:underline">{{user.name}}</h3>
                </Link>
                <div v-if="forApprove" class="flex gap-1">
                    <button class="text-xs py-1 px-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white capitalize" @click.prevent.stop="$emit('approve', user)">Approve</button>
                    <button class="text-xs py-1 px-2 rounded bg-red-500 hover:bg-red-600 text-white capitalize" @click.prevent.stop="$emit('reject', user)">reject</button>
                </div>
                <div v-if="showRoleDropdown">
                    <select :disabled="disableRoleDropdown" @change="$emit('roleChange', user, $event.target.value)" class="rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                        <option value="admin" :selected="user.role === 'admin'">Admin</option>
                        <option value="user" :selected="user.role === 'user'">User</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>

</style>
