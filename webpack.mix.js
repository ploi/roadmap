const mix = require('laravel-mix')
const tailwindcss = require('tailwindcss');

mix
    .postCss('resources/css/admin.css', 'public/css/admin.css', [
        tailwindcss('tailwind.admin.config.js'),
    ])
    .postCss('resources/css/app.css', 'public/css/app.css', [
        tailwindcss('tailwind.config.js'),
    ])
    .js('resources/js/app.js', 'public/js/app.js')
    .version();
