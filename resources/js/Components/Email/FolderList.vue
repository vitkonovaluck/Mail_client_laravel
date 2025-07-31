<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    folders: Array,
    currentFolder: Number
});
</script>

<template>
    <div class="p-4">
        <ul>
            <li v-for="folder in folders" :key="folder.id">
                <Link
                    :href="route('dashboard', { folder_id: folder.id })"
                    class="flex items-center justify-between px-4 py-2 rounded-lg"
                    :class="{
                        'bg-blue-50 text-blue-600': folder.id === currentFolder
                    }"
                >
                    <span>{{ folder.name }}</span>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">
                        {{ folder.emails_count }}
                    </span>
                </Link>
            </li>
        </ul>
    </div>
</template>
