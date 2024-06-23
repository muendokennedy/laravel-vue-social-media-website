<script setup>
    import { computed, ref } from 'vue'
    import {TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle} from '@headlessui/vue'
    import InputTextarea from '@/Components/InputTextarea.vue';
    import PostUserInfo from '@/Components/app/PostUserInfo.vue';
    import { XMarkIcon } from '@heroicons/vue/24/solid'
    import { useForm } from '@inertiajs/vue3';


    const props = defineProps({
        post: {
            type: Object,
            required: true
        },
        modelValue: Boolean
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
        const form = useForm({
            id: props.post.id,
            body: props.post.body
        })

        form.put(route('post.update', props.post), {
            onSuccess: () => {
                show.value = false
            },
            preserveScroll: true
        })
    }

    // function openModal() {
    //     show.value = true
    // }

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
                      Update Post
                      <button class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                        <XMarkIcon class="w-4 h-4" @click="show = false"/>
                      </button>
                    </DialogTitle>
                    <div class="p-4">
                        <PostUserInfo :post="post" :show-time="false" class="mb-4"/>
                        <InputTextarea v-model="post.body" class="mb-3 w-full"/>
                    </div>
                    <div class="py-3 px-4">
                      <button @click="submit"
                        type="button"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full">
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

