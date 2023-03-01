<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }

    public function showAdminLogin()
    {
        return view('auth.login', ['admin' => 'admin']);
    }

    public function showUserLogin()
    {
        return view('auth.login');
    }

    public function adminLogin(Request $req)
    {

        $req->validate([
            'email' => 'required|email|max:30',
            'password' => 'required|max:50',
        ]);

        if (Auth::guard('admin')->attempt(['email' => $req->email, 'password' => $req->password], $req->get('remember'))) {
            return redirect()->intended('/admin');
        }

        return back()->withInput([$req->only('email', 'remember')]);
    }

    public function userLogin(Request $req)
    {
        $req->validate([
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
        ]);

        if (Auth::attempt(['email' => $req->email, 'password' => $req->password], $req->get('remember'))) {
            return redirect()->intended('/home');
        }

        return back()->withInput([$req->only('email', 'remember')]);
    }

    public function logout()
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            return redirect('/login/admin');
        }

        Auth::logout();
        return redirect('/login');

    }
}