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

require('./webpacks/webpack.mix.production.js');
require('./webpacks/webpack.mix.accounts.js');

require('./webpacks/webpack.mix.accounts_production.js');


require('./webpacks/webpack.mix.purchase.js');
require('./webpacks/webpack.mix.sale.js');
require('./webpacks/webpack.mix.inventory.js');
require('./webpacks/webpack.mix.lc.js');
require('./webpacks/webpack.mix.hr.js');
require('./webpacks/webpack.mix.payroll.js');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/assets/css');
