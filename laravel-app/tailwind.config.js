import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-rose': {
                    DEFAULT: '#d02e5c',
                    vibrant: '#f33a6a',
                    muted: '#fceef2',
                    deep: '#9b1c3d',
                    surface: '#2d0a15',
                },
                'brand-teal': {
                    DEFAULT: '#00667e',
                    light: '#e6f0f2',
                    dark: '#004d5f',
                }
            }
        },
    },

    plugins: [forms],
};
