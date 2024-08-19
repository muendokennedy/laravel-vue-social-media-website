<script setup>
import { computed} from 'vue'
import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle} from '@headlessui/vue'
import { XMarkIcon, PaperClipIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { isImage } from '@/helpers.js'
import { isVideo } from '@/helpers.js'

    const props = defineProps({
        attachments: {
            type: Array,
            required: true
        },
        index: Number,
        modelValue: Boolean
    })

    const show = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    const currentIndex = computed({
        get: () => props.index,
        set: (value) => emit('update:index', value)
    })

    const attachment = computed(() => {
        return props.attachments[currentIndex.value]
    })

    const emit = defineEmits(['update:modelValue', 'update:index', 'hide'])

    const closeModal = () => {
        show.value = false
        emit('hide')
    }

    const prev = () => {
        if(currentIndex.value === 0) return
        currentIndex.value--
    }

    const next = () => {
        if(currentIndex.value === props.attachments.length - 1) return
        currentIndex.value++
    }

</script>
<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
          <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
              <div
                class="h-screen w-screen flex items-center justify-center"
              >
                <TransitionChild
                  as="template"
                  enter="duration-300 ease-out"
                  enter-from="opacity-0 scale-95"
                  enter-to="opacity-100 scale-100"
                  leave="duration-200 ease-in"
                  leave-from="opacity-100 scale-100"
                  leave-to="opacity-0 scale-95"
                >
                  <DialogPanel
                    class="w-full max-w-3xl flex flex-col transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
                  >
                  <button @click="closeModal" class="text-white absolute z-40 right-6 top-6 w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                        <XMarkIcon class="size-10" />
                    </button>
                    <div class="relative p-4 group">
                        <div class="flex items-center absolute text-white cursor-pointer opacity-0 group-hover:opacity-100 w-16 h-full left-0 bg-black/5 z-30">
                            <ChevronLeftIcon class="w-16" @click="prev"/>
                        </div>
                        <div class="flex items-center absolute text-white cursor-pointer opacity-0 group-hover:opacity-100 w-16 h-full right-0 bg-black/5 z-30">
                            <ChevronRightIcon class="w-16" @click="next"/>
                        </div>
                        <img v-if="isImage(attachments[currentIndex])" :src="attachments[currentIndex].url" alt="" class="object-cover w-full h-full">
                        <div v-else-if="isVideo(attachment)" class="flex items-center">
                            <video :src="attachment.url" controls autoplay="true"></video>
                        </div>
                        <div v-else class="p-32 flex flex-col items-center justify-center">
                            <PaperClipIcon class="size-10 mr-2"/>
                            <small>{{ attachments[currentIndex].name }}</small>
                        </div>
                    </div>
                  </DialogPanel>
                </TransitionChild>
              </div>
            </div>
          </Dialog>
        </TransitionRoot>
    </teleport>
  </template>

