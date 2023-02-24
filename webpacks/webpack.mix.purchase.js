const mix = require('laravel-mix');

mix .js('resources/js/purchase/setup/purchase_supplier/purchase-supplier.js', 'public/js/purchase')
    .js('resources/js/purchase/setup/purchase_cost_type/purchase-cost-type.js', 'public/js/purchase')
    .js('resources/js/purchase/setup/purchase_return_type/purchase-return-type.js', 'public/js/purchase')
    .js('resources/js/purchase/setup/purchase_ware_house/purchase-ware-house.js', 'public/js/purchase')

    .js('resources/js/purchase/purchase-sections/purchase_requisition/purchase-requisition.js', 'public/js/purchase')
	.js('resources/js/purchase/purchase-sections/purchase_receive/purchase-receive.js', 'public/js/purchase')

    .js('resources/js/purchase/purchase-sections/purchase_related_cost_entry/purchase-related-cost-entry.js', 'public/js/purchase')
    .js('resources/js/purchase/purchase-sections/purchase_return/purchase-return.js', 'public/js/purchase')

    .js('resources/js/purchase/report/purchase_report/purchase-report.js', 'public/js/purchase')
    .js('resources/js/purchase/report/purchase_return_report/purchase-return-report.js', 'public/js/purchase')
    .js('resources/js/purchase/report/purchase_summary/purchase-summary-report.js', 'public/js/purchase')
    .js('resources/js/purchase/report/supplier_ledeger_report/supplier-ledeger-report.js', 'public/js/purchase')






