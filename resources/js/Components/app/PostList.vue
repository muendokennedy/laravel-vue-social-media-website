<script setup>
import PostItem from '@/Components/app/PostItem.vue'
import PostModal from '@/Components/app/PostModal.vue'
import { onMounted, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AttachmentPreviewModal from '@/Components/app/AttachmentPreviewModal.vue'
import axiosClient from '@/axiosClient.js'


const props = defineProps({
    posts: {
        type: Array
    }
})

const page = usePage()

const allPosts =  ref({
    data: page.props.posts.data,
    next: page.props.posts.links.next
})

const authUser = usePage().props.auth.user

const editPost = ref({})
const previewAttachmentPost = ref({})

const showEditModel = ref(false)
const showAttachmentModal = ref(false)
const loadMoreIntersect = ref(null)

onMounted(() => {
    const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting
        && loadMore()
    ), {
        rootMargin: '-250px 0px 0px 0px'
    })

    observer.observe(loadMoreIntersect.value)

})

const loadMore = () => {

    if(!allPosts.value.next){
        return
    }
    axiosClient.get(allPosts.value.next).then(({data}) => {
        allPosts.value.data = [...allPosts.value.data, ...data.data]
        allPosts.value.next = data.links.next
    })
}

watch(() => page.props.posts, () => {
    console.log('The watch happened')
    allPosts.value = {
        data: page.props.posts.data,
        next: page.props.posts.links.next
    }
}, {deep: true})

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
        <PostItem v-for="post in allPosts.data" :key="post.id" :post="post" @edit-click="openEditModal" @attachment-click="openAttachmentPreviewModal"/>
        <div ref="loadMoreIntersect"></div>
        <PostModal :post="editPost" v-model="showEditModel" @hide="onModalhide"/>
        <AttachmentPreviewModal :attachments=previewAttachmentPost.post?.attachments
        v-model:index="previewAttachmentPost.index"
          v-model="showAttachmentModal" @hide="onModalhide"/>
    </div>
</template>
