<?php

namespace App\Http\Controllers\Payroll;

use App\Model\HR\HrManageEmployee;
use App\Model\Payroll\PayrollBonusProcess;
use App\Model\Payroll\PayrollBonusSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class BonusProcessController extends Controller
{

    public function index()
    {
        return view('payroll.payroll_section.bonus_process');
    }


    public function store(Request $request)
    {
        $bonusRule           = PayrollBonusSetting::where('status', '1')->orderBy('id', 'DESC')->first();
        $bonus_type          = $bonusRule->bonus_type;
        $percentage_of_bonus = $bonusRule->percentage_of_bonus;

        $employee = HrManageEmployee::select('hr_manage_employees.id', 'hr_manage_employees.date_of_joining', 'hr_manage_employees.permanent_status', 'payroll_monthly_pay_grades.pay_grade_name', 'payroll_monthly_pay_grades.gross_salary', 'payroll_monthly_pay_grades.basic_salary')
            ->leftJoin('payroll_monthly_pay_grades', 'payroll_monthly_pay_grades.id', '=', 'hr_manage_employees.monthly_pay_grade_id')
            ->where('hr_manage_employees.permanent_status', '=', 1)
            ->get();

        try {
            DB::beginTransaction();

            $data = [];

            if ($employee) {
                foreach ($employee as $key => $value) {

                    if ($bonus_type == 'Basic') {
                        if ($value['pay_grade_name'] != '') {
                            $bonus_amount = $value['basic_salary'] * $percentage_of_bonus / 100;
                        }
                    } else if ($bonus_type == 'Gross') {
                        if ($value['pay_grade_name'] != '') {
                            $bonus_amount = $value['basic_salary'] * $percentage_of_bonus / 100;
                        }
                    }

                    $data[] = [
                        'employee_id' => $value['id'],
                        'gross_salary' => $value['gross_salary'],
                        'basic_salary' => $value['basic_salary'],
                        'bonus_setting_id' => $bonusRule->id,
                        'festival_name' => $bonusRule->festival_name,
                        'bonus_type' => $bonusRule->bonus_type,
                        'percentage_of_bonus' => $bonusRule->percentage_of_bonus,
                        'bonus_amount' => $bonus_amount,
                        'bonus_month' => $request->bonus_month,

                    ];
                    $input['employee_id'] = $value['id'];
                    $input['gross_salary'] = $value['gross_salary'];
                    $input['basic_salary'] = $value['basic_salary'];
                    $input['bonus_setting_id'] = $bonusRule->id;
                    $input['festival_name'] = $bonusRule->festival_name;
                    $input['bonus_type'] = $bonusRule->bonus_type;
                    $input['percentage_of_bonus'] = $bonusRule->percentage_of_bonus;
                    $input['bonus_amount'] = $bonus_amount;
                    $input['bonus_month'] = $request->bonus_month;

                    $resultData = PayrollBonusProcess::create($input);
                }
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Bonus Generate successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Bonus Generate is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
