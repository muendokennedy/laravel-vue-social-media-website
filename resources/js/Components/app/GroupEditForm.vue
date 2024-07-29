<script setup>
import InputTextarea from '@/Components/InputTextarea.vue'
import Checkbox from '@/Components/Checkbox.vue'
import TextInput from '@/Components/TextInput.vue'
defineProps({
    form: {
        type: Object
    }
})
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
    <InputTextarea id="about" v-model="form.about" class="w-full"/>
</div>
</template>
