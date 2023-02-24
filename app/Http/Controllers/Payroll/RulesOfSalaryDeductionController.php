<?php

namespace App\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Payroll\RulesOfSalaryDeduction;

class RulesOfSalaryDeductionController extends Controller
{

    public function index()
    {
        $data = RulesOfSalaryDeduction::first();
        return view('payroll.payroll_setup.rules_of_salary_deduction',['data' => $data]);
    }


    public function updateSalaryDeductionRule(Request $request)
    {
        $input   = $request->all();
        $data = RulesOfSalaryDeduction::findOrFail($request->id);

        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            return "success";

        }else {
            return "error";
        }
    }


    public function store(Request $request)
    {
        $input   = $request->all();

        try{
            RulesOfSalaryDeduction::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            return "success";

        }else {
            return "error";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
