<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronDownIcon, PencilIcon, TrashIcon, EllipsisVerticalIcon, ArrowDownTrayIcon, PaperClipIcon } from '@heroicons/vue/20/solid'
import { ChatBubbleLeftRightIcon, MapPinIcon, ChatBubbleLeftEllipsisIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'
import PostUserInfo from '@/Components/app/PostUserInfo.vue'
import ReadMoreReadLess from '@/Components/app/ReadMoreReadLess.vue'
import { router, usePage } from '@inertiajs/vue3'
import axiosClient from '@/axiosClient.js'
import InputTextarea from '@/Components/InputTextarea.vue'
import IndigoButton from '@/Components/app/IndigoButton.vue'
import EditDeleteDropdown from '@/Components/app/EditDeleteDropdown.vue'
import PostAttachments from '@/Components/app/PostAttachments.vue'
import CommentList from '@/Components/app/CommentList.vue'
import { ref, computed } from 'vue'
import UrlPreview from '@/Components/app/UrlPreview.vue'

const props = defineProps({
    post: Object,
})

const authUser = usePage().props.auth.user

const emit = defineEmits(['editClick', 'attachmentClick'])

const openEditModel = () => {
    emit('editClick', props.post)
}

const deletePost = () => {
    if(window.confirm('Are you sure you want to delete this post!')){
        router.delete(route('post.destroy', props.post), {
            preserveScroll: true
        })
    }
}

const pinUnpinPost = () => {
    axiosClient.post(route('post.pinupin', props.post))
    .then(response => {
        props.post.pinned = !props.post.pinned
    })
    .catch(error => {
        console.log(error)
    })
}

const openAttachment = (index) => {
    emit('attachmentClick', props.post, index)
}

const sendReaction = () => {
    axiosClient.post(route('post.reaction', props.post), {
        reaction: 'like'
    }).then(({data}) => {
        props.post.current_user_has_reaction = data.current_user_has_reaction,
        props.post.num_of_reactions = data.num_of_reactions
    })
}

const postBody = computed(() => {
    let content = props.post.body.replace(
        /(?:(\s+)|<p>)((#\w+)(?![^<]*<\/a>))/g,
            (match, group1, group2) => {
                const encodedGroup = encodeURIComponent(group2)
                return `${group1 || ''} <a href="/search/${encodedGroup}" class="hashtag">${group2}</a>`
            }
    )

    return content
})

</script>
<template>
    <div class="bg-white rounded p-4 mb-3">
        <div class="flex justify-between mb-3">
            <PostUserInfo :post="post" class="mb-4"/>
            <div class="flex items-center gap-2">
                <template v-if="post.pinned">
                    <MapPinIcon
                    :active="active"
                    class="mr-1 h-5 w-5 text-indigo-400"
                    aria-hidden="true"
                    />
                    Pinned
                </template>
                <EditDeleteDropdown :user="post.user" :post="post" @edit="openEditModel" @delete="deletePost" @pin="pinUnpinPost"/>
            </div>
        </div>
        <div class="mb-3">
            <ReadMoreReadLess :content="postBody"/>
            <UrlPreview :preview="post.preview" :url="post.preview_url"/>
        </div>
        <div class="grid gap-3 mb-3"
        :class="[
            post.attachments.length === 1 ? 'grid-cols-1' :  'grid-cols-2'
        ]">
        <PostAttachments :attachments="post.attachments" @attachmentClick="openAttachment"/>
        </div>
            <Disclosure>
                <div class="flex gap-2">
                    <button @click="sendReaction" class="text-gray-800 flex items-center justify-between bg-gray-100 hover:bg-gray-200 gap-1 flex-1 py-2 px-4 rounded-lg"
                    :class="[
                        post.current_user_has_reaction ? 'bg-sky-100 hover:bg-sky-200' : 'bg-gray-100 hover:bg-gray-200'
                    ]">
                    <div class="flex flex-1">
                        <span class="mr-2">{{ post.num_of_reactions }}</span>
                        {{ post.num_of_reactions == 1 ? 'Like' : 'Likes' }}
                    </div>
                    <div class="flex flex-1 gap-2">
                        <HandThumbUpIcon class="size-6"/>
                        {{post.current_user_has_reaction ? 'Unlike' : 'Like'}}
                    </div>
                    </button>
                    <DisclosureButton
                        class="text-gray-800 flex items-center justify-center bg-gray-100 hover:bg-gray-200 gap-1 flex-1 py-2 px-4 rounded-lg"
                        >
                        <ChatBubbleLeftRightIcon class="size-6"/>
                        <span class="mr-2">{{ post.num_of_comments }}</span>
                        {{ post.num_of_comments == 1 ? 'Comment' : 'Comments' }}
                    </DisclosureButton>
                </div>

                <DisclosurePanel class="p-2 comment-list mt-3 max-h-96 overflow-auto">
                    <CommentList :post="post" :data="{comments: post.comments}"/>
                </DisclosurePanel>
            </Disclosure>
    </div>
</template>
<style scoped>

</style>
