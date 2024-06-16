<script setup>
import FollowingItem from './FollowingItem.vue';
import TextInput from '../TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')

</script>
<template>
    <div class="px-3">
        <h2 class="text-2xl font-bold mb-4">My Friends</h2>
        <TextInput v-model="searchKeyword" placeHolder="Search here..."/>
        <div class="py-8 px-3">
            <div v-if="false" class="text-gray-400 flex text-center">
                You do not have any friends yet.
            </div>
            <div v-else>
                <FollowingItem title="John Doe" image="/man1.jpg"/>
                <FollowingItem title="Elon Musk" image="/man1.jpg"/>
            </div>
        </div>
    </div>
</template>
