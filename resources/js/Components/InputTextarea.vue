<script setup>
import { onMounted, ref, watch } from 'vue';

const model = defineModel({
    type: String,
    required: false,
});

const props = defineProps({
    placeHolder: String,
    autoResize: {
        type: Boolean,
        default: true
    }
})

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
    adjustTextareaHeight()
});

watch(() => props.modelValue, () => {
    setTimeout(() => {
        adjustTextareaHeight()
    }, 10)
})

defineExpose({ focus: () => input.value.focus() });

const onInputChange = () => {
    adjustTextareaHeight()
}

const adjustTextareaHeight = () => {
    if(props.autoResize){
        input.value.style.height = 'auto'
        input.value.style.height = (input.value.scrollHeight) + 2 + 'px'
    }
}

</script>

<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        v-model="model"
        ref="input"
        @input="onInputChange"
        :placeholder="placeHolder"
    ></textarea>
</template>
