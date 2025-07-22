import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                'pokemon': {
                    'red': '#EE1515',
                    'black': '#222224',
                },
                'konbini': {
                    'green': '#4CAF50',
                    'orange': '#FF9800',
                },
                'brand': {
                    'white': '#FAFAFA',
                    'gray': {
                        100: '#F5F5F5',
                        200: '#EEEEEE',
                        300: '#E0E0E0',
                        400: '#BDBDBD',
                        500: '#9E9E9E',
                        600: '#757575',
                        700: '#616161',
                        800: '#424242',
                        900: '#212121',
                    }
                }
            },
            fontFamily: {
                'display': ['Nunito', 'sans-serif'],
                'body': ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [forms],
}
