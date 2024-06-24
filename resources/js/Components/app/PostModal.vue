<script setup>
    import { computed, ref, watch } from 'vue'
    import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle} from '@headlessui/vue'
    import PostUserInfo from '@/Components/app/PostUserInfo.vue';
    import { XMarkIcon, PaperClipIcon, BookmarkIcon } from '@heroicons/vue/24/solid'
    import { useForm } from '@inertiajs/vue3';
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    import { isImage } from '@/helpers.js'


    const props = defineProps({
        post: {
            type: Object,
            required: true
        },
        modelValue: Boolean
    })

    const form = useForm({
        id: null,
        body: ''
    })

    const attachmentFiles = ref([])

    watch(() => props.post, () => {
        form.id = props.post.id
        form.body = props.post.body
    })

    const show = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    const emit = defineEmits(['update:modelValue'])

    const closeModal = () => {
        show.value = false
    }

    const submit = () => {

        if(form.id){
            form.put(route('post.update', props.post), {
                onSuccess: () => {
                    show.value = false
                    form.reset()
                },
                preserveScroll: true
            })
        } else {
            form.post(route('post.store'), {
                onSuccess: () => {
                    show.value = false
                    form.reset()
                },
                preserveScroll: true
            })
        }
    }

    const editor = ClassicEditor

    const editorConfig = {
        toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', '|', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote']
    }

    // function openModal() {
    //     show.value = true
    // }

    const onAttachmentChoose = async (event) => {
        for(const file of event.target.files){
            const myFile = {
                file,
            }
            try {
                myFile.url = await readFile(file)
            } catch (error) {
                console.log(error.message)
            }
            attachmentFiles.value.push(myFile)
        }
        console.log(attachmentFiles.value)
    }

    const readFile = async (file) => {
        return new Promise((resolve, reject) => {
            if(isImage(file)){
            const reader = new FileReader()
            reader.onload = () => {
                resolve(reader.result)
            }
            reader.onerror = () => {
                reject(new Error('Error reading this file'))
            }
            reader.readAsDataURL(file)
        } else {
            resolve(null)
        }
        })
    }

</script>
<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
          <Dialog as="div" @close="closeModal" class="relative z-10">
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
                class="flex min-h-full items-center justify-center p-4 text-center"
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
                    class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
                  >
                    <DialogTitle
                      as="h3"
                      class="flex items-center justify-between py-3 px-4 font-medium bg-gray-100 leading-6 text-gray-900"
                    >
                      {{ form.id ? 'Update Post' : 'Create Post' }}
                      <button class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                        <XMarkIcon class="w-4 h-4" @click="show = false"/>
                      </button>
                    </DialogTitle>
                    <div class="p-4">
                        <PostUserInfo :post="post" :show-time="false" class="mb-4"/>
                        <ckeditor :editor="editor" v-model="form.body" :config="editorConfig"></ckeditor>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
                            <template v-for="myFile in attachmentFiles">
                                <div class="group aspect-square bg-blue-100 flex items-center justify-center text-gray-500 relative">
                                    <button class="flex opacity-0 group-hover:opacity-100 transition-all items-center text-gray-100 justify-center w-8 h-8 bg-gray-700 hover:bg-gray-800 rounded absolute top-2 right-2 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                    </button>
                                    <img v-if="isImage(myFile.file)" :src="myFile.url" alt="" class="object-cover aspect-square">
                                    <template v-else>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12 text-gray-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <small>{{ myFile.file.name }}</small>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="py-3 px-4 flex gap-2">
                      <button @click="submit"
                        type="button"
                        class="relative flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full">
                        <PaperClipIcon class="h-4 w-4 mr-2"/>
                        Attach Files
                        <input @click.stop @change="onAttachmentChoose" type="file" multiple class="absolute inset-0 opacity-0">
                      </button>
                      <button @click="submit"
                        type="button"
                        class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full">
                        <BookmarkIcon class="h-4 w-4 mr-2"/>
                        submit
                      </button>
                    </div>
                  </DialogPanel>
                </TransitionChild>
              </div>
            </div>
          </Dialog>
        </TransitionRoot>
    </teleport>
  </template>

