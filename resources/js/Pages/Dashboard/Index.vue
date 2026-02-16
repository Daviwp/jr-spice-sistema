<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PowerBIEmbed from '@/Components/PowerBIEmbed.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ShieldAlert, Clock, Layout } from 'lucide-vue-next';

const props = defineProps(['embedConfig']);
const user = usePage().props.auth.user;

// Only show reports if user is master or has allowed pages
const hasAccess = user.is_master || (user.allowed_pages && user.allowed_pages.length > 0);
</script>

<template>
    <Head :title="$t('Dashboard')" />

    <DashboardLayout>
        <div class="h-full w-full flex flex-col bg-[#f8fafc]">
            <!-- Embed Section -->
            <div class="flex-1 min-h-[600px] bg-white relative">
                <PowerBIEmbed
                    :embed-config="embedConfig"
                    :hidden-pages="['HOME', 'CONTATO', 'EXPORT']"
                />
            </div>
        </div>
    </DashboardLayout>
</template>
