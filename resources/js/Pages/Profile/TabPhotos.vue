<script setup>
import { isImage } from '@/helpers'
import { ArrowDownTrayIcon } from '@heroicons/vue/20/solid'
import { PaperClipIcon } from '@heroicons/vue/20/solid'
import { ref } from 'vue'
import AttachmentPreviewModal from '@/Components/app/AttachmentPreviewModal.vue'

defineProps({
    photos: {
        type: Array
    }
})

const currentPhotoIndex = ref(0)
const showModal = ref(false)



const openPhoto = (index) => {
    currentPhotoIndex.value = index
    showModal.value = true
}
</script>
<template>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
        <template v-for="(attachment, index) in photos" :key="index">
            <div @click="openPhoto(index)" class="cursor-pointer group aspect-square bg-blue-100 flex items-center justify-center text-gray-500 relative">
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
    </div>
    <AttachmentPreviewModal :attachments="photos || []"
        v-model:index="currentPhotoIndex"
          v-model="showModal"/>
</template>
