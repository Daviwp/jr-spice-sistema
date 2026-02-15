<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PowerBIEmbed from '@/Components/PowerBIEmbed.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Activity, TrendingUp, Globe } from 'lucide-vue-next';

const props = defineProps(['embedConfig']);
const user = usePage().props.auth.user;
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
