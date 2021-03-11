<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;

use DB;
use Auth;
use Hash;
use Input;
use Session;
use Redirect;
use Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
   
    #Home
    public function index()
    {
//                dd(bcrypt('admin'));
        $data = Product::get();
        return view('backend.layouts.index')->withdata($data);
    }


    #Login
    public function login()
    {
        return view('backend.layouts.login');
    }

    public function doLogin(Request $request)
    {
       $username = $request->input('username');
       $password = $request->input('password');
 
      $data = [
            'username'   => $username,
            'password' => $password
            ];

      if(Auth::attempt($data,true))
      {
            return Redirect::to('/');
      } else {
         Session::flash('login_error','');
         return Redirect::back();
      }        
    }

    #Logout
    public function logout()
    {
      Auth::logout();
      return Redirect::to('/');
    }


    #Category
    public function display_category($name,$id)
    {
       $data = Product::where('cat_id',$id)->get();
       $getname = str_replace('-', ' ', $name);
       return view('backend.layouts.category')->withdata($data)->withname($getname);
    }


    #Details
    public function details($name,$id)
    {
        $row = Product::where('id',$id)->first();
        return view('backend.layouts.details')->withrow($row);
    }

    #Search
    public function search()
    {
        $s = Input::get('s');
        $data = Product::where('slug','LIKE','%'.$s.'%')->orwhere('pro_code',$s)->orderby('id','DESC')->get();
        return view('backend.layouts.search')->withdata($data);
    }

    #Profile
    public function profile()
    {
       return view('backend.profile.list');
    }

    public function doProfile(Request $request)
    {
        $row = User::find(1);
        $row->password = Hash::make($request->input('password'));
        $row->save();
        Session::flash('success','تم تغير كلمة المرور بنجاح');
        return Redirect::back();
    }




}
