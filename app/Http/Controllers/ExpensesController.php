<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Balance;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class ExpensesController extends Controller
{

   public function index()
   {
       $data = Expense::orderby('id','ASC')->get();
       return view('backend.expenses.list')->withdata($data);
   }

   public function store(Request $request)
   {
       $row = new Expense;
       $row->date = $request->input('date');
       $row->amount = $request->input('amount');
       $row->reason = $request->input('reason');
       try {
        $row->save();
		
		     /* Balance */
             $rows = new Balance;
             $rows->type = 1;
             $rows->date = date('Y-m-d');
             $rows->amount = $row->amount;
			 $rows->reason = $row->reason;
             $rows->save();	
			 
         Session::flash('success','تم إضافة المصروفات بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم إضافة المصروفات');
      }
       return Redirect::back();
   }

   public function edit($id)
   {
      $row = Expense::find($id);
      return view('backend.expenses.edit')->withrow($row);
   }

   public function update($id,Request $request)
   {
       $row = Expense::find($id);
       $row->date = $request->input('date');
       $row->amount = $request->input('amount');
       $row->reason = $request->input('reason');
       try {
        $row->save();
         Session::flash('success','تم تعديل المصروفات بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم تعديل المصروفات');
      }
       return Redirect::back();
   }

   public function destroy($id)
   {
      $row = Expense::find($id);
      $row->delete();
      Session::flash('success','تم حذف المصروفات بنجاح');
      return Redirect::back();
   }

}
