<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

defineProps({
    placeHolder: String
})

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border-gray-300 dark:border-slate-800 focus:border-indigo-500 dark:focus:border-slate-800 focus:ring-indigo-500 dark:focus:ring-slate-800 rounded-md shadow-sm dark:bg-slate-800"
        v-model="model"
        ref="input"
        :placeholder="placeHolder"
    />
</template>
