<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChatBubbleLeftRightIcon, ChatBubbleLeftEllipsisIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'
import ReadMoreReadLess from '@/Components/app/ReadMoreReadLess.vue'
import IndigoButton from '@/Components/app/IndigoButton.vue'
import InputTextarea from '@/Components/InputTextarea.vue'
import EditDeleteDropdown from '@/Components/app/EditDeleteDropdown.vue'
import CommentList from '@/Components/app/CommentList.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import axiosClient from '@/axiosClient.js'


const props = defineProps({
    post: {
        type: Object
    },
    data: {
        type: Object
    },
    parentComment: {
        type: [Object, null],
        default: null
    }
})

const emit = defineEmits(['commentCreate', 'commentDelete'])

const authUser = usePage().props.auth.user

const newCommentText = ref('')

const editingComment = ref(null)

const createComment = () => {
    axiosClient.post(route('post.comment.create', props.post), {
        comment: newCommentText.value,
        parent_id: props.parentComment?.id ?? null
    }).then(({data}) => {
        newCommentText.value = ''
        props.data.comments.unshift(data)
        if(props.parentComment){
            props.parentComment.num_of_comments++
        }
        props.post.num_of_comments++
        emit('commentCreate', data)
    })
}

const deleteComment = (comment) => {
    if(!window.confirm('Are sure you want to delete this comment?')){
        return false
    }
    axiosClient.delete(route('comment.delete', comment.id)).then(({data}) => {

        const commentIndex = props.data.comments.findIndex(c => c.id === comment.id)
        props.data.comments.splice(commentIndex, 1)
        if(props.parentComment){
            props.parentComment.num_of_comments--
        }
        props.post.num_of_comments--
        emit('commentDelete', comment)
    })
}

const startCommentEdit = (comment) => {
   editingComment.value = {
    id: comment.id,
    comment: comment.comment.replace(/<br\s*\/?>/gi, '\n')
   }
}

const updateComment = () => {
    axiosClient.put(route('comment.update', editingComment.value.id), editingComment.value).then(({data}) => {
        editingComment.value = null
        props.data.comments = props.data.comments.map((c) => {
            if(c.id === data.id){
                return data
            }
            return c
        })
    })
}

const sendCommentReaction = (comment) => {
    axiosClient.post(route('comment.reaction', comment.id), {
        reaction: 'like'
    }).then(({data}) => {
        comment.current_user_has_reaction = data.current_user_has_reaction,
        comment.num_of_reactions = data.num_of_reactions
    })
}

const onCommentCreate = (comment) => {
    if(props.parentComment){
        props.parentComment.num_of_comments++
    }
    emit('commentCreate', comment)
}

const onDeleteComment = (comment) => {
    if(props.parentComment){
        props.parentComment.num_of_comments--
    }
    emit('commentDelete', comment)
}

</script>

<template>
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
            <div v-for="(comment, index) in data.comments" :key="index" class="mb-4">
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
                    <Disclosure>
                        <div class="mt-1 flex gap-2">
                            <button @click="sendCommentReaction(comment)" class="flex items-center text-xs text-indigo-500 p-1 rounded-lg"
                            :class="[
                                    comment.current_user_has_reaction ? 'bg-indigo-50 hover:bg-indigo-100' : 'hover:bg-indigo-50'
                                ]"
                            >
                                <HandThumbUpIcon class="size-3 mr-1"/>
                                <span class="mr-2">{{ comment.num_of_reactions }}</span>
                                {{comment.current_user_has_reaction ? 'Unlike' : 'like'}}
                            </button>
                            <DisclosureButton class="flex items-center text-xs text-indigo-500 p-1 rounded-lg hover:bg-indigo-100">
                                <ChatBubbleLeftEllipsisIcon class="size-3 mr-1"/>
                                <span class="mr-2">{{ comment.num_of_comments }}</span>
                                {{ comment.num_of_comments == 1 ? 'Comment' : 'Comments' }}
                            </DisclosureButton>
                        </div>
                        <DisclosurePanel class="mt-3">
                            <CommentList :post="post" :data="{comments: comment.comments}" :parent-comment="comment" @comment-create="onCommentCreate" @comment-delete="onDeleteComment"/>
                        </DisclosurePanel>
                    </Disclosure>
                </div>
            </div>
        </div>
    </div>
</template>
