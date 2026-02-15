<script setup>
import { useForm } from '@inertiajs/vue3';
import { ShieldCheck, Save, Loader2, AlertCircle } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps({
    initialRequiresActivation: {
        type: Boolean,
        default: false
    }
});

const form = useForm({
    settings: {
        registration_requires_activation: props.initialRequiresActivation
    }
});

// Keep form in sync with prop updates from server
watch(() => props.initialRequiresActivation, (newVal) => {
    form.settings.registration_requires_activation = newVal;
    form.defaults();
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults();
        }
    });
};
</script>

<template>
    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <ShieldCheck class="w-5 h-5 text-blue-600" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Segurança e Acessos</h3>
                    <p class="text-xs text-slate-500">Configurações globais de registro de usuários.</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="flex items-start justify-between p-4 bg-slate-50 rounded-xl border border-slate-100 transition-colors hover:bg-slate-100/50">
                <div class="space-y-1 pr-4">
                    <label for="registration_requires_activation" class="text-sm font-bold text-slate-800 cursor-pointer">
                        Requerer Ativação Manual
                    </label>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Quando ativado, novos usuários registrados precisarão ser aprovados manualmente por um administrador master antes de conseguirem fazer login.
                    </p>
                </div>

                <div class="flex items-center pt-1">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            id="registration_requires_activation"
                            v-model="form.settings.registration_requires_activation"
                            class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <!-- Transition or Success Message (Optional) -->
            <transition name="fade">
                <div v-if="form.recentlySuccessful" class="flex items-center space-x-2 text-emerald-600 bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                    <ShieldCheck class="w-4 h-4" />
                    <span class="text-xs font-bold uppercase tracking-wider">Configurações salvas com sucesso!</span>
                </div>
            </transition>

            <!-- Error Message -->
            <transition name="fade">
                <div v-if="form.hasErrors" class="flex items-center space-x-2 text-rose-600 bg-rose-50 p-3 rounded-lg border border-rose-100">
                    <AlertCircle class="w-4 h-4" />
                    <span class="text-xs font-bold uppercase tracking-wider">Erro ao salvar configurações.</span>
                </div>
            </transition>

            <!-- Action Button -->
            <div class="flex justify-end pt-2">
                <button
                    type="submit"
                    :disabled="form.processing || !form.isDirty"
                    class="inline-flex items-center justify-center px-8 py-3 bg-[#1a73e8] text-white text-sm font-bold uppercase tracking-widest rounded-full hover:bg-[#1557b0] focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-300 transform active:scale-[0.98] disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                >
                    <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                    <Save v-else class="w-4 h-4 mr-2" />
                    Salvar Alterações
                </button>
            </div>
        </form>
    </section>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
