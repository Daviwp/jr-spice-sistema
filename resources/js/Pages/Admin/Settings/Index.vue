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
    <Head :title="$t('Configurações')" />

    <DashboardLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                <!-- SEGURANÇA SECTION -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2 px-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Segurança e Acesso</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <UserActivationSetting
                        :initial-requires-activation="!!settings.registration_requires_activation"
                    />
                </div>

                <!-- PERMISSÕES PADRÃO SECTION -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2 px-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Configuração de Permissões</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <DefaultPagesSetting
                        :initial-default-pages="settings.default_user_pages || []"
                    />
                </div>

                <!-- OUTRAS CONFIGURAÇÕES (Placeholder for future use) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 opacity-60">
                     <div class="p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-300 flex flex-col items-center justify-center text-center space-y-2">
                         <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                             <Settings class="w-5 h-5 text-slate-400" />
                         </div>
                         <h4 class="text-sm font-bold text-slate-400">Novas Funcionalidades</h4>
                         <p class="text-[10px] text-slate-400 uppercase tracking-tighter">Em breve</p>
                     </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
