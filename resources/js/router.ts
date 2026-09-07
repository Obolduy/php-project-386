import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import MeetingTypesView from './views/MeetingTypesView.vue';
import SlotsView from './views/SlotsView.vue';

const routes: RouteRecordRaw[] = [
    { path: '/', name: 'meeting-types', component: MeetingTypesView },
    { path: '/meeting-types/:id', name: 'slots', component: SlotsView, props: true },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});
