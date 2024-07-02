<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon, PencilIcon, TrashIcon, EllipsisVerticalIcon, ArrowDownTrayIcon } from '@heroicons/vue/20/solid'
import { ChatBubbleLeftRightIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'
import PostUserInfo from '@/Components/app/PostUserInfo.vue'
import { router } from '@inertiajs/vue3'
import { isImage } from '@/helpers.js'

const props = defineProps({
    post: Object,
})

const emit = defineEmits(['editClick'])

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

</script>
<template>
    <div class="bg-white rounded p-4 mb-3">
        <div class="flex justify-between mb-3">
            <PostUserInfo :post="post" class="mb-4"/>
                <Menu as="div" class="z-10 relative inline-block text-left">
                <div>
                    <MenuButton
                    class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center"
                    >
                    <EllipsisVerticalIcon
                        class="h-5 w-5"
                        aria-hidden="true"
                    />
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                    class="absolute right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                    >
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button

                        @click="openEditModel"

                            :class="[
                            active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <PencilIcon
                            :active="active"
                            class="mr-2 h-5 w-5 text-indigo-400"
                            aria-hidden="true"
                            />
                            Edit
                        </button>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                        <button

                        @click="deletePost"

                            :class="[
                            active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <TrashIcon
                            :active="active"
                            class="mr-2 h-5 w-5 text-indigo-400"
                            aria-hidden="true"
                            />
                            Delete
                        </button>
                        </MenuItem>
                    </div>
                    </MenuItems>
                </transition>
                </Menu>
        </div>
        <div class="mb-3">
            <Disclosure v-slot="{ open }">
                <div class="ck-content-output" v-if="!open" v-html="post.body.substring(0, 200)"/>
                <template v-if="post.body.length > 200">
                    <DisclosurePanel>
                        <div class="ck-content-output" v-html="post.body"/>
                    </DisclosurePanel>
                    <div class="flex justify-end">
                        <DisclosureButton class="text-blue-500 hover:underline">
                            {{ open ? 'Read Less' : 'Read More'}}
                        </DisclosureButton>
                    </div>
                </template>
            </Disclosure>
        </div>
        <div class="grid gap-3 mb-3"
        :class="[
            post.attachments.length === 1 ? 'grid-cols-1' :  'grid-cols-2'
        ]">
            <template v-for="(attachment, index) in post.attachments.slice(0,4)" :key="index">
                <div class="group aspect-square bg-blue-100 flex items-center justify-center text-gray-500 relative">
                    <div v-if="index === 3 && post.attachments.length > 4" class="absolute inset-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl">
                        + {{ post.attachments.length - 4 }} more
                    </div>
                    <a :href="route('post.download', attachment)" class="flex z-10 opacity-0 group-hover:opacity-100 transition-all items-center text-gray-100 justify-center w-8 h-8 bg-gray-700 hover:bg-gray-800 rounded absolute top-2 right-2 cursor-pointer">
                        <ArrowDownTrayIcon class="size-4"/>
                    </a>
                    <img v-if="isImage(attachment)" :src="attachment.url" alt="" class="object-cover w-full h-full">
                    <template v-else>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <small>{{ attachment.name }}</small>
                    </template>
                </div>
            </template>
        </div>
        <div class="flex gap-2">
            <button class="text-gray-800 flex items-center justify-center bg-gray-100 hover:bg-gray-200 gap-1 flex-1 py-2 px-4 rounded-lg">
                <HandThumbUpIcon class="size-6"/>
                Like
            </button>
            <button class="text-gray-800 flex items-center justify-center bg-gray-100 hover:bg-gray-200 gap-1 flex-1 py-2 px-4 rounded-lg">
                <ChatBubbleLeftRightIcon class="size-6"/>
                Comment
            </button>
        </div>
    </div>
</template>
<style scoped>

</style>
