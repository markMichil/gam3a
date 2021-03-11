<?php

namespace App\Http\Controllers;

use App\Mward;
use App\Mward_detail;
use App\Mward_fatora;
use App\Balance;

use DB;
use Auth;
use Input;
use Session;
use Redirect;
use Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MowardController extends Controller
{

  
   public function index()
   {
     $data = Mward::get();
	 
     return view('backend.mward.list')->withdata($data);
   }

   public function create()
   {
       return view('backend.mward.create');
   }

   public function store(Request $request)
   {
       $row = new Mward;
       $row->name = $request->input('name');
       $row->phone = $request->input('phone');
       $row->address = $request->input('address');
       $row->save();
       Session::flash('success','تم إضافة مورد بنجاح');
       return Redirect::to('moward');
   }


   public function edit($id)
   {
       $row = Mward::find($id);
       return view('backend.mward.edit')->withrow($row);
   }

   public function update($id, Request $request)
   {
       $row = Mward::find($id);
       $row->total = $request->input('total');
       $row->paid = $request->input('paid');
       $row->remain = $request->input('remain');
       $row->save();
       Session::flash('success','تم تعديل مورد بنجاح');
       return Redirect::back();
   }

   public function add_row($id)
   {
       $row = new Mward_detail;
       $row->mward_id = $id;
       $row->save();
       return Redirect::back();
   }



   public function update_row($id, Request $request)
   {

       $row = Mward_detail::find($id);

       $big = Mward::where('id',$row->mward_id)->first();
       $big->paid  = $big->paid + $request->input('paid');
       $big->remain  = $big->remain - $request->input('paid');
       $big->save();

       $row->inv_date = $request->input('date');
       $row->paid = $request->input('paid');
       $row->note = $request->input('note');
       $row->save();

//             /* Balance */
//             $rows = new Balance;
//             $rows->type = 0;
//             $rows->date = date('Y-m-d');
//             $rows->amount = $row->paid;
//             $rows->reason = 'مبيعات قسط';
//             $rows->save();

       return Redirect::back();
   }


   public function delete_row($id)
   {
       $row = Mward_detail::find($id);
       $big = Mward::where('id',$row->mward_id)->first();

       if($row->paid > 0) {
          $big->paid = $big->paid - $row->paid;
          $big->remain = $big->remain + $row->paid;
          $big->save();
       }

//             /* Balance */
//             $rows = new Balance;
//             $rows->type = 1;
//             $rows->date = date('Y-m-d');
//             $rows->amount = $row->paid;
//             $rows->reason = 'قسط مرتجع';
//             $rows->save();

       $row->delete();
       return Redirect::back();
   }



    public function add_fatora($id)
    {
        $row = new Mward_fatora;
        $row->mward_id = $id;
        $row->save();
        return Redirect::back();
    }

   public function update_fatora($id, Request $request)
   {

       $row = Mward_fatora::find($id);

       $big = Mward::where('id',$row->mward_id)->first();
       $big->total  = $big->total + $request->input('paid');
       $big->remain  = $big->remain +  $request->input('paid');
       $big->save();

       $row->inv_date = $request->input('date');
       $row->paid = $request->input('paid');
       $row->note = $request->input('note');
       $row->save();

//             /* Balance */
//             $rows = new Balance;
//             $rows->type = 0;
//             $rows->date = date('Y-m-d');
//             $rows->amount = $row->paid;
//             $rows->reason = 'مبيعات قسط';
//             $rows->save();

       return Redirect::back();
   }

   public function delete_fatora($id)
   {
       $row = Mward_fatora::find($id);
       $big = Mward::where('id',$row->mward_id)->first();

       if($row->paid > 0) {
          $big->total = $big->total - $row->paid;
          $big->remain = $big->remain - $row->paid;
          $big->save();
       }

//             /* Balance */
//             $rows = new Balance;
//             $rows->type = 1;
//             $rows->date = date('Y-m-d');
//             $rows->amount = $row->paid;
//             $rows->reason = 'قسط مرتجع';
//             $rows->save();

       $row->delete();
       return Redirect::back();
   }


  public function destory($id)
  {
       $big = Mward::find($id);

//            /* Balance */
//             $rows = new Balance;
//             $rows->type = 1;
//             $rows->date = date('Y-m-d');
//             $rows->amount = $big->paid;
//             $rows->reason = 'قسط مرتجع';
//             $rows->save();

       $row = Mward_detail::where('mward_id',$big->id)->delete();
       $big->delete();
       Session::flash('success','تم حذف مورد بنجاح');
       return Redirect::back();
  }



}
