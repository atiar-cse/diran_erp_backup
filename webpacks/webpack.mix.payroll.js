const mix = require('laravel-mix');

mix .js('resources/js/payroll/setup/hourly-pay-grade/hourly-pay-grade.js', 'public/js/payroll')
    .js('resources/js/payroll/setup/allowance/allowance.js', 'public/js/payroll')
    .js('resources/js/payroll/setup/deduction/deduction.js', 'public/js/payroll')
    .js('resources/js/payroll/setup/bonus-setting/bonus-setting.js', 'public/js/payroll')
    .js('resources/js/payroll/setup/monthly-pay-grade/monthly-pay-grade.js', 'public/js/payroll')
    .js('resources/js/payroll/setup/tax-rule-setup/tax-rule-setup.js', 'public/js/payroll')
    .js('resources/js/payroll/setup/rules-of-salary-deduction/rules-of-salary-deduction.js', 'public/js/payroll')
    .js('resources/js/payroll/payroll-section/generate-bonus/generate-bonus.js', 'public/js/payroll')
    .js('resources/js/payroll/payroll-section/manage-work-hour-approve/manage-work-hour-approve.js', 'public/js/payroll')
    .js('resources/js/payroll/payroll-section/generate-salary-sheet/generate-salary-sheet.js', 'public/js/payroll')
    .js('resources/js/payroll/payroll-section/salary-process/salary-process.js', 'public/js/payroll')
    .js('resources/js/payroll/payroll-section/bonus-process/bonus-process.js', 'public/js/payroll')
    .js('resources/js/payroll/payroll-section/payroll-salary-process-monthly/payroll-salary-process-monthly.js', 'public/js/payroll')
    .js('resources/js/payroll/payroll-section/payroll-bonus-summery/payroll-bonus-summery.js', 'public/js/payroll')

