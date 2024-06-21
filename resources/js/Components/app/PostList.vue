<script setup>
import PostItem from '@/Components/app/PostItem.vue'

defineProps({
    posts: {
        type: Object
    }
})
const firstPost = {
    user: {
        id: 1,
        name: 'John Smith',
        avatar: '/man1.jpg'
    },
    group: null,
    attachments: [
        {
            id: 1,
            name: 'test.png',
            url: '/man1.jpg',
            mime: 'image/png'
        },
        {
            id: 2,
            name: 'test2.png',
            url: '/man1.jpg',
            mime: 'image/png'
        },
        {
            id: 3,
            name: 'mydocument.docx',
            url: '#',
            mime: 'application/msword'
        },
    ],
    body: `<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Optio, obcaecati ipsam alias odio blanditiis quam eos provident tempore. Natus corrupti placeat dolore iste itaque expedita ab autem laudantium dignissimos non?</p>

    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Optio, obcaecati ipsam alias odio blanditiis quam eos provident tempore. Natus corrupti placeat dolore iste itaque expedita ab autem laudantium dignissimos non?</p>`,
    created_at: '2024-02-19'
}
const secondPost = {
    user: {
        id: 1,
        name: 'James Doe',
        avatar: '/man1.jpg'
    },
    group: {
        id: 1,
        name: 'Laravel Developers',
    },
    body: `<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Optio, obcaecati ipsam alias odio blanditiis quam eos provident tempore. Natus corrupti placeat dolore iste itaque expedita ab autem laudantium dignissimos non?</p>

    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Optio, obcaecati ipsam alias odio blanditiis quam eos provident tempore. Natus corrupti placeat dolore iste itaque expedita ab autem laudantium dignissimos non?</p>`,
    created_at: '2024-02-19'
}
</script>
<template>
    <div class="h-full overflow-auto">
        <PostItem v-for="post in posts" :key="post.id" :post="post"/>
    </div>

</template>
<style scoped>

</style>
