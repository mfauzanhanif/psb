import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Laravel Echo with Pusher for real-time broadcasting
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

/**
 * Listen for new student registrations and refresh Filament table
 */
window.Echo.channel('registrations')
    .listen('.student.registered', (e) => {
        console.log('New registration received:', e);

        // Dispatch Livewire refresh event for Filament tables
        if (window.Livewire) {
            window.Livewire.dispatch('$refresh');
        }

        // Show browser notification if page is in background
        if (document.hidden && Notification.permission === 'granted') {
            new Notification('Pendaftaran Baru!', {
                body: `${e.full_name} - ${e.registration_number}`,
                icon: '/favicon.ico'
            });
        }
    });
