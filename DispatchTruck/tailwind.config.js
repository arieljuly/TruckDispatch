/** @type {import('tailwindcss').Config} */
export default {
    darkMode: false,
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Livewire/**/*.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
