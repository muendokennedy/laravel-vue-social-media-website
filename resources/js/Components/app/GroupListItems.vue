<script setup>
import GroupItem from './GroupItem.vue';
import TextInput from '../TextInput.vue';
import { ref } from 'vue';
import GroupModal from '@/Components/app/GroupModal.vue'

defineProps({
    groups: Array
})

const showNewGroupModal = ref(false)
const searchKeyword = ref('')



</script>
<template>
    <div class="flex gap-2 mt-4">
        <TextInput v-model="searchKeyword" placeHolder="Search here..." class="w-full"/>
        <button @click="showNewGroupModal = true" class="text-sm bg-indigo-500 hover:bg-indigo-600 text-white rounded py-1 px-2 capitalize w-fit text-nowrap">new group</button>
    </div>
    <div class="mt-3 h-96 lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            You are not joined to any groups
        </div>
        <div v-else>
            <GroupItem v-for="(group, index) in groups" :key="index" :group="group"/>
        </div>
    </div>
    <GroupModal v-model="showNewGroupModal"/>
</template>
