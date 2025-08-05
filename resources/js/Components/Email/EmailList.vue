<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    emails: Object,
    selectedEmails: Array
});

const emit = defineEmits(['update:selectedEmails']);

const toggleSelect = (emailId) => {
    const index = props.selectedEmails.indexOf(emailId);
    if (index === -1) {
        emit('update:selectedEmails', [...props.selectedEmails, emailId]);
    } else {
        emit('update:selectedEmails', props.selectedEmails.filter(id => id !== emailId));
    }
};
</script>

<template>
    <div class="divide-y">
        <div
            v-for="email in emails.data"
            :key="email.id"
            class="p-4 hover:bg-gray-50 flex items-start"
            :class="{ 'bg-blue-50': selectedEmails.includes(email.id) }"
        >
            <input
                type="checkbox"
                :checked="selectedEmails.includes(email.id)"
                @change="toggleSelect(email.id)"
                class="mt-1 mr-2"
            >

            <Link :href="route('emails.show', email.id)" class="flex-1 block">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ email.from }}</p>
                        <p class="text-gray-600 text-sm">{{ email.subject }}</p>
                    </div>
                    <span class="text-xs text-gray-500">
                        {{ new Date(email.received_at).toLocaleTimeString() }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                    {{ email.body }}
                </p>
            </Link>

            <div class="ml-2 flex space-x-1">
                <Link
                    :href="route('emails.forward', email.id)"
                    class="text-gray-400 hover:text-gray-600"
                    title="Переслати"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18m-5-5h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </Link>
                <Link
                    :href="route('emails.destroy', email.id)"
                    method="delete"
                    class="text-gray-400 hover:text-gray-600"
                    title="Видалити"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </Link>
            </div>
        </div>
    </div>
</template>
