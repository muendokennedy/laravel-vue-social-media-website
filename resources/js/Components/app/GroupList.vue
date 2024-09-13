<script setup>
import GroupListItems from './GroupListItems.vue'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'

defineProps({
    groups: Array
})


</script>
<template>
    <div class="px-3 bg-white h-full dark:bg-slate-950 dark:border-slate-900 dark:text-gray-100 rounded border py-3 overflow-hidden">
        <div class="h-full block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="text-left w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">My Groups</h2>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 transition-all" :class="open ? 'rotate-90 transform' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <GroupListItems :groups="groups"/>
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="h-full overflow-hidden flex-col hidden lg:flex">
            <div class="flex justify-between">
                <h2 class="text-xl font-bold">My Groups</h2>
            </div>
            <GroupListItems :groups="groups"/>
        </div>
    </div>
</template>
