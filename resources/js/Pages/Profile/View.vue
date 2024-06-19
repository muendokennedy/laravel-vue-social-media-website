<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabItem from './Partials/TabItem.vue'
import Edit from '@/Pages/Profile/Edit.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { computed, ref } from 'vue';
import { XMarkIcon, CheckCircleIcon } from '@heroicons/vue/24/solid'
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
    }
});

const coverImageSource = ref(null)



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

const cancelCoverImage = () => {
    imagesForm.cover = null
    coverImageSource.value = null
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
</script>
<template>

    <AuthenticatedLayout>
        <div class="max-w-[768px] mx-auto h-full overflow-auto">

            <div v-show="showNotification && status === 'cover-image-updated'" class="my-2 py-2 px-3 text-white font-medium text-sm bg-emerald-500">
                The cover image has been updated successfully
            </div>
            <div v-show="imagesForm.errors.cover" class="my-2 py-2 px-3 text-white font-medium text-sm bg-red-400">
                {{ imagesForm.errors.cover }}
            </div>
            <pre>{{}}</pre>
        <div class="relative bg-white group">
            <img :src="coverImageSource ?? user.cover_url ?? '/images/coverimageholder.webp'" alt="cover image" class="w-full h-52 object-cover">
            <div class="absolute top-2 right-2">
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
                <img src="/images/useravatar4.webp" alt="user avatar" class="w-32 h-32 ml-12 -mt-16">
                <div class="flex justify-between items-center flex-1 p-4">
                    <h2 class="font-bold text-lg">{{ user.name }}</h2>
                    <PrimaryButton v-if="isMyProfile">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                        Edit Profile
                    </PrimaryButton>
                </div>
            </div>
        </div>
        <div class="border-t">
          <TabGroup>
            <TabList class="flex bg-white">
              <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
                <TabItem text='About me' :selected="selected"></TabItem>
              </Tab>
              <Tab v-slot="{ selected }" as="template">
                <TabItem text='My Posts' :selected="selected"></TabItem>
              </Tab>
              <Tab v-slot="{ selected }" as="template">
                <TabItem text='Who I Follow' :selected="selected"></TabItem>
              </Tab>
              <Tab v-slot="{ selected }" as="template">
                <TabItem text='My Followers' :selected="selected"></TabItem>
              </Tab>
              <Tab v-slot="{ selected }" as="template">
                <TabItem text='My Photos' :selected="selected"></TabItem>
              </Tab>
            </TabList>
            <TabPanels class="mt-2">
              <TabPanel class="" v-if="isMyProfile">
                <Edit :mustVerifyEmail="mustVerifyEmail" :status="status"/>
              </TabPanel>
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
            </TabPanels>
          </TabGroup>
        </div>
        </div>

    </AuthenticatedLayout>
  </template>

