<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon, MapPinIcon, EyeIcon, PencilIcon, TrashIcon, EllipsisVerticalIcon, ArrowDownTrayIcon, PaperClipIcon } from '@heroicons/vue/20/solid'
import { ClipboardIcon } from '@heroicons/vue/24/outline'
import { usePage, Link } from '@inertiajs/vue3'
import { comment } from 'postcss';
import { computed } from 'vue';

const props = defineProps({
    post: {
        type: Object,
        default: null
    },
    comment: {
        type: Object,
        default: null
    }
})

const authUser = usePage().props.auth.user

const group = usePage().props.group

const user = computed(() => props.comment?.user || props.post?.user)

const editAllowed = computed(() => user.value.id === authUser?.id)

const deleteAllowed = computed(() => {

    if(user.value.id === authUser?.id) return true

    if(props.post.user.id === authUser?.id) return true

    return !props.comment && props.post.group?.role === 'admin'
})

const pinAllowed = computed(() => {
    return user.value.id === authUser.id || props.post.group && props.post.group.role === 'admin'
})

const isPinned = computed(() => {
    if(group?.id){
        return group?.pinned_post_id === props.post.id
    }

    return authUser?.pinned_post_id === props.post.id
})

defineEmits(['edit', 'delete', 'pin'])

const copyToClipboard = async () => {

    const textToCopy = route('post.show', props.post.id)

    try {
        await navigator.clipboard.writeText(textToCopy)
    } catch (error) {
        console.log('Error: failed to copy, ', error)
    }
}

</script>

<template>
    <Menu as="div" class="relative inline-block text-left">
        <div v-if="deleteAllowed">
            <MenuButton
            class="z-10 w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center"
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
            class="z-20 absolute right-0 mt-2 w-40 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
            <div class="px-1 py-1">
                <MenuItem v-slot="{ active }">
                <button
                @click="copyToClipboard"
                    :class="[
                    active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                >
                    <ClipboardIcon
                    :active="active"
                    class="mr-2 h-5 w-5 text-indigo-400"
                    aria-hidden="true"
                    />
                    copy post URL
                </button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                <Link :href="route('post.show', post.id)"
                    :class="[
                    active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                >
                    <EyeIcon
                    :active="active"
                    class="mr-2 h-5 w-5 text-indigo-400"
                    aria-hidden="true"
                    />
                    Open post
                </Link>
                </MenuItem>
                <MenuItem v-if="editAllowed" v-slot="{ active }">
                <button

                @click="$emit('edit')"

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
                <MenuItem v-if="pinAllowed" v-slot="{ active }">
                <button

                @click="$emit('pin')"

                    :class="[
                    active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                >
                    <MapPinIcon
                    :active="active"
                    class="mr-2 h-5 w-5 text-indigo-400"
                    aria-hidden="true"
                    />
                    {{ isPinned ? 'Unpin' : 'Pin' }}
                </button>
                </MenuItem>
                <MenuItem v-if="deleteAllowed" v-slot="{ active }">
                <button

                @click="$emit('delete')"

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
</template>
<style scoped>
</style>
