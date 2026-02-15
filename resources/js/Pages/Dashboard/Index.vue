<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PowerBIEmbed from '@/Components/PowerBIEmbed.vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps(['embedConfig']);
const user = usePage().props.auth.user;

</script>

<template>
    <Head title="Dashboard" />

    <DashboardLayout>
        <div class="h-full w-full">
            <PowerBIEmbed
                :embed-config="embedConfig"
                :hidden-pages="['HOME', 'CONTATO', 'EXPORT']"
            />
        </div>
    </DashboardLayout>
</template>
