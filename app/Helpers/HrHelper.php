<?php // Code within app\Helpers\HrHelper.php

use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrUnit;
use App\Model\HR\HrDivision;
use App\Model\HR\HrDepartment;
use App\Model\HR\HrEmployeeType;
use App\Model\HR\HrTeam;
use App\Model\HR\HrSalaryGrade;
use App\Model\HR\HrApplyForLeave;

function get_hr_leave_pending()
{
    return HrApplyForLeave::with('employee')->where('status',1)->orderBy('id','DESC')->get();
}

function getHeadPersonList(){
    $details = HrManageEmployee::where('status',1)->select('id','first_name','last_name')->get();
    $headPersonListData = [] ;
    foreach ($details as $row) {
        $headPersonListData[] = [
            'id'   => $row->id,
            'name' => $row->first_name.' '.$row->last_name,
        ];
    }

    return $headPersonListData;
}

function getUnitList(){
    $UnitList = HrUnit::where('status',1)->select('id','unit_name')->get();
    $UnitListData = [];
    foreach ($UnitList as $row) {
        $UnitListData[] = [
            'id'   => $row->id,
            'name' => $row->unit_name,
        ];
    }

    return $UnitListData;
}

function getDivList(){
    $DivList = HrDivision::with('getUnit')->where('status',1)->select('id','unit_id','title')->get();
    $DivListData = [];
    foreach ($DivList as $row) {
        $DivListData[] = [
            'id'   => $row->id,
            'name' => $row->title.' ('.$row->getUnit->unit_name.'.)',
        ];
    }

    return $DivListData;
}

function getDeptList(){
    $DeptList = HrDepartment::with('getDiv')->where('status',1)
        ->select('id','div_id','department_name')->get();
    $deptListData = [];

    foreach ($DeptList as $row) {
        $divName = $row->getDiv ? $row->getDiv->title:'';
        $deptListData[] = [
            'id'   => $row->id,
            'name' => $row->department_name.' ('.$divName.')',
        ];
    }

    return $deptListData;

}

function getEmpTypeList(){
    $EmpTypeList =HrEmployeeType::where('status',1)->select('id','title')->get();
    $empTypeListData = [];
    foreach ($EmpTypeList as $row) {
        $empTypeListData[] = [
            'id'   => $row->id,
            'name' => $row->title,
        ];
    }

    return $empTypeListData;

}


function getTeamList(){
    $TeamList =HrTeam::where('status',1)->select('id','title')->get();
    $teamListData = [];
    foreach ($TeamList as $row) {
        $teamListData[] = [
            'id'   => $row->id,
            'name' => $row->title,
        ];
    }

    return $teamListData;

}

function getReligionList(){
    $ReligionnList =DB::table('hr_religion')->where('status',1)->select('id','title')->get();
    $religionListData = [];
    foreach ($ReligionnList as $row) {
        $religionListData[] = [
            'id'   => $row->id,
            'name' => $row->title,
        ];
    }

    return $religionListData;

}
function getGradeList(){
    $GradeList =HrSalaryGrade::where('status',1)->select('id','title')->get();
    $gradListData = [];
    foreach ($GradeList as $row) {
        $gradListData[] = [
            'id'   => $row->id,
            'name' => $row->title,
        ];
    }

    return $gradListData;

}
