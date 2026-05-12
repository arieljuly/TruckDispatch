/** @type {import('tailwindcss').Config} */
export default {
    darkMode: false,
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Livewire/**/*.php",
        "./app/Livewire/**/*.php", 
        "./app/View/Components/**/*.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
