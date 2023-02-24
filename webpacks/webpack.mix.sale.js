const mix = require('laravel-mix');

mix .js('resources/js/sales/setup/sales_customer/sales-customer.js', 'public/js/sales')
    .js('resources/js/sales/sales_sections/sales_invoice/sales-invoice.js', 'public/js/sales')
    .js('resources/js/sales/sales_sections/sales_challan_delivery/sales-challan-delivery.js', 'public/js/sales')
    .js('resources/js/sales/sales_sections/sales_delivery_return/sales-delivery-return.js', 'public/js/sales')

    .js('resources/js/sales/report/sales_report/sales-report.js', 'public/js/sales')
    .js('resources/js/sales/report/sales_summary_report/sales-summary-report.js', 'public/js/sales')
    .js('resources/js/sales/report/sales_return_report/sales-return-report.js', 'public/js/sales')
    .js('resources/js/sales/report/customer_ledeger_report/customer-ledeger-report.js', 'public/js/sales')
