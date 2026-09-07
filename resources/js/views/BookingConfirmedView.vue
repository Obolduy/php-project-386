<script setup lang="ts">
import { useRoute } from 'vue-router';
import { formatWhen } from '../time';

const route = useRoute();

const title = String(route.query.title ?? 'Встреча');
const startsAt = String(route.query.startsAt ?? '');
const timezone = String(route.query.timezone ?? 'UTC');

const when = startsAt ? formatWhen(startsAt, timezone) : '';
</script>

<template>
    <h1 class="text-3xl font-semibold tracking-tight">Вы записаны</h1>

    <p class="mt-3 text-neutral-600">{{ title }}</p>
    <p v-if="when" class="mt-1 text-lg font-medium">{{ when }}</p>
    <p v-if="when" class="mt-1 text-sm text-neutral-600">Время указано в таймзоне {{ timezone }}</p>

    <RouterLink
        :to="{ name: 'meeting-types' }"
        class="mt-8 self-start rounded-xl border border-neutral-200 bg-white px-5 py-3 font-medium transition hover:border-neutral-400"
    >
        Записаться ещё раз
    </RouterLink>
</template>
