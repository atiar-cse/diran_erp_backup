const mix = require('laravel-mix');

mix .js('resources/js/inventory/inventory_product_damage/inventory-product-damage.js', 'public/js/inventory')
	.js('resources/js/inventory/inventory_stock_adjust/inventory-stock-adjust.js', 'public/js/inventory')
    .js('resources/js/inventory/inventory_current_stock/inventory-current-stock.js', 'public/js/inventory')
	.js('resources/js/inventory/inventory_stock_transfer/inventory-stock-transfer.js', 'public/js/inventory')
	
	
    .js('resources/js/inventory/inventory_stock_report/inventory-stock-report.js', 'public/js/inventory')
    .js('resources/js/inventory/report/pricing/sales-price-report.js', 'public/js/inventory')

//.js('resources/js/inventory/report/stock_transfer_report/inv-stock-transfer-report.js', 'public/js/inventory')





