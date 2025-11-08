const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
   .vue({ version: 2 })
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
   ])
   .webpackConfig({
       resolve: {
           alias: {
               '@': path.resolve(__dirname, 'resources/js'),             // base js folder
               '@components': path.resolve(__dirname, 'resources/js/components'), // components folder
               '@pages': path.resolve(__dirname, 'resources/js/pages'),         // pages folder
           }
       }
   })
   .disableNotifications(); // optional, remove the Windows notifier warning

if (mix.inProduction()) {
    mix.version();
}
