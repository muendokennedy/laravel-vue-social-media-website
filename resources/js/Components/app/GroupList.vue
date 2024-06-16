<script setup>
import GroupItem from './GroupItem.vue';
import TextInput from '../TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')

</script>
<template>
    <div class="px-3">
        <h2 class="text-2xl font-bold mb-4">My Groups</h2>
        <TextInput v-model="searchKeyword" placeHolder="Search here..."/>
        <div class="py-8 px-3">
            <div v-if="false" class="text-gray-400 flex text-center">
                You are not joined to any groups
            </div>
            <div v-else>
                <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
                <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            </div>
        </div>
    </div>
</template>
