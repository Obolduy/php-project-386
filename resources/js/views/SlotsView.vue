<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { bookingsCreate, meetingTypesList, meetingTypesSlots } from '../api';
import type { MeetingType, Slot } from '../api';
import { dayKey as dayKeyIn, formatDay as formatDayIn, formatTime as formatTimeIn } from '../time';

const props = defineProps<{ id: string }>();
const router = useRouter();

const meetingType = ref<MeetingType | null>(null);
const timezone = ref('');
const slots = ref<Slot[]>([]);
const selectedDay = ref('');
const selectedSlot = ref<Slot | null>(null);
const guestName = ref('');
const guestEmail = ref('');
const fields = ref<Record<string, string[]>>({});
const conflict = ref(false);
const failed = ref(false);
const bookingFailed = ref(false);
const notFound = ref(false);
const saving = ref(false);

const dayKey = (iso: string): string => dayKeyIn(iso, timezone.value);
const formatDay = (iso: string): string => formatDayIn(iso, timezone.value);
const formatTime = (iso: string): string => formatTimeIn(iso, timezone.value);

const days = computed(() => {
    const seen = new Map<string, string>();

    for (const slot of slots.value) {
        const key = dayKey(slot.startsAt);

        if (!seen.has(key)) {
            seen.set(key, slot.startsAt);
        }
    }

    return [...seen.entries()].map(([key, iso]) => ({ key, iso }));
});

const slotsOfSelectedDay = computed(() =>
    slots.value.filter((slot) => dayKey(slot.startsAt) === selectedDay.value),
);

async function loadSlots(): Promise<void> {
    const { data, error } = await meetingTypesSlots({ path: { id: Number(props.id) } });

    if (error !== undefined || data === undefined) {
        notFound.value = error?.code === 'not_found';
        failed.value = !notFound.value;

        return;
    }

    timezone.value = data.timezone;
    slots.value = data.slots;

    if (!days.value.some((day) => day.key === selectedDay.value)) {
        selectedDay.value = days.value[0]?.key ?? '';
    }
}

function selectSlot(slot: Slot): void {
    selectedSlot.value = slot;
    conflict.value = false;
    fields.value = {};
}

async function submit(): Promise<void> {
    if (selectedSlot.value === null) {
        return;
    }

    saving.value = true;
    conflict.value = false;
    bookingFailed.value = false;
    fields.value = {};

    const startsAt = selectedSlot.value.startsAt;

    const { data, error } = await bookingsCreate({
        body: {
            meetingTypeId: Number(props.id),
            startsAt,
            guestName: guestName.value,
            guestEmail: guestEmail.value,
        },
    });

    saving.value = false;

    if (error !== undefined) {
        if (error.code === 'slot_taken') {
            conflict.value = true;
            selectedSlot.value = null;
            await loadSlots();
        } else if (error.code === 'validation_failed') {
            fields.value = error.fields;
        } else {
            bookingFailed.value = true;
        }

        return;
    }

    if (data !== undefined) {
        await router.push({
            name: 'booking-confirmed',
            query: { title: meetingType.value?.title, startsAt, timezone: timezone.value },
        });
    }
}

onMounted(async () => {
    const [typesResult] = await Promise.all([meetingTypesList(), loadSlots()]);

    if (typesResult.data !== undefined) {
        meetingType.value = typesResult.data.find((type: MeetingType) => type.id === Number(props.id)) ?? null;
    }
});
</script>

<template>
    <RouterLink :to="{ name: 'meeting-types' }" class="text-sm text-neutral-600 hover:text-neutral-900">
        ← Все типы встречи
    </RouterLink>

    <h1 class="mt-4 text-3xl font-semibold tracking-tight">
        {{ meetingType?.title ?? 'Страница записи' }}
    </h1>

    <p v-if="meetingType" class="mt-2 text-neutral-600">{{ meetingType.description }}</p>

    <p v-if="notFound" class="mt-6 text-neutral-600">Такого типа встречи нет.</p>
    <p v-else-if="failed" class="mt-6 text-neutral-600">Не удалось загрузить свободное время.</p>

    <template v-else>
        <p v-if="timezone" class="mt-6 text-sm text-neutral-600">Время указано в таймзоне {{ timezone }}</p>

        <p v-if="conflict" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            Это время успели занять. Выберите другое — список обновлён.
        </p>

        <p v-if="days.length === 0 && timezone" class="mt-6 text-neutral-600">
            Свободного времени в ближайшие дни нет.
        </p>

        <div v-if="days.length" class="mt-4 flex flex-wrap gap-2">
            <button
                v-for="day in days"
                :key="day.key"
                type="button"
                class="rounded-lg border px-3 py-2 text-sm transition"
                :class="
                    day.key === selectedDay
                        ? 'border-neutral-900 bg-neutral-900 text-white'
                        : 'border-neutral-200 bg-white text-neutral-700 hover:border-neutral-400'
                "
                @click="selectedDay = day.key; selectedSlot = null"
            >
                {{ formatDay(day.iso) }}
            </button>
        </div>

        <ul v-if="slotsOfSelectedDay.length" class="mt-6 flex flex-col gap-2">
            <li v-for="slot in slotsOfSelectedDay" :key="slot.startsAt">
                <button
                    type="button"
                    class="w-full rounded-xl border px-5 py-3 text-center font-medium transition"
                    :class="
                        selectedSlot?.startsAt === slot.startsAt
                            ? 'border-neutral-900 bg-neutral-900 text-white'
                            : 'border-neutral-200 bg-white hover:border-neutral-400'
                    "
                    @click="selectSlot(slot)"
                >
                    {{ formatTime(slot.startsAt) }}
                </button>

                <form
                    v-if="selectedSlot?.startsAt === slot.startsAt"
                    class="mt-2 flex flex-col gap-3 rounded-xl border border-neutral-200 bg-white p-5"
                    @submit.prevent="submit"
                >
                    <label class="flex flex-col gap-1">
                        <span class="text-sm text-neutral-600">Как вас зовут</span>
                        <input v-model="guestName" type="text" class="rounded-lg border border-neutral-200 px-4 py-2">
                        <span v-for="message in fields.guestName" :key="message" class="text-sm text-red-600">
                            {{ message }}
                        </span>
                    </label>

                    <label class="flex flex-col gap-1">
                        <span class="text-sm text-neutral-600">Почта</span>
                        <input v-model="guestEmail" type="email" class="rounded-lg border border-neutral-200 px-4 py-2">
                        <span v-for="message in fields.guestEmail" :key="message" class="text-sm text-red-600">
                            {{ message }}
                        </span>
                    </label>

                    <p v-if="bookingFailed" class="text-sm text-red-600">
                        Не удалось записаться. Попробуйте ещё раз.
                    </p>

                    <button
                        type="submit"
                        :disabled="saving"
                        class="rounded-xl bg-neutral-900 px-5 py-3 font-medium text-white transition hover:bg-neutral-700 disabled:opacity-50"
                    >
                        Записаться на {{ formatTime(slot.startsAt) }}
                    </button>
                </form>
            </li>
        </ul>
    </template>
</template>
