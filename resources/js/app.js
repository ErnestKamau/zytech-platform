/**
 * App frontend bootstrap.
 *
 * Livewire already ships Alpine. Starting a second Alpine instance from Vite
 * breaks Livewire form bindings (wire:submit never fires → login/register
 * appear to "do nothing" and only reload the same page).
 */

import { DotLottie } from '@lottiefiles/dotlottie-web';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

function mountLotties(root = document) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    root.querySelectorAll('[data-zy-lottie]').forEach((canvas) => {
        if (! (canvas instanceof HTMLCanvasElement) || canvas.dataset.zyLottieMounted === '1') {
            return;
        }

        canvas.dataset.zyLottieMounted = '1';

        new DotLottie({
            canvas,
            src: canvas.dataset.src ?? '',
            loop: canvas.dataset.loop !== '0',
            autoplay: ! reducedMotion && canvas.dataset.autoplay !== '0',
        });
    });
}

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

document.addEventListener('DOMContentLoaded', () => {
    mountLotties();
    bindRealtimeNotifications();
});

document.addEventListener('livewire:navigated', () => mountLotties());
