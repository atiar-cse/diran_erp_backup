const mix = require('laravel-mix');

mix .js('resources/js/accounts/setup/bank-name/bank-name.js', 'public/js/account')
    .js('resources/js/accounts/setup/bank-branch/bank-branch.js', 'public/js/account')
    .js('resources/js/accounts/setup/bank-accounts/bank-account.js', 'public/js/account')
    .js('resources/js/accounts/setup/tax-rules/tax-rules.js', 'public/js/account')
    .js('resources/js/accounts/setup/cost-centre/cost-centre.js', 'public/js/account')
    .js('resources/js/accounts/setup/project-info/project-info.js', 'public/js/account')
    .js('resources/js/accounts/setup/check-book-leaf/check-book-leaf.js', 'public/js/account')

    /*Lc voucher Section*/

    .js('resources/js/accounts/acc-lc-section/lc-opening-section/acc-lc-opening-section.js', 'public/js/account')
    .js('resources/js/accounts/acc-lc-section/lc-insurance/acc-lc-insurance.js', 'public/js/account')
    .js('resources/js/accounts/acc-lc-section/lc-cf-value-margin-entry/acc-lc-cf-value-margin-entry.js', 'public/js/account')
    .js('resources/js/accounts/acc-lc-section/latr-entry/acc-latr-entry.js', 'public/js/account')
    .js('resources/js/accounts/acc-lc-section/custom-duty-charge-entry/acc-custom-duty-charge-entry.js', 'public/js/account')
    .js('resources/js/accounts/acc-lc-section/others-charge-entry/acc-others-charge-entry.js', 'public/js/account')
    .js('resources/js/accounts/acc-lc-section/lc-stock-entry/acc-lc-stock-entry.js', 'public/js/account')


    /*Lc voucher end*/

    .js('resources/js/accounts/accounts-section/bank-payment-voucher/bank-payment-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/bank-receipt-voucher/bank-receipt-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/bank-transfer-deposit/bank-transfer-deposit.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/bill-paymnt-voucher/bill-paymnt-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/cash-paymnt-voucher/cash-paymnt-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/cash-receipt-voucher/cash-receipt-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/contra-entry-voucher/contra-entry-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/fixed-asset-depreciation/fixed-asset-depreciation.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/journal-voucher/journal-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/opening-balance/opening-balance.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/purchase-invoice-voucher/purchase-invoice-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/purchase-return-voucher/purchase-return-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/sales-invoice-voucher/sales-invoice-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/sales-return-voucher/sales-return-voucher.js', 'public/js/account')
    .js('resources/js/accounts/accounts-section/year-end/year-end.js', 'public/js/account')
    .js('resources/js/accounts/chart-of-accounts/account-main-code/account-main-code.js', 'public/js/account')
    .js('resources/js/accounts/chart-of-accounts/account-sub-code/account-sub-code.js', 'public/js/account')
    .js('resources/js/accounts/chart-of-accounts/account-sub-code2/account-sub-code2.js', 'public/js/account')
    .js('resources/js/accounts/chart-of-accounts/chart-of-accounts/chart-of-accounts.js', 'public/js/account')

    .js('resources/js/accounts/reports/balance-sheet-reports/balance-sheet-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/daily-statements/daily-statement-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/bank-statements/bank-statement-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/expense-statements/expense-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/income-statements/income-statement-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/journal-reports/journal-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/ledger-reports/ledger-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/ledger-sub-code2-reports/ledger-sub-code2-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/account-wise-ledger-report/account-wise-ledger-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/loss-profit-statements/loss-profit-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/trial-balance/trial-balance-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/trial-balance-sub2/trial-balance-reports-sub2.js', 'public/js/account')
    .js('resources/js/accounts/reports/bank-reconcilation-reports/bank-reconcilation-reports.js', 'public/js/account')
    .js('resources/js/accounts/reports/cash-book-reports/cash-book-reports.js', 'public/js/account')



