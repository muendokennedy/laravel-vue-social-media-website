<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabItem from './Partials/TabItem.vue'
import Edit from '@/Pages/Profile/Edit.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { computed, ref } from 'vue';
import { XMarkIcon, CheckCircleIcon, CameraIcon } from '@heroicons/vue/24/solid'
import { useForm } from '@inertiajs/vue3'

const imagesForm = useForm({
    cover: null,
    avatar: null,
})

const authUser = usePage().props.auth.user;

const showNotification = ref(true)

const isMyProfile = computed(() => authUser && authUser.id === props.user.id)

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object
    },
    success: {
        type: String
    },
    isCurrentUserFollower: {
        type: Boolean,
        default: false
    },
    followerCount: {
        type: Number
    }
});

const coverImageSource = ref(null)
const avatarImageSource = ref(null)



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
const onAvatarChange = (event) => {

    imagesForm.avatar = event.target.files[0]

    if(imagesForm.avatar){

        const reader = new FileReader()

        reader.onload = () => {
            avatarImageSource.value = reader.result
        }

        reader.readAsDataURL(imagesForm.avatar)
    }
}

const cancelCoverImage = () => {
    imagesForm.cover = null
    coverImageSource.value = null
}
const cancelAvatarImage = () => {
    imagesForm.avatar = null
    avatarImageSource.value = null
}

const submitCoverImage = () => {
    imagesForm.post(route('profile.updateImages'), {
        onSuccess: () => {
            cancelCoverImage()
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
            showNotification.value = true
        }
    })
}
const submitAvatarImage = () => {
    imagesForm.post(route('profile.updateImages'), {
        onSuccess: () => {
            cancelAvatarImage()
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
            showNotification.value = true
        }
    })
}

const followUser = () => {
    const form = useForm({
        follow: !props.isCurrentUserFollower
    })

    form.post(route('user.follow', props.user), {
        preserveScroll: true
    })
}
</script>
<template>
    <AuthenticatedLayout>
        <div class="max-w-[768px] mx-auto h-full overflow-auto">
            <div v-show="showNotification && success" class="my-2 py-2 px-3 text-white font-medium text-sm bg-emerald-500">
                {{ success }}
            </div>
            <div v-show="imagesForm.errors.cover" class="my-2 py-2 px-3 text-white font-medium text-sm bg-red-400">
                {{ imagesForm.errors.cover }}
            </div>
            <div v-show="imagesForm.errors.avatar" class="my-2 py-2 px-3 text-white font-medium text-sm bg-red-400">
                {{ imagesForm.errors.avatar }}
            </div>
        <div class="relative bg-white group">
            <img :src="coverImageSource ?? user.cover_url ?? '/images/coverimageholder.webp'" alt="cover image" class="w-full h-52 object-cover">
            <div v-if="isMyProfile" class="absolute top-2 right-2">
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
                <div class="flex items-center justify-center relative group/avatar -mt-16 ml-12 w-32 h-32 rounded-full">
                    <img :src="avatarImageSource ?? user.avatar_url ?? '/images/useravatar4.webp'" alt="user avatar" class="w-full h-full object-cover rounded-full">
                    <div v-if="isMyProfile">
                        <button v-if="!avatarImageSource" class="absolute bg-black/50 text-white rounded-full inset-0 flex items-center justify-center opacity-0 group-hover/avatar:opacity-100">
                            <CameraIcon  class="size-8 mr-2"/>
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="onAvatarChange">
                        </button>
                        <div v-else class="absolute right-0 top-1 flex gap-2 flex-col">
                            <button @click="cancelAvatarImage" class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full">
                                <XMarkIcon class="size-5"/>
                            </button>
                            <button @click="submitAvatarImage" class="w-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full">
                                <CheckCircleIcon class="size-5"/>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center flex-1 p-4">
                    <div>
                        <h2 class="font-bold text-lg">{{ user.name }}</h2>
                        <p class="text-xs text-gray-500">{{ followerCount }} {{ followerCount === 1 ? ' follower' : ' followers'}}</p>
                    </div>
                    <div>
                        <PrimaryButton v-if="!isCurrentUserFollower" @click="followUser">
                            Follow user
                        </PrimaryButton>
                        <DangerButton v-else-if="isCurrentUserFollower" @click="followUser">
                            Unfollow user
                        </DangerButton>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t">
            <TabItem text='My Posts' :selected="false"></TabItem>
            <TabItem text='Who I Follow' :selected="false"></TabItem>
            <TabItem text='My Followers' :selected="false"></TabItem>
            <TabItem text='My Photos' :selected="false"></TabItem>
            <TabItem text='Profile information' :selected="false"></TabItem>

          <TabGroup>
            <TabPanels class="mt-2">
              <TabPanel class="bg-white p-3 shadow">
                These are my posts
              </TabPanel>
              <TabPanel class="bg-white p-3 shadow">
                I follow all these people
              </TabPanel>
              <TabPanel class="bg-white p-3 shadow">
                These are my followers
              </TabPanel>
              <TabPanel class="bg-white p-3 shadow">
                These are my Photos
              </TabPanel>
              <TabPanel class="" v-if="isMyProfile">
                  <Edit :mustVerifyEmail="mustVerifyEmail" :status="status"/>
              </TabPanel>
            </TabPanels>
          </TabGroup>
        </div>
        </div>

    </AuthenticatedLayout>
  </template>

