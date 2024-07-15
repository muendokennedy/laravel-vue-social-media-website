<script setup>
import { ArrowDownTrayIcon } from '@heroicons/vue/20/solid'
import { PaperClipIcon } from '@heroicons/vue/20/solid'
import { isImage } from '@/helpers.js'

defineProps({
    attachments: {
        type: Array
    }
})

defineEmits(['attachmentClick']);

</script>

<template>
    <template v-for="(attachment, index) in attachments.slice(0,4)" :key="index">
        <div @click="$emit('attachmentClick', index)" class="cursor-pointer group aspect-square bg-blue-100 flex items-center justify-center text-gray-500 relative">
            <div v-if="index === 3 && attachments.length > 4" class="absolute inset-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl">
                + {{ attachments.length - 4 }} more
            </div>
            <a @click.stop :href="route('post.download', attachment)" class="flex z-10 opacity-0 group-hover:opacity-100 transition-all items-center text-gray-100 justify-center w-8 h-8 bg-gray-700 hover:bg-gray-800 rounded absolute top-2 right-2 cursor-pointer">
                <ArrowDownTrayIcon class="size-4"/>
            </a>
            <img v-if="isImage(attachment)" :src="attachment.url" alt="" class="object-cover w-full h-full">
            <div v-else class="flex flex-col items-center justify-center">
                <PaperClipIcon class="size-10 mr-2"/>
                <small>{{ attachment.name }}</small>
            </div>
        </div>
    </template>
</template>
