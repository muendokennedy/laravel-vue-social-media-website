<script setup>
import UserListItem from './UserListItem.vue';
import TextInput from '../TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')

</script>
<template>
        <TextInput v-model="searchKeyword" placeHolder="Search here..." class="w-full mt-3"/>
        <div class="mt-3 h-96 flex-1 overflow-auto">
            <div v-if="false" class="text-gray-400 text-center p-3">
                You do not have any friends yet.
            </div>
            <div v-else>

            </div>
        </div>
</template>
