<script setup>
import { ref, computed, onUpdated } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue'
import { MoonIcon, SunIcon } from '@heroicons/vue/24/solid'

const showingNavigationDropdown = ref(false)

const keywords = ref(usePage().props.search || '')

const authUser = usePage().props.auth.user

const search = () => {
    router.get(route('search', encodeURIComponent(keywords.value)))
}

const isDarkMode = ref(localStorage.getItem('darkMode') === '1')

const currentIcon = computed(() => (isDarkMode.value ? SunIcon : MoonIcon))


function toggleDarkmode() {

    const html = window.document.documentElement

    if(html.classList.contains('dark')){
        html.classList.remove('dark')
        localStorage.setItem('darkMode', '0')
        isDarkMode.value = false
    }else{
        html.classList.add('dark')
        localStorage.setItem('darkMode', '1')
        isDarkMode.value = true
    }
}

</script>

<template>
    <div class="h-full flex flex-col bg-gray-100 dark:bg-gray-800">
    <nav class="bg-white border-b border-gray-100 dark:bg-slate-950 dark:border-slate-800">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center gap-2 h-16">
                <div class="flex mr-2">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('home')">
                            <ApplicationLogo
                                class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-100"
                            />
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <NavLink :href="route('home')" :active="route().current('home')">
                            Homepage
                        </NavLink>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-1">
                    <TextInput v-model="keywords" placeholder="Search on the website..." class="w-full dark:text-gray-100" @keyup.enter="search"/>
                    <button @click="toggleDarkmode" class="dark:text-white">
                        <component :is="currentIcon" class="size-5"/>
                    </button>
                </div>

                <div class="hidden sm:flex sm:items-center">
                    <!-- Settings Dropdown -->
                    <div class="ms-3 relative">
                        <Dropdown align="right" v-if="authUser" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white dark:bg-slate-950 dark:text-gray-100 dark:hover:text-gray-200 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                    >
                                        {{ authUser.name }}

                                        <svg
                                            class="ms-2 -me-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.home', {username: authUser.username})"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                        <div v-else>
                            <Link :href="route('login')" class="dark:text-gray-100">Login button</Link>
                        </div>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
            :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
            class="sm:hidden"
        >
            <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink :href="route('home')" :active="route().current('home')">
                    Homepage
                </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <template v-if="authUser">
                    <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ authUser.name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">{{ authUser.email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink :href="route('profile.home', {username: authUser.username})"> Profile </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                        Log Out
                    </ResponsiveNavLink>
                </div>
                </template>
                <template>
                    Log in
                </template>
            </div>
        </div>
    </nav>

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot name="header" />
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 overflow-hidden">
        <slot />
    </main>
    </div>
</template>
