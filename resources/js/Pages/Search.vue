<script setup>
import UserListItem from '@/Components/app/UserListItem.vue'
import GroupItem from '@/Components/app/GroupItem.vue'
import PostList from '@/Components/app/PostList.vue'

const props = defineProps({
    users: Array,
    groups: Array,
    posts: Object
})
</script>
<template>
    <div class="grid-cols-1 sm:grid-cols-2">
        <div>
            <h2 class="text-lg font-bold">Users</h2>
            <div class="grid-cols-2">
                <UserListItem v-for="(user, index) in users" :user="user" :key="index"/>
            </div>
        </div>
        <div>
            <h2 class="text-lg font-bold">Groups</h2>
            <div class="grid-cols-2">
                <GroupItem v-for="(group, index) in groups" :group="group" :key="index"/>
            </div>
        </div>
    </div>
    <div>
        <h2 class="text-lg font-bold">Posts</h2>
        <PostList v-if="posts.data.length" :posts="posts.data" class="flex-1"/>
        <div class="py-8 text-center">
            No posts found
        </div>
    </div>
</template>
