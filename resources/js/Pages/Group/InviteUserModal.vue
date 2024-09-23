<script setup>
    import { computed, ref} from 'vue'
    import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle} from '@headlessui/vue'
    import { XMarkIcon, PaperClipIcon, BookmarkIcon } from '@heroicons/vue/24/solid'
    import { useForm, usePage } from '@inertiajs/vue3'
    import TextInput from '@/Components/TextInput.vue'
    import Checkbox from '@/Components/Checkbox.vue'
    import axiosClient from '@/axiosClient.js'
    import InputTextarea from '@/Components/InputTextarea.vue'
    import BaseModal from '@/Components/app/BaseModal.vue'


    const props = defineProps({
        modelValue: Boolean
    })

    const form = useForm({
        email: ''
    })

    const page = usePage();

    const formErrors = ref({})

    const show = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    const emit = defineEmits(['update:modelValue', 'hide', 'create'])

    const closeModal = () => {
        show.value = false
        emit('hide')
        resetModal()
    }

    const resetModal = () => {
        form.reset()
        formErrors.value = {}
    }

    const submit = () => {
        form.post(route('group.inviteUser', page.props.group.slug), {
            onSuccess: (res) => {
                console.log(res)
                closeModal()
            },
            onError: (error) => {
                console.log(error)
            }
        })
    }

</script>
<template>
    <BaseModal title="Invite users" v-model="show" @hide="closeModal">
        <div class="p-4 dark:text-gray-100">
                <div class="mb-3">
                    <label for="name">User username or email</label>
                    <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    :class = "$page.props.errors.email ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''"
                    v-model="form.email"
                    autofocus
                    />
                </div>
                <div class="text-red-500">{{ $page.props.errors.email }}</div>
            </div>
            <div class="py-3 px-4 flex gap-2 justify-end">
                <button @click="show = false"
                class="text-gray-800 flex items-center justify-center bg-gray-100 hover:bg-gray-200 gap-1 py-2 px-4 rounded-md"
                >
                <XMarkIcon class="size-6"/>
                Cancel
                </button>
                <button @click="submit"
                type="button"
                class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <BookmarkIcon class="size-4 mr-2"/>
                submit
                </button>
            </div>
    </BaseModal>

  </template>

