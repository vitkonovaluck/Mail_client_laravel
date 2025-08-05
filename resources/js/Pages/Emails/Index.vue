<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AuthenticatedLayout.vue';
import FolderList from '@/Components/Email/FolderList.vue';
import EmailList from '@/Components/Email/EmailList.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    folders: Array,
    emails: Object,
    current_folder: Number,
    perPage: Number
});

const emit = defineEmits(['update:perPage']);

const localPerPage = ref(props.perPage);
const selectedEmails = ref([]);
const selectedAction = ref('');
const targetFolder = ref('');

const perPageOptions = [10, 25, 50, 100];

// Спостерігаємо за змінами localPerPage
watch(localPerPage, (newValue) => {
    emit('update:perPage', newValue);
    window.location.href = route('emails.index', { per_page: newValue });
});
</script>

<template>
    <Head title="Пошта" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span>Поштова скринька: </span>
                <span class="email-address"> {{ $page.props.auth.user.email }}</span>
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex h-full overflow-hidden bg-white rounded-lg shadow">
                    <!-- Бічна панель -->
                    <FolderList
                        :folders="folders"
                        :current-folder="current_folder"
                        class="w-64 border-r"
                    />

                    <!-- Основний вміст -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <!-- Панель дій -->
                        <div class="border-b p-2 flex items-center justify-between bg-gray-50">
                            <div class="flex space-x-2">
                                <select
                                    v-model="selectedAction"
                                    class="border rounded px-2 py-1"
                                >
                                    <option value="">Оберіть дію</option>
                                    <option value="delete">Видалити</option>
                                    <option value="mark_unread">Позначити як непрочитане</option>
                                    <option value="move">Перемістити</option>
                                </select>

                                <select
                                    v-if="selectedAction === 'move'"
                                    v-model="targetFolder"
                                    class="border rounded px-2 py-1"
                                >
                                    <option value="">Оберіть папку</option>
                                    <option
                                        v-for="folder in folders"
                                        :key="folder.id"
                                        :value="folder.id"
                                    >
                                        {{ folder.name }}
                                    </option>
                                </select>

                                <button
                                    v-if="selectedAction"
                                    @click="handleBulkAction"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"
                                >
                                    Застосувати
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span>Листів на сторінці:</span>
                                <select
                                    v-model="localPerPage"
                                    @change="$inertia.get(route('emails.index', {per_page: perPage}))"
                                    class="border rounded px-2 py-1"
                                >
                                    <option
                                        v-for="option in perPageOptions"
                                        :key="option"
                                        :value="option"
                                    >
                                        {{ option }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Список листів -->
                        <EmailList
                            :emails="emails"
                            :selected-emails="selectedEmails"
                            class="flex-1 overflow-y-auto"
                        />

                        <!-- Пагінація -->
                        <div class="border-t p-2 bg-gray-50 flex justify-between items-center">
                            <div>
                                Показано {{ emails.from }} - {{ emails.to }} з {{ emails.total }} листів
                            </div>
                            <div class="flex space-x-1">
                                <Link
                                    v-for="link in emails.links"
                                    :href="link.url || '#'"
                                    :class="{
                                        'bg-blue-500 text-white': link.active,
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                    class="px-3 py-1 border rounded"
                                    preserve-scroll
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.email-header {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.email-address {
    font-size: 1.3rem;
    color: #235830;
    font-weight: bold;
}
</style>
