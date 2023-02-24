<?php

namespace App\Http\Controllers;

use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class CompanyInfoController extends Controller
{
    public function index()
    {
        $editModeData = CompanyInfo::get()->first();
        return view('company_info.add_info',compact('editModeData'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //dd($request);
    }

    public function show($id)
    {
        $comapny=CompanyInfo::where('id',$id)->first();
        $data =[
            'name'     => $comapny->name,
            'phone'    => $comapny->phone,
            'mobile'   => $comapny->mobile,
            'email'    => $comapny->email,
            'fax'      => $comapny->fax,
            'web'      => $comapny->web,
            'address'  => $comapny->address,
            'address2' => $comapny->address2,
            'logo'     => url('uploads/company_logo').'/'. $comapny->logo  ,
        ];


        return $data;
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'   =>'required',
            'address'=>'required',
        ]);

        $data = CompanyInfo::findOrFail($id);

        $data_insert = array(
            'name'     => $request->name,
            'phone'    => $request->phone,
            'mobile'   => $request->mobile,
            'email'    => $request->email,
            'fax'      => $request->fax,
            'web'      => $request->web,
            'address'  => $request->address,
            'address2' => $request->address2,
        );


        $image=$request->file('logo');

        if($image){
            $imgName=md5(str_random(30).time().'_'.$request->file('logo')).'.'.$request->file('logo')->getClientOriginalExtension();

            $request->file('logo')->move('uploads/company_logo/',$imgName);
            if(file_exists('uploads/company_logo/'.$data->logo) AND !empty($data->logo)){
                unlink('uploads/company_logo/'.$data->logo);
            }
            $data_insert['logo']=$imgName;
        }

        try {

            CompanyInfo::where('id',$id)->update($data_insert);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return redirect('Company-Info')->with('successMsg', 'Company Info Updated Successfully.');
        }elseif ($bug == 1062) {
            return redirect('Company-Info')->with('errorMsg', 'Company is Found Duplicate');
        }
        else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }


    }

    public function destroy($id)
    {
        //
    }
}
