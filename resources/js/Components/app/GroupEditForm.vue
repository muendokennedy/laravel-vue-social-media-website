<script setup>
import InputTextarea from '@/Components/InputTextarea.vue'
import Checkbox from '@/Components/Checkbox.vue'
import TextInput from '@/Components/TextInput.vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

defineProps({
    form: {
        type: Object
    }
})

const editor = ClassicEditor

const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', '|', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote']
}
</script>
<template>
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
    <ckeditor :editor="editor" v-model="form.about" :config="editorConfig"></ckeditor>
</div>
</template>
