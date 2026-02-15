<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import PasswordInput from '@/Components/PasswordInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { Loader2 } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_master: false,
    allowed_pages: [],
});

const availablePages = ref([]);
const isLoadingPages = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get(route('powerbi.pages'));
        availablePages.value = response.data;
    } catch (error) {
        console.error('Failed to load Power BI pages', error);
    } finally {
        isLoadingPages.value = false;
    }
});

const submit = () => {
    form.post(route('admin.users.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const togglePageAccess = (pageName) => {
    if (form.allowed_pages.includes(pageName)) {
        form.allowed_pages = form.allowed_pages.filter(p => p !== pageName);
    } else {
        form.allowed_pages.push(pageName);
    }
};
</script>

<template>
    <Head :title="$t('Add New User')" />

    <DashboardLayout>
        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

                <!-- Page Header -->
                <div class="mb-10 text-center">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $t('Add New User') }}</h1>
                    <p class="mt-3 text-lg text-slate-500 max-w-2xl mx-auto">{{ $t('Create a new user or master admin to manage the platform.') }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-8 sm:p-12">
                        <form @submit.prevent="submit" class="space-y-12">

                            <!-- Section: Account Information -->
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-4 mb-8 flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $t('Account Information') }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Name -->
                                    <div class="space-y-2">
                                        <InputLabel for="name" :value="$t('Name')" class="text-base font-medium text-slate-700" />
                                        <TextInput
                                            id="name"
                                            type="text"
                                            class="block w-full py-3 px-4 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-shadow duration-200"
                                            v-model="form.name"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            :placeholder="$t('Full Name')"
                                        />
                                        <InputError :message="form.errors.name" />
                                    </div>

                                    <!-- Email -->
                                    <div class="space-y-2">
                                        <InputLabel for="email" :value="$t('Email')" class="text-base font-medium text-slate-700" />
                                        <TextInput
                                            id="email"
                                            type="email"
                                            class="block w-full py-3 px-4 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-shadow duration-200"
                                            v-model="form.email"
                                            required
                                            autocomplete="username"
                                            placeholder="user@example.com"
                                        />
                                        <InputError :message="form.errors.email" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Security -->
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-4 mb-8 flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    {{ $t('Security') }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-2">
                                        <InputLabel for="password" :value="$t('Password')" class="text-base font-medium text-slate-700" />
                                        <PasswordInput
                                            id="password"
                                            class="block w-full py-3 px-4 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-shadow duration-200"
                                            v-model="form.password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                        />
                                        <InputError :message="form.errors.password" />
                                    </div>
                                    <div class="space-y-2">
                                        <InputLabel for="password_confirmation" :value="$t('Confirm Password')" class="text-base font-medium text-slate-700" />
                                        <PasswordInput
                                            id="password_confirmation"
                                            class="block w-full py-3 px-4 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-shadow duration-200"
                                            v-model="form.password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                        />
                                        <InputError :message="form.errors.password_confirmation" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Permissions -->
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-4 mb-8 flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    {{ $t('Permissions') }}
                                </h3>

                                <div class="space-y-8">
                                    <!-- Master Privileges -->
                                    <div
                                        class="relative flex items-start p-6 rounded-xl border-2 transition-all cursor-pointer group"
                                        :class="form.is_master ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50'"
                                        @click="form.is_master = !form.is_master"
                                    >
                                        <div class="flex items-center h-6">
                                            <Checkbox name="is_master" v-model:checked="form.is_master" id="is_master" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded" @click.stop />
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <label for="is_master" class="text-base font-bold text-slate-900 select-none cursor-pointer block mb-1 group-hover:text-indigo-700 transition-colors">{{ $t('Grant Master Privileges') }}</label>
                                            <p class="text-sm text-slate-600 select-none leading-relaxed">{{ $t('Grants full administrative access to all system settings and users.') }}</p>
                                        </div>
                                    </div>

                                    <!-- Allowed Pages (Only visible if NOT master) -->
                                    <div v-if="!form.is_master" class="space-y-4">
                                        <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center">
                                            {{ $t('Allowed Reports') }}
                                            <Loader2 v-if="isLoadingPages" class="w-4 h-4 ml-2 animate-spin text-slate-400" />
                                        </h4>

                                        <div v-if="isLoadingPages" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div v-for="n in 4" :key="n" class="h-14 bg-slate-100 rounded-lg animate-pulse"></div>
                                        </div>

                                        <p v-else-if="availablePages.length === 0" class="text-sm text-slate-500 italic">{{ $t('No reports available from Power BI.') }}</p>

                                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div
                                                v-for="page in availablePages.filter(p => !p.displayName.toUpperCase().includes('HOME'))"
                                                :key="page.name"
                                                class="relative flex items-center p-4 rounded-lg border transition-all cursor-pointer"
                                                :class="form.allowed_pages.includes(page.name) ? 'border-blue-500 bg-blue-50/30' : 'border-slate-200 bg-white hover:border-blue-300'"
                                                @click="togglePageAccess(page.name)"
                                            >
                                                <div class="flex items-center h-5">
                                                    <input
                                                        type="checkbox"
                                                        :checked="form.allowed_pages.includes(page.name)"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded"
                                                        @click.stop="togglePageAccess(page.name)"
                                                    />
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <span class="font-medium text-slate-900">{{ page.displayName.replace(/_/g, ' ') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="p-4 bg-yellow-50 text-yellow-800 rounded-lg text-sm border border-yellow-200">
                                        {{ $t('Master administrators have access to all reports automatically.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-x-8 pt-8 border-t border-slate-100">
                                <Link :href="route('admin.users.index')" class="text-sm font-semibold leading-6 text-slate-600 hover:text-slate-900 transition-colors uppercase tracking-wide">
                                    {{ $t('Cancel') }}
                                </Link>

                                <PrimaryButton :class="{ 'opacity-75 cursor-wait': form.processing }" :disabled="form.processing" class="px-10 py-3 text-base font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transform transition-all duration-200">
                                    {{ $t('Create User') }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
