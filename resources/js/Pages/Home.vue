<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps();

</script>

<template>
    <Head title="Welcome" />
    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-3">
            <h2>My Groups</h2>
            <div>
                You are not joined to any groups
            </div>
            <div>
                <div class="flex">
                    <img src="" alt="">
                    <div>
                        <h3>Laravel Developers</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-6">
            Posts
        </div>
        <div class="col-span-3">
            Followers
        </div>
    </div>
</template>
<style scoped>


</style>
