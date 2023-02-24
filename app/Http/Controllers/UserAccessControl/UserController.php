<?php

namespace App\Http\Controllers\UserAccessControl;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\UserAccessControl\AclRole;
use Hash;
use Auth;
use App\Repositories\CommonRepositories;

class UserController extends Controller
{

    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index()
    {
        $userList = User::where('email','!=','software@iventurebd.com')
                        ->with('role')->orderBy('id', 'desc')->get();
        return view('user_access_control.manage_user', compact('userList'));
    }

    
    public function create()
    {
        $roleList = $this->commonRepositories->selectRoleList();
        return view('user_access_control.add_user', compact('roleList'));
    }


    public function store(UserRequest $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $image=$request->file('user_photo');
        $input['created_by'] = Auth::user()->id;
        $input['modified_by'] = Auth::user()->id;

        if($image){
            $imgName=md5(str_random(30).time().'_'.$request->file('user_photo')).'.'.$request->file('user_photo')->getClientOriginalExtension();

            $request->file('user_photo')->move('uploads/user_photo/',$imgName);
            $input['user_photo']=$imgName;
        }

        try {
            User::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return redirect('user')->with('successMsg', 'User Inserted Successfully.');
        }elseif ($bug == 1062) {
            return redirect('user')->with('errorMsg', 'User is Found Duplicate');
        }
        else {
            return redirect()->back()->with('errorMsg', 'Something Error Found !, Please try again.');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $editModeData = User::FindOrFail($id);
        $roleList = $this->commonRepositories->selectRoleList();
        return view('user_access_control.add_user',compact('editModeData','roleList'));
    }

    public function update(UserRequest $request, $id)
    {
        $data = User::findOrFail($id);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $image=$request->file('user_photo');
        $input['created_by'] = Auth::user()->id;
        $input['modified_by'] = Auth::user()->id;

        if($image){
            $imgName=md5(str_random(30).time().'_'.$request->file('user_photo')).'.'.$request->file('user_photo')->getClientOriginalExtension();

            $request->file('user_photo')->move('uploads/user_photo/',$imgName);
            if(file_exists('uploads/user_photo/'.$data->photo) AND !empty($data->photo)){
                unlink('uploads/user_photo/'.$data->photo);
            }
            $input['user_photo']=$imgName;
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return redirect('user')->with('successMsg', 'User Updated Successfully.');
        }elseif ($bug == 1062) {
            return redirect('user')->with('errorMsg', 'User is Found Duplicate');
        }
        else {
            return redirect()->back()->with('errorMsg', 'Something Error Found !, Please try again.');
        }
    }

    public function destroy($id)
    {
        $data = User::FindOrFail($id);
        if(file_exists('uploads/user_photo/'.$data->user_photo) AND !empty($data->user_photo)){
            unlink('uploads/user_photo/'.$data->user_photo);
        }

        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            echo "success";
        }else{
            echo 'error';
        }
    }
}
