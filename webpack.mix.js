const mix = require('laravel-mix')

mix
    .postCss('resources/css/admin.css', 'public/css/admin.css', [
        require('tailwindcss'),
    ])
    .postCss('resources/css/app.css', 'public/css/app.css', [
        require('tailwindcss'),
    ])
    .js('resources/js/app.js', 'public/js/app.js');
