const mix = require('laravel-mix');

mix.js('resources/js/lc/lc-voucher-type/lc-voucher-type.js', 'public/js/lc')
    .js('resources/js/lc/lc-cost-name-entry/lc-cost-name-entry.js', 'public/js/lc')
    .js('resources/js/lc/lc-opening-section/lc-opening-section.js', 'public/js/lc')
    .js('resources/js/lc/lc-insurance/lc-insurance.js', 'public/js/lc')
    .js('resources/js/lc/others-charge-entry/others-charge-entry.js', 'public/js/lc')
    .js('resources/js/lc/work-order-import/work-order-import.js', 'public/js/lc')
    .js('resources/js/lc/latr-entry/latr-entry.js', 'public/js/lc')
    .js('resources/js/lc/custom-duty-charge-entry/custom-duty-charge-entry.js', 'public/js/lc')
    .js('resources/js/lc/proforma-invoice-entry/proforma-invoice-entry.js', 'public/js/lc')
    .js('resources/js/lc/commercial-invoice-entry/commercial-invoice-entry.js', 'public/js/lc')
    .js('resources/js/lc/lc-cf-value-margin-entry/lc-cf-value-margin-entry.js', 'public/js/lc')
    .js('resources/js/lc/lc-cost-name-category-entry/lc-cost-name-category-entry.js', 'public/js/lc')
    .js('resources/js/lc/lc-custom-duty-cost-name-entry/lc-custom-duty-cost-name-entry.js', 'public/js/lc')
    .js('resources/js/lc/lc-cnf-agent-entry/lc-cnf-agent-entry.js', 'public/js/lc')
    .js('resources/js/lc/lc-ci-landed-cost/lc-ci-landed-cost.js', 'public/js/lc')
    .js('resources/js/lc/lc-stock-entry/lc-stock-entry.js', 'public/js/lc')
    .js('resources/js/lc/lc-closing/lc-closing.js', 'public/js/lc')
    .js('resources/js/lc/lc-report/lc-report.js', 'public/js/lc')
    .js('resources/js/lc/lc-ledger-report/lc-ledger-report.js', 'public/js/lc');


