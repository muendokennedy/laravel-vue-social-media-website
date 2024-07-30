<script setup>
import { onMounted, ref, watch } from 'vue';
import {Link} from '@inertiajs/vue3'
import { ChevronRightIcon } from '@heroicons/vue/24/solid'

const postUpdatedStatus = ref(false)


const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    showTime: {
        type: Boolean,
        default: true
    }
})

onMounted(() => {
    if(props.post.time_difference){
        postUpdatedStatus.value = true
    }
})

watch(() => props.post.body, () => {
    postUpdatedStatus.value = true
})
</script>
<template>
        <div class="flex items-center gap-2">
        <Link :href="route('profile.home', post.user)">
                    <img :src="post.user.avatar_url" alt="" class="w-10 h-10 rounded-full border border-2 hover:border-blue-500 transition-all">
        </Link>
        <div>
            <h4 class="font-bold">
                <Link :href="route('profile.home', post.user)" class="hover:underline">
                {{ post.user.name }}
                </Link>
                <ChevronRightIcon class="size-4 inline"/>
                <template v-if="post.group">
                    <Link :href="route('group.profile', post.group)" class="hover:underline">
                        {{ post.group.name }}
                    </Link>
                </template>
            </h4>
            <small v-if="showTime && postUpdatedStatus === false" class="text-gray-400">Created {{ post.created_at }}</small>
            <small v-else-if="showTime && postUpdatedStatus === true" class="text-gray-400">Created {{ post.created_at }}, Updated {{ post.updated_at }}</small>
        </div>
    </div>
</template>
