<?php

namespace App\Http\Controllers;

use App\Liabilitie;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LiabilitiesController extends Controller
{
    public function index()
   {
       $data = Liabilitie::orderby('id','ASC')->get();
       return view('backend.liabilities.list')->withdata($data);
   }

   public function store(Request $request)
   {
       $row = new Liabilitie;
       $row->date = $request->input('date');
       $row->name = $request->input('name');
       $row->reason = $request->input('reason');
       try {
        $row->save();
         Session::flash('success','تم إضافة المطلوبات بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم إضافة المطلوبات');
      }
       return Redirect::back();
   }

   public function edit($id)
   {
      $row = Liabilitie::find($id);
      return view('backend.liabilities.edit')->withrow($row);
   }

   public function update($id,Request $request)
   {
       $row = Liabilitie::find($id);
       $row->date = $request->input('date');
       $row->name = $request->input('name');
       $row->reason = $request->input('reason');
       try {
        $row->save();
         Session::flash('success','تم تعديل المطلوبات بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم تعديل المطلوبات');
      }
       return Redirect::back();
   }

   public function destroy($id)
   {
      $row = Liabilitie::find($id);
      $row->delete();
      Session::flash('success','تم حذف المطلوبات بنجاح');
      return Redirect::back();
   }
   
}
