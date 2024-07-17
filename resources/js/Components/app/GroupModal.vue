<script setup>
    import { computed, ref} from 'vue'
    import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle} from '@headlessui/vue'
    import { XMarkIcon, PaperClipIcon, BookmarkIcon } from '@heroicons/vue/24/solid'
    import { useForm } from '@inertiajs/vue3'
    import TextInput from '@/Components/TextInput.vue'
    import Checkbox from '@/Components/Checkbox.vue'
    import InputTextarea from '@/Components/InputTextarea.vue'


    const props = defineProps({
        modelValue: Boolean
    })

    const form = useForm({
        name: '',
        auto_approval: true,
        about: '',
    })

    const formErrors = ref({})

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
    }

    const submit = () => {
        form.post(route(''))
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
                      Create new group

                      <button class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                        <XMarkIcon class="w-4 h-4" @click="closeModal"/>
                      </button>
                    </DialogTitle>
                    <div class="p-4">
                        <div class="mb-3">
                            <label for="name">Group name</label>
                            <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            autofocus
                            />
                        </div>
                        <div  class="mb-3">
                            <label for="remember">
                                <Checkbox name="remember" id="remember" v-model:checked="form.auto_approval" />
                                Enable auto approval
                            </label>
                        </div>
                        <div class="mb-3">
                            <label for="about">Group Description</label>
                            <InputTextarea id="about" v-model="form.about" class="w-full"/>
                        </div>
                    </div>
                    <div class="py-3 px-4 flex gap-2 justify-end">
                        <button
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
                  </DialogPanel>
                </TransitionChild>
              </div>
            </div>
          </Dialog>
        </TransitionRoot>
    </teleport>
  </template>

