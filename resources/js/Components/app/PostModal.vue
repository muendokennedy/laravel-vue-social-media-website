<script setup>
    import { computed, ref, watch } from 'vue'
    import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle} from '@headlessui/vue'
    import PostUserInfo from '@/Components/app/PostUserInfo.vue';
    import { XMarkIcon, PaperClipIcon, BookmarkIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
    import { useForm, usePage } from '@inertiajs/vue3';
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    // import '../../../css/ckeditor.css';
    import { isImage } from '@/helpers.js'
    import axiosClient from '@/axiosClient.js'
    import UrlPreview from '@/Components/app/UrlPreview.vue'
    import BaseModal from '@/Components/app/BaseModal.vue'


    const props = defineProps({
        post: {
            type: Object,
            required: true
        },
        group: {
            type: Object,
            default: null
        },
        modelValue: Boolean
    })

    const form = useForm({
        body: '',
        group_id: null,
        attachments: [],
        deleted_file_ids: [],
        preview: {},
        preview_url: null,
        _method: 'POST'
    })

    const attachmentFiles = ref([])
    const formErrors = ref({})
    const attachmentErrors = ref([])

    watch(() => props.post, () => {
            form.body = props.post.body || ''
            onInputChange()
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
        formErrors.value = {}
        attachmentFiles.value = []
        attachmentErrors.value = []
        if(props.post.attachments){
            props.post.attachments.forEach(file => file.deleted = false)
        }
    }

    const submit = () => {

        if(props.group){
            form.group_id = props.group.id
        }

        form.attachments = attachmentFiles.value.map(myFile => myFile.file)

        if(props.post.id){
            form._method = 'PUT'
            form.post(route('post.update', props.post), {
                onSuccess: (response) => {
                    closeModal()
                },
                onError: (errors) => {
                    processErrors(errors)
                },
                preserveScroll: true
            })
        } else {
            form.post(route('post.store'), {
                onSuccess: (response) => {
                    closeModal()
                },
                onError: (errors) => {
                    processErrors(errors)
                },
                preserveScroll: true
            })
        }
    }

    const processErrors = (errors) => {
        formErrors.value = errors
        for(const key in errors){
            if(key.includes('.')){
                const [ field, index ] = key.split('.')
                attachmentErrors.value[index] = errors[key]
            }
        }
    }

    const editor = ClassicEditor

    const editorConfig = {
        mediaEmbed: {
            removeProviders: ['dailymotion', 'spotify', 'youtube', 'vimeo', 'instagram', 'twitter', 'googleMaps', 'flickr', 'facebook']
        },
        toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', '|', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote']
    }
    const attachmentExtensions = usePage().props.attachmentExtensions

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
            if(!(form.deleted_file_ids).includes(file.id)){
                form.deleted_file_ids.push(file.id)
            }
            file.deleted = true
        }
    }

    const computedAttachments = computed(() => {
            return [...attachmentFiles.value, ...(props.post.attachments || [])]
    })

    const showExtensionText = computed(() => {
        for(let myFile of attachmentFiles.value){

            let [type, ext] = myFile.file.type.split('/')

            ext = ext.toLowerCase()

            if(!attachmentExtensions.includes(ext)){
                return true
            }
        }
        return false
    })

    const undoDelete = (file) => {
        file.deleted = false
        form.deleted_file_ids = form.deleted_file_ids.filter(id => file.id !== id)
    }

    const getAIContent = () => {

        if(!form.body){
            return
        }

        axiosClient.post(route('post.ai.generate'), {
            prompt: form.body
        }).then(({data}) => {
            form.body = data.content
        }).catch(error => {
            console.log(error)
        })
    }

    const fetchPreview = (url) => {

        if(url === form.preview_url){
            return;
        }
            form.preview_url = url
            form.preview = {}
            if(url){
                axiosClient.post(route('post.fetchUrlPreview'), {url})
                .then(({data}) => {
                    form.preview = {
                        title: data['og:title'],
                        description: data['og:description'],
                        image: data['og:image']
                    }
                })
                .catch(error => {
                    console.log(error)
                })
            }
    }

    const onInputChange = () => {
        let url = matchHref()
        if(!url){
            url = matchLink()
        }
            fetchPreview(url)
    }

    const matchHref = () => {
        const urlRegex = /<a.+href="((https?):\/\/[^"]+)"/

        const match = form.body.match(urlRegex)

        if(match && match.length > 0){
            return match[1]
        }

        return null
    }

    const matchLink = () => {
        const urlRegex = /(?:https?):\/\/[^\s<]+/

        const match = form.body.match(urlRegex)

        if(match && match.length > 0){
            return match[0]
        }

        return null
    }

</script>
<template>
    <BaseModal :title="post.id ? 'Update Post' : 'Create Post'" v-model="show" @hide="closeModal">
        <div class="p-4">
                        <PostUserInfo :post="post" :show-time="false" class="mb-4 dark:text-gray-100"/>
                        <div v-if="formErrors.group_id" class="bg-red-400 py-2 px-3 text-white rounded mb-3">{{ formErrors.group_id }}</div>
                        <ckeditor :editor="editor" v-model="form.body" :config="editorConfig" @input="onInputChange"></ckeditor>
                        <UrlPreview :preview="form.preview" :url="form.preview_url"/>
                        <div v-if="showExtensionText" class="border-l-4 border-amber-500 py-2 px-3 bg-amber-100 mt-3 text-gray-800">
                            File must be one of the following extensions:
                            {{ $page.props.attachmentExtensions.join(', ') }}
                        </div>
                        <div v-if="formErrors.attachments" class="border-l-4 border-red-500 py-2 px-3 bg-red-100 mt-3 text-gray-800">
                            {{ formErrors.attachments }}
                        </div>
                        <button class="flex mt-4 items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500" @click="getAIContent">AI Post</button>
                        <div class="grid gap-3 my-3"
                        :class="[
                            computedAttachments.length === 1 ? 'grid-cols-1' :  'grid-cols-2'
                            ]">
                            <div v-for="(myFile, index) in computedAttachments" :key="index">
                                <div class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative border-2" :class="attachmentErrors[index] ? 'border-red-500' : ''">
                                    <div v-if="myFile.deleted" class="z-10 absolute left-0 bottom-0 right-0 py-2 px-3 text-sm bg-black text-white flex items-center justify-between">
                                        To be deleted
                                        <ArrowUturnLeftIcon @click="undoDelete(myFile)" class="size-4 cursor-pointer"/>
                                    </div>
                                    <button @click="removeFile(myFile)" class="absolute z-20 right-1 top-1 w-7 h-7 flex items-center justify-center bg-black/30 hover:bg-black/70 text-white rounded-full">
                                        <XMarkIcon class="size-5"/>
                                    </button>
                                    <img v-if="isImage(myFile.file ?? myFile)" :src="myFile.url" alt="" class="object-cover w-full h-full" :class="myFile.deleted ? 'opacity-50' : ''">
                                    <div v-else class="flex justify-center items-center flex-col px-3" :class="myFile.deleted ? 'opacity-50' : ''">
                                        <PaperClipIcon class="size-10 mb-3"/>
                                        <small class="text-center">{{ (myFile.file ?? myFile).name }}</small>
                                    </div>
                                </div>
                                <small class="text-red-500">{{ attachmentErrors[index] }}</small>
                            </div>
                        </div>
        </div>
        <div class="py-3 px-4 flex gap-2">
            <button
            type="button"
            class="relative flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full">
            <PaperClipIcon class="size-4 mr-2"/>
            Attach Files
            <input @click.stop @change="onAttachmentChoose" type="file" multiple class="absolute inset-0 opacity-0">
            </button>
            <button @click="submit"
            type="button"
            class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full">
            <BookmarkIcon class="size-4 mr-2"/>
            submit
            </button>
        </div>
    </BaseModal>
  </template>

