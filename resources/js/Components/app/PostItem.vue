<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronDownIcon, PencilIcon, TrashIcon, EllipsisVerticalIcon, ArrowDownTrayIcon, PaperClipIcon } from '@heroicons/vue/20/solid'
import { ChatBubbleLeftRightIcon, ChatBubbleLeftEllipsisIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'
import PostUserInfo from '@/Components/app/PostUserInfo.vue'
import ReadMoreReadLess from '@/Components/app/ReadMoreReadLess.vue'
import { router, usePage } from '@inertiajs/vue3'
import { isImage } from '@/helpers.js'
import axiosClient from '@/axiosClient.js'
import InputTextarea from '@/Components/InputTextarea.vue'
import IndigoButton from '@/Components/app/IndigoButton.vue'
import EditDeleteDropdown from '@/Components/app/EditDeleteDropdown.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { ref } from 'vue'

const props = defineProps({
    post: Object,
})

const newCommentText = ref('')

const authUser = usePage().props.auth.user

const editingComment = ref(null)

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

const createComment = () => {
    axiosClient.post(route('post.comment.create', props.post), {
        comment: newCommentText.value
    }).then(({data}) => {
        newCommentText.value = ''
        props.post.latestComments.unshift(data)
        props.post.num_of_comments++
    })
}

const deleteComment = (comment) => {
    if(!window.confirm('Are sure you want to delete this comment?')){
        return false
    }
    axiosClient.delete(route('post.comment.delete', comment.id)).then(({data}) => {
        props.post.latestComments = props.post.latestComments.filter(c => c.id !== comment.id)
        props.post.num_of_comments--
    })
}

const startCommentEdit = (comment) => {
   editingComment.value = {
    id: comment.id,
    comment: comment.comment.replace(/<br\s*\/?>/gi, '\n')
   }
}

const updateComment = () => {
    axiosClient.put(route('post.comment.update', editingComment.value.id), editingComment.value).then(({data}) => {
        editingComment.value = null
        props.post.latestComments = props.post.latestComments.map((c) => {
            if(c.id === data.id){
                return data
            }
            return c
        })
    })
}

const sendCommentReaction = () => {
    axiosClient.post(route('comment.reaction', props.post), {
        reaction: 'like'
    }).then(({data}) => {
        props.post.current_user_has_reaction = data.current_user_has_reaction,
        props.post.num_of_reactions = data.num_of_reactions
    })
}

</script>
<template>
    <div class="bg-white rounded p-4 mb-3">
        <div class="flex justify-between mb-3">
            <PostUserInfo :post="post" class="mb-4"/>
            <EditDeleteDropdown :user="post.user" @edit="openEditModel" @delete="deletePost"/>
        </div>
        <div class="mb-3">
            <ReadMoreReadLess :content="post.body"/>
        </div>
        <div class="grid gap-3 mb-3"
        :class="[
            post.attachments.length === 1 ? 'grid-cols-1' :  'grid-cols-2'
        ]">
            <template v-for="(attachment, index) in post.attachments.slice(0,4)" :key="index">
                <div @click="openAttachment(index)" class="cursor-pointer group aspect-square bg-blue-100 flex items-center justify-center text-gray-500 relative">
                    <div v-if="index === 3 && post.attachments.length > 4" class="absolute inset-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl">
                        + {{ post.attachments.length - 4 }} more
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

                <DisclosurePanel class="mt-3">
                    <div class="flex gap-2 mb-3">
                        <a href="javascript:void(0)">
                                    <img :src="authUser.avatar_url" alt="" class="w-10 h-10 object-cover rounded-full border border-2 hover:border-blue-500 transition-all">
                        </a>
                        <div class="flex-1 flex gap-2">
                            <InputTextarea v-model="newCommentText" rows="1" class="w-full overflow-auto resize-none max-h-40" placeholder="Enter your comment here..."/>
                            <IndigoButton @click="createComment" class="w-40 h-10 text-nowrap">Add comment</IndigoButton>
                        </div>
                    </div>
                    <div>
                        <div>
                            <div v-for="(comment, index) in post.latestComments" :key="index" class="mb-4">
                                <div class="flex gap-2 justify-between">
                                    <div class="flex gap-2">
                                        <a href="javascript:void(0)">
                                                <img :src="comment.user.avatar_url" alt="" class="w-10 h-10 object-cover rounded-full border border-2 hover:border-blue-500 transition-all">
                                        </a>
                                        <div>
                                            <h4 class="font-bold">
                                                <a href="javascript:void(0)" class="hover:underline">
                                                {{ comment.user.name }}
                                                </a>
                                            </h4>
                                            <small  class="text-gray-400 text-xs">Updated {{ comment.updated_at }}</small>
                                        </div>
                                    </div>
                                    <EditDeleteDropdown :user="comment.user" @edit="startCommentEdit(comment)" @delete="deleteComment(comment)"/>
                                </div>
                                <div class="pl-12">
                                    <div v-if="editingComment && editingComment.id === comment.id" class="text-sm">
                                        <InputTextarea  v-model="editingComment.comment" rows="1" class="w-full overflow-auto resize-none max-h-40" placeholder="Enter your comment here..."/>
                                        <div class="flex gap-2 justify-end">
                                            <IndigoButton @click="updateComment" class="w-40 h-10 text-nowrap">Edit comment</IndigoButton>
                                            <DangerButton @click="editingComment = null">Cancel</DangerButton>
                                        </div>
                                    </div>
                                    <div v-else  class="text-sm">
                                        <ReadMoreReadLess :content="comment.comment"/>
                                    </div>
                                    <div class="mt-1 flex gap-2">
                                        <button class="flex items-center text-xs text-indigo-500 p-1 rounded-lg hover:bg-indigo-100">
                                            <HandThumbUpIcon class="size-3 mr-1"/>
                                            like
                                        </button>
                                        <button class="flex items-center text-xs text-indigo-500 p-1 rounded-lg hover:bg-indigo-100">
                                            <ChatBubbleLeftEllipsisIcon class="size-3 mr-1"/>
                                            reply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </DisclosurePanel>
            </Disclosure>
    </div>
</template>
<style scoped>

</style>
