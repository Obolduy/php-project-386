<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { adminMeetingTypesCreate } from '../../api';

const router = useRouter();

const title = ref('');
const description = ref('');
const durationMinutes = ref(30);
const fields = ref<Record<string, string[]>>({});
const failed = ref(false);
const saving = ref(false);

async function submit(): Promise<void> {
    saving.value = true;
    fields.value = {};
    failed.value = false;

    const { data, error } = await adminMeetingTypesCreate({
        body: {
            title: title.value,
            description: description.value,
            durationMinutes: durationMinutes.value,
        },
    });

    saving.value = false;

    if (error !== undefined) {
        if (error.code === 'validation_failed') {
            fields.value = error.fields;
        } else {
            failed.value = true;
        }

        return;
    }

    if (data !== undefined) {
        await router.push({ name: 'meeting-types' });
    }
}
</script>

<template>
    <h1 class="text-3xl font-semibold tracking-tight">Новый тип встречи</h1>

    <form class="mt-6 flex flex-col gap-4" @submit.prevent="submit">
        <label class="flex flex-col gap-1">
            <span class="text-sm text-neutral-600">Название</span>
            <input
                v-model="title"
                type="text"
                class="rounded-lg border border-neutral-200 bg-white px-4 py-2"
            >
            <span v-for="message in fields.title" :key="message" class="text-sm text-red-600">
                {{ message }}
            </span>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm text-neutral-600">Описание</span>
            <textarea
                v-model="description"
                rows="3"
                class="rounded-lg border border-neutral-200 bg-white px-4 py-2"
            ></textarea>
            <span v-for="message in fields.description" :key="message" class="text-sm text-red-600">
                {{ message }}
            </span>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm text-neutral-600">Длительность в минутах</span>
            <input
                v-model.number="durationMinutes"
                type="number"
                class="rounded-lg border border-neutral-200 bg-white px-4 py-2"
            >
            <span v-for="message in fields.durationMinutes" :key="message" class="text-sm text-red-600">
                {{ message }}
            </span>
        </label>

        <p v-if="failed" class="text-sm text-red-600">Не удалось сохранить тип встречи.</p>

        <button
            type="submit"
            :disabled="saving"
            class="mt-2 rounded-xl bg-neutral-900 px-5 py-3 font-medium text-white transition hover:bg-neutral-700 disabled:opacity-50"
        >
            Создать
        </button>
    </form>
</template>
