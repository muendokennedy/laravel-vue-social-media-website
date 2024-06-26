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
        body: '',
        attachments: []
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
        resetModal()
    }

    const resetModal = () => {
        form.reset()
        attachmentFiles.value = []
    }

    const submit = () => {

        form.attachments = attachmentFiles.value.map(myFile => myFile.file)

        if(form.id){
            form.put(route('post.update', props.post), {
                onSuccess: () => {
                    show.value = false
                    closeModal()
                },
                preserveScroll: true
            })
        } else {
            form.post(route('post.store'), {
                onSuccess: () => {
                    show.value = false
                    closeModal()
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
        event.target.files = null
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

    const removeFile = (file) => {
        attachmentFiles.value = attachmentFiles.value.filter(f => f !== file)
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
                        <div class="grid gap-3 my-3"
                        :class="[
                            attachmentFiles.length === 1 ? 'grid-cols-1' :  'grid-cols-2'
                            ]">
                            <template v-for="(myFile, index) in attachmentFiles" :key="index">
                                <div class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative">
                                    <button @click="removeFile(myFile)" class="absolute z-20 right-1 top-1 w-7 h-7 flex items-center justify-center bg-black/30 hover:bg-black/70 text-white rounded-full">
                                        <XMarkIcon class="size-5"/>
                                    </button>
                                    <img v-if="isImage(myFile.file)" :src="myFile.url" alt="" class="object-cover w-full h-full">
                                    <template v-else>
                                        <PaperClipIcon class="w-10 h-10 mb-3"/>
                                        <small class="text-center">{{ myFile.file.name }}</small>
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

