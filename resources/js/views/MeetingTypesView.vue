<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { meetingTypesList } from '../api';
import type { MeetingType } from '../api';

const meetingTypes = ref<MeetingType[]>([]);
const failed = ref(false);

onMounted(async () => {
    const { data, error } = await meetingTypesList();

    if (error !== undefined || data === undefined) {
        failed.value = true;

        return;
    }

    meetingTypes.value = data;
});
</script>

<template>
    <h1 class="text-3xl font-semibold tracking-tight">Запись на звонок</h1>

    <p class="mt-3 text-neutral-600">
        Выберите свободное время в календаре и запишитесь на разговор с владельцем календаря.
    </p>

    <p v-if="failed" class="mt-8 text-neutral-600">Не удалось загрузить типы встречи.</p>

    <RouterLink
        v-for="meetingType in meetingTypes"
        :key="meetingType.id"
        :to="{ name: 'slots', params: { id: meetingType.id } }"
        class="mt-4 flex items-center justify-between rounded-xl border border-neutral-200 bg-white px-5 py-4 transition hover:border-neutral-400"
    >
        <span>
            <span class="block font-medium">{{ meetingType.title }}</span>
            <span class="block text-sm text-neutral-600">{{ meetingType.description }}</span>
        </span>
        <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm text-neutral-600">
            {{ meetingType.durationMinutes }} мин
        </span>
    </RouterLink>
</template>
