<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-lg font-semibold text-gray-900 tracking-tight">
                Profile
            </h2>
        </template>

        <div class="py-12 bg-gray-50 h-full overflow-y-auto">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-6 border border-gray-200 shadow-sm sm:rounded-2xl">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-6 border border-gray-200 shadow-sm sm:rounded-2xl">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white p-6 border border-gray-200 shadow-sm sm:rounded-2xl">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
