<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    email: Object,
    folders: Array
});

const markAsUnread = () => {
    Inertia.post(route('emails.mark-unread', {id: props.email.id}));
};
</script>

<template>
    <Head :title="email.subject" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6 border-b flex justify-between items-center">
                    <h1 class="text-2xl font-bold">{{ email.subject }}</h1>
                    <div class="flex space-x-2">
                        <button
                            @click="markAsUnread"
                            class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded"
                        >
                            Позначити як непрочитане
                        </button>
                        <Link
                            :href="route('emails.forward', email.id)"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"
                        >
                            Переслати
                        </Link>
                        <Link
                            :href="route('emails.destroy', email.id)"
                            method="delete"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                        >
                            Видалити
                        </Link>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <div>
                            <strong>Від:</strong> {{ email.from }}<br>
                            <strong>Кому:</strong> {{ email.to }}<br>
                            <strong>Дата:</strong> {{ new Date(email.received_at).toLocaleString() }}
                        </div>
                        <select
                            v-model="targetFolder"
                            @change="moveEmail"
                            class="border rounded px-2 py-1"
                        >
                            <option value="">Перемістити в...</option>
                            <option
                                v-for="folder in folders"
                                :key="folder.id"
                                :value="folder.id"
                            >
                                {{ folder.name }}
                            </option>
                        </select>
                    </div>

                    <div class="email-body" v-html="email.body_html || email.body"></div>

<!--                    <div v-if="email.attachments.length" class="mt-6">-->
<!--                        <h3 class="font-bold mb-2">Вкладення:</h3>-->
<!--                        <div class="flex flex-wrap gap-2">-->
<!--                            <a-->
<!--                                v-for="attachment in email.attachments"-->
<!--                                :key="attachment.id"-->
<!--                                :href="route('attachments.download', attachment.id)"-->
<!--                                class="border px-3 py-1 rounded flex items-center"-->
<!--                            >-->
<!--                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />-->
<!--                                </svg>-->
<!--                                {{ attachment.filename }} ({{ (attachment.size / 1024).toFixed(1) }} KB)-->
<!--                            </a>-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</template>
