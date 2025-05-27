/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Ensure CSRF token is sent with every request
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    // Also set for jQuery if using it
    if (window.jQuery) {
        window.jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token.content
            }
        });
    }
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Add axios request interceptor to include the XSRF-TOKEN cookie in the X-XSRF-TOKEN header
axios.interceptors.request.use(function (config) {
    // Get the XSRF-TOKEN cookie
    const xsrfToken = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1];
        
    if (xsrfToken) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
    }

    // TẮT cache cho real-time data
    if (config.method === 'get') {
        config.headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
        config.headers['Pragma'] = 'no-cache';
        config.headers['Expires'] = '0';
    }
    
    return config;
});

// Add client-side caching using localStorage for GET requests
const requestCache = {
    cache: {},
    
    async get(key) {
        // Check if cache exists in localStorage
        const cachedData = localStorage.getItem(`axios_cache_${key}`);
        if (cachedData) {
            const data = JSON.parse(cachedData);
            // Check if cache is still valid
            if (data.expiry > Date.now()) {
                return data.value;
            }
            // Clear expired cache
            localStorage.removeItem(`axios_cache_${key}`);
        }
        return null;
    },
    
    set(key, value, ttl = 0) { // TẮT cache localStorage
        const data = {
            value,
            expiry: Date.now() + ttl
        };
        
        try {
            localStorage.setItem(`axios_cache_${key}`, JSON.stringify(data));
        } catch (e) {
            // In case localStorage is full, clear all cache
            this.clear();
        }
    },
    
    clear() {
        Object.keys(localStorage)
            .filter(key => key.startsWith('axios_cache_'))
            .forEach(key => localStorage.removeItem(key));
    }
};

// DISABLE response caching cho real-time data
axios.interceptors.response.use(response => {
    // TẮT hoàn toàn cache
    return response;
});

// DISABLE request caching cho real-time data
axios.interceptors.request.use(async config => {
    // TẮT hoàn toàn check cache - luôn fetch mới
    return config;
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
