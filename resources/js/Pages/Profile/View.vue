<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabItem from './Partials/TabItem.vue'
import Edit from '@/Pages/Profile/Edit.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { computed } from 'vue';

const authUser = usePage().props.auth.user;

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
</script>
<template>
    <AuthenticatedLayout>
        <div class="w-[768px] mx-auto h-full overflow-auto">
        <div class="bg-white">
            <img src="/coverimage2.jpeg" alt="cover image" class="w-full h-52 object-cover">
            <div class="flex">
                <img src="/useravatar4.webp" alt="user avatar" class="w-32 h-32 ml-12 -mt-16">
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

