<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const user = usePage().props.auth.user;
// Custom Navigation Items
// Note: We are using 'dashboard' as a placeholder route until specific routes are defined.
const navItems = [];
</script>

<template>
    <div class="h-screen w-full bg-slate-50 font-sans text-slate-900 flex relative selection:bg-blue-100 selection:text-blue-900">

        <!-- 🚀 MAIN STAGE (FULL WIDTH) -->
        <div class="flex-1 flex flex-col h-full relative z-10 bg-slate-50">

            <!-- MINIMALIST HEADER (DARK THEME) -->
            <header class="h-16 flex items-center justify-between px-6 bg-[#0f172a] border-b border-slate-800 z-50 shadow-md relative">
                <div class="flex items-center space-x-8">
                    <!-- Brand Logo -->
                    <Link :href="route('dashboard')" class="flex items-center space-x-2 group">
                        <img src="/logo-white.png" alt="Jrspice" class="h-10 w-auto object-contain transition-opacity duration-300 opacity-90 group-hover:opacity-100" />
                    </Link>
                    <slot name="header">
                        <h2 class="hidden md:block text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $t('Analytics Platform') }}</h2>
                    </slot>

                    <!-- Navigation Links -->

                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-5">
                    <div class="flex items-center space-x-4 pl-4 border-l border-slate-800 relative z-50">
                        <LanguageSwitcher />

                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-2 p-1 rounded-lg hover:bg-slate-800 transition-colors focus:outline-none">
                                    <div class="h-8 w-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-blue-400">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $t('Account') }}</p>
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ user.name }}</p>
                                </div>
                                <div v-if="user.is_master" class="border-b border-slate-100 block">
                                    <DropdownLink :href="route('admin.users.index')" class="text-sm">
                                        {{ $t('Users') }}
                                    </DropdownLink>
                                    <DropdownLink :href="route('admin.settings.index')" class="text-sm">
                                        {{ $t('Settings') }}
                                    </DropdownLink>
                                </div>
                                <DropdownLink :href="route('profile.edit')" class="text-sm"> {{ $t('Profile') }} </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="text-sm text-red-600 hover:bg-red-50"> {{ $t('Log Out') }} </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- MAIN VIEWPORT -->
            <main class="flex-1 overflow-hidden relative">
                <!-- Content gets a wrapper now for consistent padding -->
                <div class="w-full h-full bg-slate-50 overflow-y-auto relative">
                    <!-- Flash Messages -->
                    <div v-if="$page.props.flash.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                        </div>
                    </div>
                    <div v-if="$page.props.flash.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                        </div>
                    </div>

                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>


