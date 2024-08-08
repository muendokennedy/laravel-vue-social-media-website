<script setup>
import TextInput from '../TextInput.vue';
import { ref } from 'vue';
import UserListItem from '@/Components/app/UserListItem.vue'

defineProps({
    users: Array
})
const searchKeyword = ref('')

</script>
<template>
        <TextInput v-model="searchKeyword" placeHolder="Search here..." class="w-full mt-3"/>
        <div class="mt-3 h-96 flex-1 overflow-auto">
            <div v-if="false" class="text-gray-400 text-center p-3">
                You do not have any friends yet.
            </div>
            <div v-else>
                <UserListItem v-for="(user, index) in users"
                        :user="user"
                        :key="index"
                        class="shadow"/>
            </div>
        </div>
</template>
