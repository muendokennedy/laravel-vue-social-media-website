<script setup>
    import { computed, ref, watch } from 'vue'
    import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle} from '@headlessui/vue'
    import PostUserInfo from '@/Components/app/PostUserInfo.vue';
    import { XMarkIcon, PaperClipIcon, BookmarkIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
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
        body: '',
        attachments: [],
        deleted_file_ids: [],
        _method: 'POST'
    })

    const attachmentFiles = ref([])

    watch(() => props.post, () => {
            form.body = props.post.body || ''
    })

    const show = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    const emit = defineEmits(['update:modelValue', 'hide'])

    const closeModal = () => {
        show.value = false
        emit('hide')
        resetModal()
    }

    const resetModal = () => {
        form.reset()
        attachmentFiles.value = []
        props.post.attachments.forEach(file => file.deleted = false)
    }

    const submit = () => {

        form.attachments = attachmentFiles.value.map(myFile => myFile.file)

        if(props.post.id){
            form._method = 'PUT'
            form.post(route('post.update', props.post), {
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
        if(file.file){
            attachmentFiles.value = attachmentFiles.value.filter(f => f !== file)
        } else {
            form.deleted_file_ids.push(file.id)
            file.deleted = true
        }
    }

    const computedAttachments = computed(() => {
            return [...attachmentFiles.value, ...(props.post.attachments || [])]
    })

    const undoDelete = (file) => {
        file.deleted = false
        form.deleted_file_ids = form.deleted_file_ids.filter(id => file.id !== id)
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
                      {{ post.id ? 'Update Post' : 'Create Post' }}
                      <button class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                        <XMarkIcon class="w-4 h-4" @click="closeModal"/>
                      </button>
                    </DialogTitle>
                    <div class="p-4">
                        <PostUserInfo :post="post" :show-time="false" class="mb-4"/>
                        <ckeditor :editor="editor" v-model="form.body" :config="editorConfig"></ckeditor>
                        <pre>{{  form.deleted_file_ids }}</pre>
                        <div class="grid gap-3 my-3"
                        :class="[
                            computedAttachments.length === 1 ? 'grid-cols-1' :  'grid-cols-2'
                            ]">
                            <template v-for="(myFile, index) in computedAttachments" :key="index">
                                <div class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative">
                                    <div v-if="myFile.deleted" class="z-10 absolute left-0 bottom-0 right-0 py-2 px-3 text-sm bg-black text-white flex items-center justify-between">
                                        To be deleted
                                        <ArrowUturnLeftIcon @click="undoDelete(myFile)" class="size-4 cursor-pointer"/>
                                    </div>
                                    <button @click="removeFile(myFile)" class="absolute z-20 right-1 top-1 w-7 h-7 flex items-center justify-center bg-black/30 hover:bg-black/70 text-white rounded-full">
                                        <XMarkIcon class="size-5"/>
                                    </button>
                                    <img v-if="isImage(myFile.file ?? myFile)" :src="myFile.url" alt="" class="object-cover w-full h-full" :class="myFile.deleted ? 'opacity-50' : ''">
                                    <div v-else class="flex justify-center items-center flex-col" :class="myFile.deleted ? 'opacity-50' : ''">
                                        <PaperClipIcon class="w-10 h-10 mb-3"/>
                                        <small class="text-center">{{ (myFile.file ?? myFile).name }}</small>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="py-3 px-4 flex gap-2">
                      <button
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

