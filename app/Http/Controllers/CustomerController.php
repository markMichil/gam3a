<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Customer_detail;
use App\Customer_fatora;
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

class CustomerController extends Controller
{

  
   public function index()
   {
     $data = Customer::get();
     return view('backend.customer.list')->withdata($data);
   }

   public function create()
   {
       return view('backend.customer.create');
   }

   public function store(Request $request)
   {
       $row = new Customer;
       $row->name = $request->input('name');
       $row->phone = $request->input('phone');
       $row->address = $request->input('address');
       $row->save();
       Session::flash('success','تم إضافة العميل بنجاح');
       return Redirect::to('customer');
   }


   public function edit($id)
   {
       $row = Customer::find($id);
       return view('backend.customer.edit')->withrow($row);
   }

   public function update($id, Request $request)
   {

       $row = Customer::find($id);
       $row->total = $request->input('total');
       $row->paid = $request->input('paid');
       $row->remain = $request->input('remain');
       $row->name = $request->input('name');
       $row->phone = $request->input('phone');
       $row->address = $request->input('address');
       $row->save();
       Session::flash('success','تم تعديل العميل بنجاح');
       return Redirect::back();
   }

   public function add_row($id)
   {
       $row = new Customer_detail;
       $row->customer_id = $id;
       $row->save();
       return Redirect::back();
   }

   public function update_row($id, Request $request)
   {
dd($request->input());
       $row = Customer_detail::find($id);

       $big = Customer::where('id',$row->customer_id)->first();
       $big->paid  = $big->paid + $request->input('paid');
       $big->remain  = $big->remain - $request->input('paid');
       $big->save();

       $row->inv_date = $request->input('date');
       $row->paid = $request->input('paid');
       $row->note = $request->input('note');

       $row->save();

             /* Balance */
             $rows = new Balance;
             $rows->type = 0;
             $rows->date = date('Y-m-d');
             $rows->amount = $row->paid;
             $rows->reason = 'مبيعات قسط';
             $rows->save();

       return Redirect::back();
   }

   public function delete_row($id)
   {
       $row = Customer_detail::find($id);
       $big = Customer::where('id',$row->customer_id)->first();

       if($row->paid > 0) {
          $big->paid = $big->paid - $row->paid;
          $big->remain = $big->remain + $row->paid;
          $big->save();
       }

             /* Balance */
             $rows = new Balance;
             $rows->type = 1;
             $rows->date = date('Y-m-d');
             $rows->amount = $row->paid;
             $rows->reason = 'قسط مرتجع';
             $rows->save();

       $row->delete();
       return Redirect::back();
   }


  public function destory($id)
  {
       $big = Customer::find($id);

            /* Balance */
             $rows = new Balance;
             $rows->type = 1;
             $rows->date = date('Y-m-d');
             $rows->amount = $big->paid;
             $rows->reason = 'قسط مرتجع';
             $rows->save();

       $row = Customer_detail::where('customer_id',$big->id)->delete();
       $big->delete();
       Session::flash('success','تم حذف العميل بنجاح');
       return Redirect::back();
  }




    #Invoice
    public function invoice()
    {

//        $data = Cash_other::where('status',0)->orderby('id','ASC')->get();
//        ->withdata($data)
        return view('backend.customer.invoice');
    }

    public function invoice_id($id)
    {
         $row = Customer_detail::find($id);
        $name = Customer::find($row->customer_id);
        $namee = $name->name;
         $date = $row->inv_date;
         $paid = $row->paid;
         $note = $row->note;




        return view('backend.customer.invoice')
            ->with('name',$namee)
            ->with('date',$date)
            ->with('paid',$paid)
            ->with('note',$note);
    }

	
	
    public function add_fatora($id)
    {
        $row = new Customer_fatora;
        $row->customer_id = $id;
        $row->save();
        return Redirect::back();
    }

   public function update_fatora($id, Request $request)
   {

       $row = Customer_fatora::find($id);

       $big = Customer::where('id',$row->customer_id)->first();
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
       $row = Customer_fatora::find($id);
       $big = Customer::where('id',$row->customer_id)->first();

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







}
