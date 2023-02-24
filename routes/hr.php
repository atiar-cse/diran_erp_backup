<?php

Route::group(['middleware' => ['preventbackbutton','auth']], function(){

    Route::resource('bonus', 'HR\Employee\HrBonusController', ['parameters' => ['bonus' => 'id']]);
    Route::resource('shift-manage', 'HR\Employee\HrShiftManageController', ['parameters' => ['shift-manage' => 'id']]);
    Route::resource('shift-schedule', 'HR\Employee\HrShiftScheduleController', ['parameters' => ['shift-schedule' => 'id']]);
    Route::resource('employee-level', 'HR\Employee\HrEmployeeLevelController', ['parameters' => ['employee-level' => 'id']]);
    Route::resource('employee-type', 'HR\Employee\HrEmployeeTypeController', ['parameters' => ['employee-type' => 'id']]);
    Route::resource('operation', 'HR\Employee\HrOperationController', ['parameters' => ['operation' => 'id']]);
    Route::resource('position', 'HR\Employee\HrPositionController', ['parameters' => ['position' => 'id']]);
    Route::resource('floor', 'HR\Employee\HrFloorController', ['parameters' => ['floor' => 'id']]);
    Route::resource('line', 'HR\Employee\HrLineController', ['parameters' => ['line' => 'id']]);
    Route::resource('section', 'HR\Employee\HrSectionController', ['parameters' => ['section' => 'id']]);
    Route::resource('team', 'HR\Employee\HrTeamController', ['parameters' => ['team' => 'id']]);
    Route::resource('unit','HR\Employee\HrUnitController',['parameters'=> ['unit'=>'id']]);
    Route::resource('division','HR\Employee\HrDivisionController',['parameters'=> ['division'=>'id']]);
    Route::resource('salary-grade','HR\Employee\HrSalaryGradeController',['parameters'=> ['salary-grade'=>'id']]);
    Route::resource('department','HR\Employee\DepartmentController',['parameters'=> ['department'=>'id']]);
    Route::resource('designation','HR\Employee\DesignationController',['parameters'=> ['designation'=>'id']]);
    Route::resource('branch','HR\Employee\BranchController',['parameters'=> ['branch'=>'id']]);
    Route::resource('termination','HR\Employee\TerminationController',['parameters'=> ['termination'=>'id']]);
    Route::resource('warning','HR\Employee\WarningController',['parameters'=> ['warning'=>'id']]);
    Route::resource('manage-employee','HR\Employee\ManageEmployeeController',['parameters'=> ['manage-employee'=>'id']]);
    Route::resource('employee-permanent','HR\Employee\EmployeePermanentController',['parameters'=> ['employee-permanent'=>'id']]);
    Route::resource('promotion','HR\Employee\PromotionController',['parameters'=> ['promotion'=>'id']]);
//    Route::resource('increment','HR\Employee\IncrementController',['parameters'=> ['increment'=>'id']]);
    Route::resource('increament','HR\Employee\IncreamentController',['parameters'=> ['increament'=>'id']]);

    //Attendance Section
    Route::resource('manage-work-shift','HR\Attendance\ManageWorkShiftController',['parameters'=> ['manage-work-shift'=>'id']]);
    Route::resource('manual-attendance-entry','HR\Attendance\ManualAttendanceEntryController',['parameters'=> ['manual-attendance-entry'=>'id']]);
   //Leave
    Route::resource('manage-holiday','HR\Leave\ManageHolidayController',['parameters'=> ['manage-holiday'=>'id']]);
    Route::resource('leave-type','HR\Leave\LeaveTypeController',['parameters'=> ['leave-type'=>'id']]);
    Route::resource('public-holiday','HR\Leave\PublicHolidayController',['parameters'=> ['public-holiday'=>'id']]);
    Route::resource('weekly-holiday','HR\Leave\WeeklyHolidayController',['parameters'=> ['weekly-holiday'=>'id']]);

    Route::resource('apply-for-leave','HR\Leave\ApplyForLeaveController',['parameters'=> ['apply-for-leave'=>'id']]);
    Route::any('check-leave-balance','HR\Leave\ApplyForLeaveController@checkLeaveBalance');
    Route::any('check-year-leave-balance','HR\Leave\ApplyForLeaveController@checkLeaveYearlyBalance');
    Route::post('apply-for-leave/leave-approved/{id}','HR\Leave\ApplyForLeaveController@approvedEmpLeave');

    Route::resource('earn-leave-configuration','HR\Leave\EarnLeaveConfigurationController',['parameters'=> ['earn-leave-configuration'=>'id']]);

    //Route::resource('attn-process','HR\Attendance\ManageWorkShiftController',['parameters'=> ['salary-process'=>'id']]);

    Route::post('employee-permanent/update-permanent/{id}','HR\Employee\EmployeePermanentController@updatePermanent');

    //Route::get('check-current-balance/{employee_id}/{leave_type_id}','HR\Leave\ApplyForLeaveController@checkEmpCurrentBalance');

    //Route::get('employee-wise-attendance-load','HR\Attendance\ManualAttendanceEntryController@employeeAttendanceLoad');
    Route::get('employee-wise-attendance-load/{department_id}/{work_shift_id}/{date}',['as' => 'employee-wise-attendance-load.department_id.work_shift_id.date', 'uses'=>'HR\Attendance\ManualAttendanceEntryController@employeeAttendanceLoad']);
   // Route::get('check-current-balance','HR\Leave\ApplyForLeaveController@checkEmpCurrentBalance');



});
