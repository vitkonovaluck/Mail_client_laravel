<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    features: Array,
    pricing: Object
});
</script>

<template>
    <Head title="Електронна пошта нового покоління" />

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
        <!-- Header -->
        <header class="container mx-auto px-6 py-8">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold text-blue-600">MailService</div>
                <nav class="flex space-x-8">
                    <template v-if="auth.user">
                        <PrimaryButton href="/dashboard" class="px-4 py-2">
                            Перейти до пошти
                        </PrimaryButton>
                    </template>
                    <template v-else>
                        <Link href="/login" class="text-gray-600 hover:text-blue-600">Увійти</Link>
                        <Link href="/register" class="text-gray-600 hover:text-blue-600">Реєстрація</Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="container mx-auto px-6 py-16 text-center">
            <h1 class="text-5xl font-bold text-gray-800 mb-6">Сучасна електронна пошта для бізнесу</h1>
            <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                Надійний, безпечний та зручний поштовий сервіс з підтримкою всіх сучасних технологій
            </p>
            <PrimaryButton class="px-8 py-4 text-lg" href="/register">
                Спробувати безкоштовно
            </PrimaryButton>
        </section>

        <!-- Features Section -->
        <section class="container mx-auto px-6 py-16 bg-white rounded-xl shadow-sm">
            <h2 class="text-3xl font-bold text-center mb-12">Наші переваги</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div v-for="(feature, index) in features" :key="index" class="p-6 border border-gray-100 rounded-lg hover:shadow-md transition">
                    <div class="text-blue-500 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">{{ feature }}</h3>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="container mx-auto px-6 py-16">
            <h2 class="text-3xl font-bold text-center mb-12">Тарифи</h2>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div v-for="(plan, name) in pricing" :key="name"
                     class="p-8 border rounded-lg"
                     :class="{'border-blue-300 bg-blue-50': name === 'pro', 'border-gray-200': name !== 'pro'}">
                    <h3 class="text-2xl font-bold mb-4 capitalize">{{ name }}</h3>
                    <div class="text-4xl font-bold mb-6">
                        {{ plan.price === '0' ? 'Безкоштовно' : `$${plan.price}/міс` }}
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li v-for="(feature, index) in plan.features" :key="index" class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ feature }}
                        </li>
                    </ul>
                    <PrimaryButton class="w-full" :class="{'bg-blue-600 hover:bg-blue-700': name === 'pro'}">
                        Обрати тариф
                    </PrimaryButton>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-6 md:mb-0">
                        <div class="text-2xl font-bold mb-2">MailService</div>
                        <p class="text-gray-400">© 2023 Всі права захищені</p>
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="hover:text-blue-400">Умови використання</a>
                        <a href="#" class="hover:text-blue-400">Політика конфіденційності</a>
                        <a href="#" class="hover:text-blue-400">Допомога</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
