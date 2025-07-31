<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    emails: Object
});
</script>

<template>
    <div class="divide-y">
        <div
            v-for="email in emails.data"
            :key="email.id"
            class="p-4 hover:bg-gray-50"
            :class="{ 'bg-white': !email.is_read, 'bg-gray-50': email.is_read }"
        >
            <Link :href="route('emails.show', email.id)" class="block">
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
                <div v-if="email.categories.length" class="mt-2 flex gap-1">
                    <span
                        v-for="category in email.categories"
                        :key="category.id"
                        class="px-2 py-1 text-xs rounded-full"
                        :style="{ backgroundColor: category.color }"
                    >
                        {{ category.name }}
                    </span>
                </div>
            </Link>
        </div>
    </div>
</template>
