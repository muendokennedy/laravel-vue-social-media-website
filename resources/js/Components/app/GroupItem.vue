<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    group: Object
})
</script>
<template>
    <div class="mb-3 hover:bg-gray-100 dark:hover:bg-slate-800 cursor-pointer">
        <Link :href="route('group.profile', {group: group.slug})" class="flex items-start gap-1 py-2 px-3">
            <img :src="group.thumbnail_url" alt="An image" class="w-10 h-10 rounded-full object-cover">
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="font-black text-xl">{{group.name}}</h3>
                </div>
                <span class="text-xs">
                    {{ group.status === 'approved' ? (group.role === 'admin' ? group.role : '') : 'pending' }}
                </span>
                <!-- <p class="text-xs text-gray-400" v-html="group.description"></p> -->
            </div>
        </Link>
    </div>
</template>
<style scoped>

</style>
