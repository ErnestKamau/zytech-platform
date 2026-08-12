/**
 * App frontend bootstrap.
 *
 * Livewire already ships Alpine. Starting a second Alpine instance from Vite
 * breaks Livewire form bindings (wire:submit never fires → login/register
 * appear to "do nothing" and only reload the same page).
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

try {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
    });
} catch (error) {
    console.warn('Laravel Echo failed to start.', error);
}

document.addEventListener('alpine:init', () => {
    Alpine.data('zyToasts', () => ({
        items: [],
        push(detail) {
            const toast = {
                id: Date.now() + Math.random(),
                title: detail?.title ?? 'Update',
                body: detail?.body ?? '',
            };
            this.items.unshift(toast);
            setTimeout(() => {
                this.items = this.items.filter((item) => item.id !== toast.id);
            }, 6000);
        },
    }));
});

function bindRealtimeNotifications() {
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (!window.Echo || !userId) {
        return;
    }

    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.NotificationPushed', (payload) => {
            window.dispatchEvent(new CustomEvent('zy-toast', {
                detail: {
                    title: payload.title ?? 'Notification',
                    body: payload.body ?? '',
                },
            }));
        });

    window.Echo.channel('platform.announcements')
        .listen('.NotificationPushed', (payload) => {
            window.dispatchEvent(new CustomEvent('zy-toast', {
                detail: {
                    title: payload.title ?? 'Announcement',
                    body: payload.body ?? '',
                },
            }));
        });
}

document.addEventListener('DOMContentLoaded', bindRealtimeNotifications);
