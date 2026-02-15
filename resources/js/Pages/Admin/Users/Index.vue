<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Trash2, Edit2, UserPlus, ShieldCheck, Search, Users, UserCheck } from 'lucide-vue-next';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    users: Array,
    filters: Object,
    total_users: Number,
    active_users: Number,
});

const showConfirmDelete = ref(false);
const userToDelete = ref(null);
const isDeleting = ref(false);

const confirmDeleteAction = (user) => {
    userToDelete.value = user;
    showConfirmDelete.value = true;
};

const deleteUser = () => {
    if (!userToDelete.value) return;

    isDeleting.value = true;
    router.delete(route('admin.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showConfirmDelete.value = false;
            userToDelete.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};

const searchQuery = ref(props.filters?.search || '');

// Search Logic with debounce
let timeout;
watch(searchQuery, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route('admin.users.index'),
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true
            }
        );
    }, 300);
});

const getAvatarColor = (name) => {
    const colors = [
        'bg-indigo-50 text-indigo-600 border-indigo-100',
        'bg-rose-50 text-rose-600 border-rose-100',
        'bg-emerald-50 text-emerald-600 border-emerald-100',
        'bg-amber-50 text-amber-600 border-amber-100',
        'bg-sky-50 text-sky-600 border-sky-100',
        'bg-violet-50 text-violet-600 border-violet-100',
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};
</script>

<template>
    <Head :title="$t('Users Management')" />

    <DashboardLayout>
        <div class="py-12 bg-[#f8fafc] min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Page Header & Stats Row -->
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center">
                            <span class="bg-indigo-600 w-2 h-10 rounded-full mr-4"></span>
                            {{ $t('Users Management') }}
                        </h1>
                        <p class="mt-3 text-slate-500 font-medium text-lg">{{ $t('Manage your organization\'s users, roles and platform access.') }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Stats Mini Cards -->
                        <div class="flex items-center space-x-4 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
                            <div class="flex items-center px-4 py-2 border-r border-slate-100">
                                <Users class="w-4 h-4 text-indigo-500 mr-2" />
                                <span class="text-sm font-black text-slate-800">{{ total_users }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1.5 mt-0.5">Total</span>
                            </div>
                            <div class="flex items-center px-4 py-2">
                                <UserCheck class="w-4 h-4 text-emerald-500 mr-2" />
                                <span class="text-sm font-black text-slate-800">{{ active_users }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1.5 mt-0.5">Ativos</span>
                            </div>
                        </div>

                        <Link :href="route('admin.users.create')">
                            <PrimaryButton class="px-8 py-3.5 text-sm font-black uppercase tracking-[0.15em] rounded-xl bg-indigo-600 shadow-xl shadow-indigo-100 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <UserPlus class="w-5 h-5 mr-2.5" />
                                {{ $t('Add New User') }}
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <!-- Filters & Search Bar -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <Search class="h-5 w-5 text-slate-400" />
                        </div>
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Buscar por nome ou email..."
                            class="block w-full pl-11 pr-4 py-3.5 rounded-xl bg-white border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50/50 transition-all font-medium text-slate-600 placeholder-slate-400 border shadow-sm"
                        />
                    </div>
                </div>

                <!-- User Listing Table -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden transition-all hover:shadow-md">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th scope="col" class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">{{ $t('Name') }}</th>
                                    <th scope="col" class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">{{ $t('Email') }}</th>
                                    <th scope="col" class="px-8 py-6 text-center text-xs font-black text-slate-400 uppercase tracking-[0.2em]">{{ $t('Role') }}</th>
                                    <th scope="col" class="px-8 py-6 text-center text-xs font-black text-slate-400 uppercase tracking-[0.2em]">{{ $t('Status') }}</th>
                                    <th scope="col" class="px-8 py-6 text-center text-xs font-black text-slate-400 uppercase tracking-[0.2em]">{{ $t('Criado em') }}</th>
                                    <th scope="col" class="px-8 py-6 text-center text-xs font-black text-slate-400 uppercase tracking-[0.2em] transition-all">{{ $t('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr
                                    v-for="user in users"
                                    :key="user.id"
                                    class="hover:bg-indigo-50/20 group transition-all duration-300"
                                >
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-12 w-12 rounded-lg border flex items-center justify-center font-black text-sm shadow-sm transition-transform duration-300 group-hover:scale-110"
                                                :class="getAvatarColor(user.name)"
                                            >
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-black text-slate-800 tracking-tight group-hover:text-indigo-600 transition-colors">{{ user.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        <div class="text-sm text-slate-500 font-bold tracking-tight">{{ user.email }}</div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-center">
                                        <div v-if="user.is_master" class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border-2 border-indigo-100/50 shadow-sm">
                                            <ShieldCheck class="w-3 h-3 mr-1.5" />
                                            {{ $t('Master') }}
                                        </div>
                                        <div v-else class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-50 text-slate-500 border-2 border-slate-100/50">
                                            {{ $t('User') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-center">
                                        <div v-if="user.is_active !== false" class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border-2 border-emerald-100/50">
                                            <div class="w-1.5 h-1.5 bg-emerald-600 rounded-full mr-2 animate-pulse"></div>
                                            {{ $t('Active') }}
                                        </div>
                                        <div v-else class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 border-2 border-rose-100/50">
                                            <div class="w-1.5 h-1.5 bg-rose-600 rounded-full mr-2"></div>
                                            {{ $t('Inactive') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-center">
                                        <div class="text-[11px] text-slate-400 font-black uppercase tracking-widest bg-slate-50/50 px-3 py-1 rounded-lg inline-block border border-slate-100">
                                            {{ formatDate(user.created_at) }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-right font-medium">
                                        <div class="flex items-center justify-center space-x-1">
                                            <Link
                                                :href="route('admin.users.edit', user.id)"
                                                class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-white hover:shadow-sm hover:border-slate-200 border border-transparent rounded-xl transition-all active:scale-90"
                                                title="Editar Usuário"
                                            >
                                                <Edit2 class="w-4 h-4" />
                                            </Link>
                                            <button
                                                @click="confirmDeleteAction(user)"
                                                class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 border border-transparent rounded-xl transition-all active:scale-90"
                                                title="Excluir Usuário"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Empty State -->
                                <tr v-if="users.length === 0">
                                    <td colspan="6" class="px-8 py-20 text-center">
                                        <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 mb-4">
                                            <Search class="w-8 h-8 text-slate-300" />
                                        </div>
                                        <h3 class="text-lg font-black text-slate-800">{{ $t('Nenhum usuário encontrado') }}</h3>
                                        <p class="text-slate-500 font-medium mt-1">Tente ajustar sua busca ou limpar os filtros.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reusable Confirmation Modal -->
        <ConfirmationModal
            :show="showConfirmDelete"
            :title="$t('Excluir Usuário')"
            :message="$t('Tem certeza que deseja excluir este usuário? Esta ação não poderá ser desfeita.')"
            :confirm-text="$t('Excluir')"
            :cancel-text="$t('Cancelar')"
            :loading="isDeleting"
            @close="showConfirmDelete = false"
            @confirm="deleteUser"
        />
    </DashboardLayout>
</template>
