const mix = require('laravel-mix');

mix .js('resources/js/production/setup/product-category/products-category.js', 'public/js/production')
    .js('resources/js/production/setup/product-brand/products-brand.js', 'public/js/production')
    .js('resources/js/production/setup/measure-unit/measure-unit.js', 'public/js/production')
    .js('resources/js/production/setup/products-entry/products-entry.js', 'public/js/production')
    .js('resources/js/production/setup/raw-material-ratio-setup/raw-material-ratio-setup.js', 'public/js/production')
    .js('resources/js/production/setup/assembling-setup/assembling-setup.js', 'public/js/production')
    .js('resources/js/production/setup/indirect-costs-type/indirect-costs-type.js', 'public/js/production')

    .js('resources/js/production/production-sections/requisition-for-rm/requisition-for-rm', 'public/js/production')
    .js('resources/js/production/production-sections/assembling/assembling-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/dryer/dryer-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/forming/forming-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/glaze/glaze-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/kiln/kiln-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/massbody/massbody-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/packing/packing-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/shapping/shapping-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/testing/testing-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/semi-finished-stock/semi-finished-stock.js', 'public/js/production')
    .js('resources/js/production/production-sections/finished-stock-section/finished-stock-section.js', 'public/js/production')
    .js('resources/js/production/production-sections/indirect-costs/indirect-costs.js', 'public/js/production')


    .js('resources/js/production/reports/massbody-reports/massbody-reports.js', 'public/js/production')
    .js('resources/js/production/reports/forming-reports/forming-reports.js', 'public/js/production')
    .js('resources/js/production/reports/shapping-reports/shapping-reports.js', 'public/js/production')
    .js('resources/js/production/reports/dryer-reports/dryer-reports.js', 'public/js/production')
    .js('resources/js/production/reports/glaze-reports/glaze-reports.js', 'public/js/production')
    .js('resources/js/production/reports/kiln-reports/kiln-reports.js', 'public/js/production')
    .js('resources/js/production/reports/finished-reports/finished-reports.js', 'public/js/production')
    .js('resources/js/production/reports/assembling-reports/assembling-reports.js', 'public/js/production')
    .js('resources/js/production/reports/semi-finished-report/semi-finished-reports.js', 'public/js/production')
    .js('resources/js/production/reports/testing-hv-reports/testing-reports.js', 'public/js/production')
    .js('resources/js/production/reports/cementing-report/cementing-reports.js', 'public/js/production')
    .js('resources/js/production/reports/cost-of-production-reports/cost-of-production-reports.js', 'public/js/production')


