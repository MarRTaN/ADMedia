const elixir = require('laravel-elixir');

require('laravel-elixir-vue');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss');

    mix.styles([
        "libs/bootstrap/dist/css/bootstrap.min.css",
        "libs/font-awesome/css/font-awesome.min.css",
        "libs/template/push-menu.css",
        "libs/template/intro.css",
        "libs/template/main.css",
        "libs/template/custom.css"
    ], 'public/build/css/app.css', 'resources/assets');

    mix.scripts([
        "libs/jquery/dist/jquery.min.js",
        "libs/bootstrap/dist/js/bootstrap.min.js",
        "js/custom.js"
    ], 'public/build/js/app.js', 'resources/assets');

    mix.copy('resources/assets/libs/font-awesome/fonts', 'public/build/fonts');

    //   .webpack('app.js');

    mix.sass('fileupload.scss', 'public/css/fileupload.css');
    mix.sass('player.scss', 'public/css/player.css');
});
