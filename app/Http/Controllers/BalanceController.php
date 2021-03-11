<?php

namespace App\Http\Controllers;

use App\Balance;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class BalanceController extends Controller
{
    
   public function index()
   {
      $data = Balance::orderby('id','ASC')->get();
      return view('backend.balance.list')->withdata($data);
   }

   public function store(Request $request)
   {
       $row = new Balance;
       $row->type = $request->input('type');
       $row->date = $request->input('date');
       $row->amount = $request->input('amount');
       $row->reason = $request->input('reason');
      try {
        $row->save();
         Session::flash('success','تم إضافة الرصيد بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم إضافة الرصيد');
       }
       return Redirect::back();
   }

   public function edit($id)
   {
      $row = Balance::find($id);
      return view('backend.balance.edit')->withrow($row);
   }

   public function update($id,Request $request)
   {
       $row = Balance::find($id);
       $row->type = $request->input('type');
       $row->date = $request->input('date');
       $row->amount = $request->input('amount');
       $row->reason = $request->input('reason');
      try {
        $row->save();
         Session::flash('success','تم تعديل الرصيد بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم تعديل الرصيد');
      }
       return Redirect::back();
   }

   public function destroy($id)
   {
      $row = Balance::find($id);
      $row->delete();
      Session::flash('success','تم حذف الرصيد بنجاح');
      return Redirect::back();
   }

}
