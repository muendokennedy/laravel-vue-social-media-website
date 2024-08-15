<script setup>
import UserListItem from '@/Components/app/UserListItem.vue'
import GroupItem from '@/Components/app/GroupItem.vue'
import PostList from '@/Components/app/PostList.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    users: Array,
    groups: Array,
    posts: Object
})
</script>
<template>
    <AuthenticatedLayout>
        <div class="p-8">
            <div class="grid gap-2 grid-cols-1 sm:grid-cols-2">
                <div class="shadow bg-white p-3 rounded mb-3">
                    <h2 class="text-lg font-bold">Users</h2>
                    <div class="grid-cols-2">
                        <div v-if="users.length">
                            <UserListItem v-for="(user, index) in users" :user="user" :key="index"/>
                        </div>
                        <div v-else class="py-8 text-center text-gray-500">
                            No users found
                        </div>
                    </div>
                </div>
                <div class="shadow bg-white p-3 rounded mb-3">
                    <h2 class="text-lg font-bold">Groups</h2>
                    <div class="grid-cols-2">
                        <div v-if="groups.length">
                            <GroupItem v-for="(group, index) in groups" :group="group" :key="index"/>
                        </div>
                        <div v-else class="py-8 text-center text-gray-500">
                            No groups found
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-lg font-bold">Posts</h2>
                <PostList v-if="posts.data.length" :posts="posts.data" class="flex-1"/>
                <div v-else class="py-8 text-center text-gray-500">
                    No posts found
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    </template>
