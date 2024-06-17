<script setup>
import GroupItem from './GroupItem.vue';
import TextInput from '../TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')

</script>
<template>
    <TextInput v-model="searchKeyword" placeHolder="Search here..." class="w-full mt-4"/>
    <div class="mt-3 h-96 lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            You are not joined to any groups
        </div>
        <div v-else>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Laravel Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
            <GroupItem title="Vue.js Developers" image="/man1.jpg" description="Lorem ipsum dolor sit amet consectetur adipisicing elit."/>
        </div>
    </div>
</template>
