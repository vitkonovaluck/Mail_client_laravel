<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AuthenticatedLayout.vue';
import FolderList from '@/Components/Email/FolderList.vue';
import EmailList from '@/Components/Email/EmailList.vue';

const props = defineProps({
    folders: Array,
    emails: Object,
    current_folder: Number
});
</script>

<template>
    <Head title="Пошта" />

    <AppLayout>
        <div class="flex h-full overflow-hidden">
            <!-- Бічна панель -->
            <FolderList
                :folders="folders"
                :current-folder="current_folder"
                class="w-64 border-r"
            />

            <!-- Основний вміст -->
            <EmailList
                :emails="emails"
                class="flex-1 overflow-y-auto"
            />
        </div>
    </AppLayout>
</template>
