<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChatBubbleLeftRightIcon, ChatBubbleLeftEllipsisIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'
import ReadMoreReadLess from '@/Components/app/ReadMoreReadLess.vue'
import IndigoButton from '@/Components/app/IndigoButton.vue'
import InputTextarea from '@/Components/InputTextarea.vue'
import EditDeleteDropdown from '@/Components/app/EditDeleteDropdown.vue'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'


defineProps({
    post: {
        type: Object
    },
    comments: {
        type: Array
    }
})

const authUser = usePage().props.auth.user

const newCommentText = ref('')

const editingComment = ref(null)

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
    axiosClient.delete(route('comment.delete', comment.id)).then(({data}) => {
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
    axiosClient.put(route('comment.update', editingComment.value.id), editingComment.value).then(({data}) => {
        editingComment.value = null
        props.post.latestComments = props.post.latestComments.map((c) => {
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
                                reply
                            </DisclosureButton>
                        </div>
                        <DisclosurePanel>
                            <CommentLit :post="post" :comments="comment.comments"/>
                            <div class="flex-1 flex gap-2 mt-2">
                                <InputTextarea v-model="newCommentText" rows="1" class="w-full overflow-auto resize-none max-h-40" placeholder="Enter your comment here..."/>
                                <IndigoButton @click="createComment" class="w-40 h-10 text-nowrap">Add Reply</IndigoButton>
                            </div>
                        </DisclosurePanel>
                    </Disclosure>
                </div>
            </div>
        </div>
    </div>
</template>