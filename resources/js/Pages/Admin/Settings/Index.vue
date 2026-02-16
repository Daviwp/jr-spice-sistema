<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserActivationSetting from './Partials/UserActivationSetting.vue';
import DefaultPagesSetting from './Partials/DefaultPagesSetting.vue';
import { Settings } from 'lucide-vue-next';

const props = defineProps({
    settings: Object
});
</script>

<template>
    <Head :title="$t('Settings')" />

    <DashboardLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                <!-- SEGURANÇA SECTION -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2 px-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $t('Security & Access') }}</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <UserActivationSetting
                        :initial-requires-activation="!!settings.registration_requires_activation"
                    />
                </div>

                <!-- PERMISSÕES PADRÃO SECTION -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2 px-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $t('Permission Settings') }}</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <DefaultPagesSetting
                        :initial-default-pages="settings.default_user_pages || []"
                    />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
