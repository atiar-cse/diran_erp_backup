<?php

Route::group(['middleware' => ['preventbackbutton','auth']], function(){

    Route::resource('hourly-pay-grade','Payroll\HourlyPayGradeController',['parameters'=> ['hourly-pay-grade'=>'id']]);
    Route::resource('allowance','Payroll\AllowanceController',['parameters'=> ['allowance'=>'id']]);
    Route::resource('deduction','Payroll\DeductionController',['parameters'=> ['deduction'=>'id']]);
    Route::resource('bonus-setting','Payroll\BonusSettingController',['parameters'=> ['bonus-setting'=>'id']]);
    Route::resource('monthly-pay-grade','Payroll\MonthlyPayGradeController',['parameters'=> ['monthly-pay-grade'=>'id']]);
    Route::resource('generate-bonus','Payroll\GenerateBonusController',['parameters'=> ['generate-bonus'=>'id']]);
    Route::resource('bonus-process','Payroll\BonusProcessController',['parameters'=> ['bonus-process'=>'id']]);

    Route::resource('payroll-bonus-summery','Payroll\PayrollBonusSummeryController',['parameters'=> ['payroll-bonus-summery'=>'id']]);
    Route::get('calculate-total-bonus', 'Payroll\PayrollBonusSummeryController@calculateTotalBonus');

    Route::resource('manage-work-hour-approve','Payroll\ManageWorkHourApproveController',['parameters'=> ['manage-work-hour-approve'=>'id']]);
    Route::resource('salary-process','Payroll\SalaryProcessController',['parameters'=> ['salary-process'=>'id']]);

    Route::resource('payroll-salary-summery','Payroll\PayrollSalarySummeryController',['parameters'=> ['payroll-salary-summery'=>'id']]);
    Route::get('calculate-total-salary', 'Payroll\PayrollSalarySummeryController@calculateTotalSalary');

    Route::resource('generate-salary-sheet','Payroll\GenerateSalarySheetController',['parameters'=> ['generate-salary-sheet'=>'id']]);
    Route::resource('tax-rule-setup','Payroll\TaxRuleSetupController',['parameters'=> ['tax-rule-setup'=>'id']]);
    Route::resource('rules-of-salary-deduction','Payroll\RulesOfSalaryDeductionController',['parameters'=> ['rules-of-salary-deduction'=>'id']]);
    Route::post('update-rules-of-salary','Payroll\RulesOfSalaryDeductionController@updateSalaryDeductionRule');



    //Route::get('salary-sheet/{employee_id}',['as' => 'salary-sheet.employee_id', 'uses'=>'Payroll\GenerateSalarySheetController@generateSalarySheet']);

    Route::any('salary-sheet','Payroll\GenerateSalarySheetController@generateSalarySheet');


});