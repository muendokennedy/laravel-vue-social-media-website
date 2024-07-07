<script setup>
import PostItem from '@/Components/app/PostItem.vue'
import PostModal from '@/Components/app/PostModal.vue'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3';
import AttachmentPreviewModal from '@/Components/app/AttachmentPreviewModal.vue'


defineProps({
    posts: {
        type: Object
    }
})

const authUser = usePage().props.auth.user

const editPost = ref({})
const previewAttachmentPost = ref({})

const showEditModel = ref(false)
const showAttachmentModal = ref(false)

const openEditModal = (post) => {
    editPost.value = post
    showEditModel.value = true
}

const openAttachmentPreviewModal = (post, index) => {
    previewAttachmentPost.value = { post, index }
    showAttachmentModal.value = true
}

const onModalhide = () => {
    editPost.value = {
        id: null,
        body: '',
        user: authUser
    }
}
</script>
<template>
    <div class="h-full overflow-auto">
        <PostItem v-for="post in posts" :key="post.id" :post="post" @edit-click="openEditModal" @attachment-click="openAttachmentPreviewModal"/>
        <PostModal :post="editPost" v-model="showEditModel" @hide="onModalhide"/>
        <AttachmentPreviewModal :attachments=previewAttachmentPost.post?.attachments
        v-model:index="previewAttachmentPost.index"
          v-model="showAttachmentModal" @hide="onModalhide"/>
    </div>
</template>
<style scoped>

</style>
