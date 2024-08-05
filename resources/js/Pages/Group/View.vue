<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import TabItem from '../Profile/Partials/TabItem.vue'
import { computed, ref } from 'vue'
import { XMarkIcon, CheckCircleIcon, CameraIcon } from '@heroicons/vue/24/solid'
import { useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import InviteUserModal from './InviteUserModal.vue'
import UserListItem from '@/Components/app/UserListItem.vue'
import TextInput from '@/Components/TextInput.vue'
import GroupEditForm from '@/Components/app/GroupEditForm.vue'
import GroupInfo from '@/Components/app/GroupInfo.vue'
import PostList from '@/Components/app/PostList.vue'
import CreatePost from '@/Components/app/CreatePost.vue';

const props = defineProps({
    group: {
        type: Object
    },
    success: {
        type: String
    },
    users: {
        type: Array
    },
    requests: {
        type: Array
    },
    posts: {
        type: Object
    }
})

const authUser = usePage().props.auth.user;

const isCurrentUserAdmin = computed(() => props.group.role === 'admin')

const isJoinedToGroup = computed(() => !!props.group.role && props.group.status === 'approved')

const imagesForm = useForm({
    cover: null,
    thumbnail: null,
})

const aboutForm = useForm({
    name: props.group.name,
    auto_approval: !!parseInt(props.group.auto_approval),
    about: props.group.about
})


const showNotification = ref(true)


const coverImageSource = ref(null)
const thumbnailImageSource = ref(null)
const showInviteUserModel = ref(false)
const searchKeyword = ref('')



const onCoverChange = (event) => {

    imagesForm.cover = event.target.files[0]

    if(imagesForm.cover){

        const reader = new FileReader()

        reader.onload = () => {
            coverImageSource.value = reader.result
        }

        reader.readAsDataURL(imagesForm.cover)
    }
}
const onThumbnailChange = (event) => {

    imagesForm.thumbnail = event.target.files[0]

    if(imagesForm.thumbnail){

        const reader = new FileReader()

        reader.onload = () => {
            thumbnailImageSource.value = reader.result
        }

        reader.readAsDataURL(imagesForm.thumbnail)
    }
}

const cancelCoverImage = () => {
    imagesForm.cover = null
    coverImageSource.value = null
}
const cancelThumbnailImage = () => {
    imagesForm.thumbnail = null
    thumbnailImageSource.value = null
}

const submitCoverImage = () => {
    imagesForm.post(route('group.updateImages', props.group.slug), {
        onSuccess: () => {
            cancelCoverImage()
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
            showNotification.value = true
        }
    })
}
const submitThumbnailImage = () => {
    imagesForm.post(route('group.updateImages', props.group.slug), {
        onSuccess: () => {
            cancelThumbnailImage()
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
            showNotification.value = true
        }
    })
}

const joinToGroup = () => {
    const form = useForm({ })

    form.post(route('group.join', props.group.slug))
}

const approveUser = (user) => {
    const form = useForm({
        user_id: user.id,
        action: 'approve'
    })

    form.post(route('group.approveRequest', props.group.slug))
}

const rejectUser = (user) => {
    const form = useForm({
        user_id: user.id,
        action: 'reject'
    })

    form.post(route('group.approveRequest', props.group.slug))
}

const onRoleChange = (user, role) => {
    const form = useForm({
        user_id: user.id,
        role
    })
    // TODO consider preserving the scroll position after all these form submission here and the ones above
    form.post(route('group.changeRole', props.group.slug))
}

const updateGroupInformation = () => {
    aboutForm.put(route('group.update', props.group.slug))
}

const deleteUser = (user) => {
    if(!window.confirm(`Are you sure you want to remove ${user.name} from this group?`)){
        return false
    }
    const form = useForm({
        user_id: user.id
    })
    // TODO consider preserving the scroll position after all these form submission here and the ones above
    form.delete(route('group.removeUser', props.group.slug))

}

</script>
<template>
    <AuthenticatedLayout>
        <div class="max-w-[950px] w-full mx-auto h-full overflow-auto">
            <div class="px-4 pt-0">
                <div v-show="showNotification && success" class="my-2 py-2 px-3 text-white font-medium text-sm bg-emerald-500">
                    {{ success }}
                </div>
                <div v-show="imagesForm.errors.cover" class="my-2 py-2 px-3 text-white font-medium text-sm bg-red-400">
                    {{ imagesForm.errors.cover }}
                </div>
                <div v-show="imagesForm.errors.thumbnail" class="my-2 py-2 px-3 text-white font-medium text-sm bg-red-400">
                    {{ imagesForm.errors.thumbnail }}
                </div>
                <div class="relative bg-white group">
                    <img :src="coverImageSource ?? group.cover_url ?? '/images/coverimageholder.webp'" alt="cover image" class="w-full h-52 object-cover">
                    <div v-if="isCurrentUserAdmin" class="absolute top-2 right-2">
                        <button v-if="!coverImageSource" class="opacity-0 group-hover:opacity-100 flex items-center bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>

                            Update Cover Image
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="onCoverChange">
                        </button>
                        <div v-else class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100">
                            <button @click="cancelCoverImage" class="flex items-center bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs">
                                <XMarkIcon class="size-3 mr-2"/>
                                Cancel
                            </button>
                            <button @click="submitCoverImage" class="flex items-center bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-xs">
                                <CheckCircleIcon class="size-3 mr-2"/>
                                Submit
                            </button>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex items-center justify-center relative group/thumbnail -mt-16 ml-12 w-32 h-32 rounded-full">
                            <img :src="thumbnailImageSource ?? group.thumbnail_url ?? '/images/useravatar4.webp'" alt="user thumbnail" class="w-full h-full object-cover rounded-full">
                                <button v-if="isCurrentUserAdmin && !thumbnailImageSource" class="absolute bg-black/50 text-white rounded-full inset-0 flex items-center justify-center opacity-0 group-hover/thumbnail:opacity-100">
                                    <CameraIcon  class="size-8 mr-2"/>
                                    <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="onThumbnailChange">
                                </button>
                                <div v-else-if="isCurrentUserAdmin" class="absolute right-0 top-1 flex gap-2 flex-col">
                                    <button @click="cancelThumbnailImage" class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full">
                                        <XMarkIcon class="size-5"/>
                                    </button>
                                    <button @click="submitThumbnailImage" class="w-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full">
                                        <CheckCircleIcon class="size-5"/>
                                    </button>
                                </div>
                        </div>
                        <div class="flex justify-between items-center flex-1 p-4">
                            <h2 class="font-bold text-lg">{{ group.name }}</h2>
                           <PrimaryButton v-if="!authUser" :href="route('login')" >
                            Login to join this group
                           </PrimaryButton>
                           <PrimaryButton v-if="isCurrentUserAdmin" @click="showInviteUserModel = true" >
                            Invite users
                           </PrimaryButton>
                           <PrimaryButton v-if="authUser && !group.role && group.auto_approval" @click="joinToGroup">
                            Join to Group
                           </PrimaryButton>
                           <PrimaryButton v-if="authUser && !group.role && !group.auto_approval" @click="joinToGroup">
                            Request to Join
                           </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        <div class="border-t p-4 pt-0">
            <TabGroup>
            <TabList class="flex bg-white">
              <Tab v-slot="{ selected }" as="template">
                <TabItem text='My Posts' :selected="selected"></TabItem>
              </Tab>
              <Tab v-if="isJoinedToGroup" v-slot="{ selected }" as="template">
                <TabItem text='Users' :selected="selected"></TabItem>
              </Tab>
              <Tab v-if="isCurrentUserAdmin" v-slot="{ selected }" as="template">
                <TabItem text='Requests' :selected="selected"></TabItem>
              </Tab>
              <Tab v-slot="{ selected }" as="template">
                <TabItem text='Photos' :selected="selected"></TabItem>
              </Tab>
              <Tab v-slot="{ selected }" as="template">
                <TabItem text='About' :selected="selected"></TabItem>
              </Tab>
            </TabList>
            <TabPanels class="mt-2">
              <TabPanel>
                <template v-if="posts">
                    <CreatePost :group="group"/>
                    <PostList :posts="posts.data" class="flex-1"/>
                </template>
                <div v-else class="py-8 text-center">
                    You do not have permission to view posts in this group
                </div>
              </TabPanel>
              <TabPanel v-if="isJoinedToGroup">
                <div class="mb-3">
                    <TextInput v-model="searchKeyword" placeHolder="Search here..." class="w-full"/>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <UserListItem v-for="(user, index) in users"
                    :user="user"
                    :key="index"
                    :show-role-dropdown="isCurrentUserAdmin"
                    :disable-role-dropdown="group.user_id === user.id"
                    @role-change="onRoleChange"
                    @delete-user="deleteUser"
                    class="shadow"/>
                </div>
              </TabPanel>
              <TabPanel v-if="isCurrentUserAdmin">
                <div v-if="requests.length" class="grid grid-cols-2 gap-3">
                    <UserListItem v-for="(user, index) in requests"
                    :user="user"
                    :for-approve="true"
                    :key="index"
                    @approve="approveUser"
                    @reject="rejectUser"
                    class="shadow"/>
                </div>
                <div v-else class="py-8 text-center">
                    There are no pending requests
                </div>
              </TabPanel>
              <TabPanel class="bg-white p-3 shadow">
                Photos
              </TabPanel>
              <TabPanel class="bg-white p-3 shadow">
                <template v-if="isCurrentUserAdmin">
                    <GroupEditForm :form="aboutForm"/>
                    <PrimaryButton @click="updateGroupInformation">Submit</PrimaryButton>
                </template>
                <div v-else v-html="group.about">

                </div>
                <!-- <GroupInfo v-else-if="!isCurrentUserAdmin" :form="aboutForm"/> -->
              </TabPanel>
            </TabPanels>
          </TabGroup>
        </div>
        </div>
    </AuthenticatedLayout>
    <InviteUserModal v-model="showInviteUserModel"/>
</template>
