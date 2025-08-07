<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    to: '',
    subject: '',
    body: '',
    attachments: []
});

const submit = () => {
    form.post(route('emails.send'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4">
        <div>
            <label>Кому:</label>
            <input v-model="form.to" type="email" required>
        </div>

        <div>
            <label>Тема:</label>
            <input v-model="form.subject" required>
        </div>

        <div>
            <label>Текст:</label>
            <textarea v-model="form.body" rows="5" required></textarea>
        </div>

        <div>
            <label>Вкладення:</label>
            <input type="file" multiple @input="form.attachments = $event.target.files">
        </div>

        <button type="submit" :disabled="form.processing">
            Надіслати
        </button>
    </form>
</template>
