<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { adminBookingsList, meetingTypesList, meetingTypesSlots } from '../../api';
import type { Booking, MeetingType } from '../../api';
import { formatWhen } from '../../time';

const bookings = ref<Booking[]>([]);
const titles = ref<Record<number, string>>({});
const timezone = ref('');
const loaded = ref(false);
const failed = ref(false);

onMounted(async () => {
    const [bookingsResult, typesResult] = await Promise.all([adminBookingsList(), meetingTypesList()]);

    if (bookingsResult.error !== undefined || bookingsResult.data === undefined) {
        failed.value = true;

        return;
    }

    bookings.value = bookingsResult.data;

    if (typesResult.data !== undefined) {
        titles.value = Object.fromEntries(
            typesResult.data.map((type: MeetingType) => [type.id, type.title]),
        );

        const firstType = typesResult.data[0];

        if (firstType !== undefined) {
            const { data } = await meetingTypesSlots({ path: { id: firstType.id } });
            timezone.value = data?.timezone ?? '';
        }
    }

    loaded.value = true;
});
</script>

<template>
    <h1 class="text-3xl font-semibold tracking-tight">Предстоящие встречи</h1>

    <p v-if="timezone" class="mt-3 text-sm text-neutral-600">Время указано в таймзоне {{ timezone }}</p>

    <p v-if="failed" class="mt-6 text-neutral-600">Не удалось загрузить список встреч.</p>

    <p v-else-if="loaded && bookings.length === 0" class="mt-6 text-neutral-600">
        Предстоящих встреч пока нет. Как только кто-то запишется, встреча появится здесь.
    </p>

    <ul v-else class="mt-6 flex flex-col gap-2">
        <li
            v-for="booking in bookings"
            :key="booking.id"
            class="rounded-xl border border-neutral-200 bg-white px-5 py-4"
        >
            <p class="font-medium">{{ formatWhen(booking.startsAt, timezone) }}</p>
            <p class="text-sm text-neutral-600">{{ titles[booking.meetingTypeId] ?? 'Встреча' }}</p>
            <p class="mt-2 text-sm">{{ booking.guestName }} · {{ booking.guestEmail }}</p>
        </li>
    </ul>
</template>
