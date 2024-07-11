<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon, PencilIcon, TrashIcon, EllipsisVerticalIcon, ArrowDownTrayIcon, PaperClipIcon } from '@heroicons/vue/20/solid'
import { ChatBubbleLeftRightIcon, HandThumbUpIcon } from '@heroicons/vue/24/outline'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
defineProps({
    content: {
        type: String
    }
})
</script>

<template>
    <Disclosure v-slot="{ open }">
        <div class="ck-content-output" v-if="!open" v-html="content.substring(0, 200)"/>
        <template v-if="content.length > 200">
            <DisclosurePanel>
                <div class="ck-content-output" v-html="content"/>
            </DisclosurePanel>
            <div class="flex justify-end">
                <DisclosureButton class="text-blue-500 hover:underline">
                    {{ open ? 'Read Less' : 'Read More'}}
                </DisclosureButton>
            </div>
        </template>
    </Disclosure>
</template>
