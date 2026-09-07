import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import MeetingTypesView from './views/MeetingTypesView.vue';
import SlotsView from './views/SlotsView.vue';
import BookingConfirmedView from './views/BookingConfirmedView.vue';
import AdminBookingsView from './views/admin/BookingsView.vue';
import NewMeetingTypeView from './views/admin/NewMeetingTypeView.vue';

const routes: RouteRecordRaw[] = [
    { path: '/', name: 'meeting-types', component: MeetingTypesView },
    { path: '/meeting-types/:id', name: 'slots', component: SlotsView, props: true },
    { path: '/bookings/confirmed', name: 'booking-confirmed', component: BookingConfirmedView },
    { path: '/admin/meeting-types/new', name: 'admin-new-meeting-type', component: NewMeetingTypeView },
    { path: '/admin/bookings', name: 'admin-bookings', component: AdminBookingsView },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});
