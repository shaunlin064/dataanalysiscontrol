const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/helper.js', 'public/js')
    // .js('node_modules/bootstrap-select/dist/js/bootstrap-select.min.js','public/plugins/bootstrap/select')
    // .js('node_modules/bootstrap-select/dist/js/i18n/defaults-zh_TW.min.js','public/plugins/bootstrap/select/i18n')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/global.scss', 'public/css')
    .sass('resources/sass/login.scss', 'public/css');
mix.copy('node_modules/material-design-lite/material.min.css', 'public/plugins/material');

mix.copyDirectory('node_modules/pdfmake', 'public/plugins/pdfmake');
mix.copyDirectory('node_modules/jszip', 'public/plugins/jszip');
mix.copyDirectory('node_modules/datatables.net', 'public/plugins/datatables.net');
mix.copyDirectory('node_modules/datatables.net-bs', 'public/plugins/datatables.net-bs');
mix.copyDirectory('node_modules/datatables.net-bs4', 'public/plugins/datatables.net-bs4');
mix.copyDirectory('node_modules/datatables.net-buttons', 'public/plugins/datatables.net-buttons');
mix.copyDirectory('node_modules/datatables.net-buttons-bs4', 'public/plugins/datatables.net-buttons-bs4');
mix.copyDirectory('node_modules/datatables.net-select-bs4', 'public/plugins/datatables.net-select-bs4');
mix.copyDirectory('node_modules/@fullcalendar', 'public/plugins/fullcalendar');


// img
mix.copy('storage/app/public/gototop.png', 'public/img/');
