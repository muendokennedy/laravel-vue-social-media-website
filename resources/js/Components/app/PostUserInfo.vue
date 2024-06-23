<script setup>
import { onMounted, ref, watch } from 'vue';


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
    if(props.post.updated_at > props.post.created_at)
    postUpdatedStatus.value = true
})

watch(() => props.post, () => {
    postUpdatedStatus.value = true
})
</script>
<template>
        <div class="flex items-center gap-2">
        <a href="javascript:void(0)">
                    <img :src="post.user.avatar_url" alt="" class="w-10 rounded-full border border-2 hover:border-blue-500 transition-all">
        </a>
        <div>
            <h4 class="font-bold">
                <a href="javascript:void(0)" class="hover:underline">
                {{ post.user.name }}
                </a>
                <template v-if="post.group">
                    >
                    <a href="javascript:void(0)" class="hover:underline">
                        {{ post.group.name }}
                    </a>
                </template>
            </h4>
            <small v-if="showTime && postUpdatedStatus === false" class="text-gray-400">Created {{ post.created_at }}</small>
            <small v-else-if="showTime && postUpdatedStatus === true" class="text-gray-400">Created {{ post.created_at }}, Updated {{ post.updated_at }}</small>
        </div>
    </div>
</template>
