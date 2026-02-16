<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { watch, onMounted } from 'vue';
import { toast } from '@/Stores/ToastStore';
import ToastContainer from '@/Components/ToastContainer.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const page = usePage();
const user = page.props.auth.user;

// Watch for flash messages from backend
watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        toast.add(flash.success, 'success');
    }
    if (flash.error) {
        toast.add(flash.error, 'error');
    }
}, { deep: true });

// Check for initial flash messages on mount
onMounted(() => {
    if (page.props.flash.success) {
        toast.add(page.props.flash.success, 'success');
    }
    if (page.props.flash.error) {
        toast.add(page.props.flash.error, 'error');
    }
});

// Custom Navigation Items
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
                                <div v-if="!user.is_master" class="border-b border-slate-100 block">
                                    <DropdownLink :href="route('profile.edit')" class="text-sm">
                                        {{ $t('My Profile') }}
                                    </DropdownLink>
                                </div>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="text-sm text-red-600 hover:bg-red-50"> {{ $t('Log Out') }} </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- MAIN VIEWPORT -->
            <main class="flex-1 overflow-hidden relative">
                <!-- Content gets a wrapper now for consistent padding -->
                <div class="w-full h-full bg-slate-50 overflow-y-auto relative flex flex-col">
                    <div class="flex-1">
                        <slot />
                    </div>

                    <!-- Discrete Footer -->
                    <footer class="py-6 px-8 border-t border-slate-200/50 mt-auto bg-white/30 backdrop-blur-sm">
                        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                {{ $t('Copyright • 2026 All rights reserved') }}
                            </p>
                            <div class="flex items-center space-x-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <a href="#" class="hover:text-blue-600 transition-colors">{{ $t('PRIVACY') }}</a>
                                <a href="#" class="hover:text-blue-600 transition-colors">{{ $t('TERMS') }}</a>
                            </div>
                        </div>
                    </footer>
                </div>
            </main>
        </div>

        <!-- Global Toast Container -->
        <ToastContainer />
    </div>
</template>


