<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    group: Object
})
</script>
<template>
    <div class="mb-3 hover:bg-gray-100 cursor-pointer">
        <Link :href="route('group.profile', {group: group.slug})" class="flex items-start gap-1 py-2 px-3">
            <img :src="group.thumbnail_url" alt="An image" class="w-8 rounded-full">
            <div class="flex-1">
                <div class="flex justify-between">
                    <h3 class="font-black text-xl">{{group.name}}</h3>
                    {{ group.status === 'approved' ? (group.role === 'admin' ? group.role : '') : 'pending' }}
                </div>
                <p class="text-xs text-gray-400">{{ group.description }}</p>
            </div>
        </Link>
    </div>
</template>
<style scoped>

</style>
