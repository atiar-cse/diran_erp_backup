const mix = require('laravel-mix');

mix
    /*Production voucher Section*/

    .js('resources/js/accounts/acc-product-section/req-for-rm/acc-req-for-rm.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/massbody/acc-massbody-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/packing/acc-packing-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/assembling/acc-assembling-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/finished-stock-section/acc-finished-stock-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/testing/acc-testing-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/kiln/acc-kiln-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/glaze/acc-glaze-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/dryer/acc-dryer-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/shapping/acc-shapping-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-product-section/forming/acc-forming-section.js', 'public/js/account')






