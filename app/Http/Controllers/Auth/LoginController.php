<?php
namespace App\Http\Controllers\Auth;

    use App\Model\CompanyInfo;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use App\Http\Requests\LoginRequest;
    use Auth;
    use Session;
    use Validator;
    use App\User;


class LoginController extends Controller
{
    public function index()
    {
        $data['company_info'] = CompanyInfo::get()->first();

        if(Auth::check()){
            return redirect()->intended(url('dashboard'));
        }
        return view('login',$data);
    }

    public function Auth(LoginRequest $request){

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){

            $userStatus = Auth::User()->status;
            if($userStatus=='1') {

                $user_data = [
                    "id" =>Auth::user()->id,
                    "email" =>Auth::user()->email,
                    "user_name" =>Auth::user()->user_name,
                    "role_id" =>Auth::user()->role_id,
                    "user_photo" =>Auth::user()->user_photo
                ];

                session()->put('logged_session_data', $user_data);
                return redirect()->intended(url('/dashboard'));
            }else{
                Auth::logout();
                Session::flush();
                return redirect(url('admin-login'))->withInput()->with('errorMsg','You are temporary blocked. please contact to admin');
            }
        }
        else {

            return redirect(url('admin-login'))->withInput()->with('errorMsg','Incorrect username or password. Please try again.');
        }
    }


    public function logout(){
        Auth::logout();
        Session::flush();
        return redirect('admin-login')->with('successMsg','Successfully Logged Out');
    }
}

