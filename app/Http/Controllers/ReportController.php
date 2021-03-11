<?php

namespace App\Http\Controllers;

use App\Cash;
use App\Order;
use App\Invoice;
use App\Product;
use App\Balance;
use App\Liabilitie;
use App\Expense;
use App\Category;
use App\Installment;
use App\Cash_other;
use App\Invoice_attribute;
use App\Installment_other;
use App\Customer;

use DB;
use Auth;
use Input;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{

#Products
  public function report_products()
  {
      $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');
     
  if(!empty($from_month)) {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all') {
          $pro = Product::where('year',$from_year)->get();
      } else if ($from_month == 'all' && $to_month != 'all') {
          $pro = Product::where('month','<=',$to_month)->where('year',$from_year)->get();
      } else {
          $pro = \DB::table('products')
                            ->where('month','>=',$from_month)
                            ->where('month','<=',$to_month)
                            ->where('year','>=',$from_year)
                            ->where('year','<=',$to_year)
                            ->get();
         }
      } else {
           $pro = Product::where('year','2015')->get();
      }

     return view('backend.report.products')->withdata($pro);
  }

  
  
  #Invoices
  public function report_invoices()
  {
      $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');




    
  if(!empty($from_month)) {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all')
          $pro = Invoice::whereRaw('year(created_at) = ?', [$from_year])->get();
       else if ($from_month == 'all' && $to_month != 'all')
          $pro = Invoice::whereRaw('month(created_at) <= ?', [$to_month])->whereRaw('year(created_at) = ?', [$from_year])->get();
       else
          $pro = \DB::table('invoices')->whereRaw('month(created_at) >= ?', [$from_month])->whereRaw('month(created_at) <= ?', [$to_month])->whereRaw('year(created_at) >= ?', [$from_year])->whereRaw('year(created_at) <= ?', [$to_year])->get();
      } else
          $pro = Invoice::where('id','-1')->get();

        return view('backend.report.invoices')->withdata($pro);
  }
  
  


  #Expenses
  public function report_expenses()
  {
      $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');
    
  if(!empty($from_month)) {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all')
          $pro = Expense::whereRaw('year(date) = ?', [$from_year])->get();
       else if ($from_month == 'all' && $to_month != 'all')
          $pro = Expense::whereRaw('month(date) <= ?', [$to_month])->whereRaw('year(date) = ?', [$from_year])->get();
       else 
          $pro = \DB::table('expenses')->whereRaw('month(date) >= ?', [$from_month])->whereRaw('month(date) <= ?', [$to_month])->whereRaw('year(date) >= ?', [$from_year])->whereRaw('year(date) <= ?', [$to_year])->get();
      } else 
          $pro = Expense::where('id','-1')->get();

        return view('backend.report.expenses')->withdata($pro);
  }


#Balance
  public function report_balance()
  {
      $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');
    
  if(!empty($from_month)) {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all') {
          $pro = Balance::whereRaw('year(date) = ?', [$from_year])->get();
          $in = Balance::whereRaw('year(date) = ?', [$from_year])->where('type',0)->sum('amount');
          $out = Balance::whereRaw('year(date) = ?', [$from_year])->where('type',1)->sum('amount');
      } else if ($from_month == 'all' && $to_month != 'all') {
          $pro = Balance::whereRaw('month(date) <= ?', [$to_month])->whereRaw('year(date) = ?', [$from_year])->get();
          $in = Balance::whereRaw('month(date) <= ?', [$to_month])->where('type',0)->sum('amount');
          $out = Balance::whereRaw('month(date) <= ?', [$to_month])->where('type',1)->sum('amount');
      } else {
          $pro = \DB::table('balances')->whereRaw('month(date) >= ?', [$from_month])->whereRaw('month(date) <= ?', [$to_month])->whereRaw('year(date) >= ?', [$from_year])->whereRaw('year(date) <= ?', [$to_year])->get();
          $in = \DB::table('balances')->whereRaw('month(date) >= ?', [$from_month])->whereRaw('month(date) <= ?', [$to_month])->whereRaw('year(date) >= ?', [$from_year])->whereRaw('year(date) <= ?', [$to_year])->where('type',0)->sum('amount');
          $out = \DB::table('balances')->whereRaw('month(date) >= ?', [$from_month])->whereRaw('month(date) <= ?', [$to_month])->whereRaw('year(date) >= ?', [$from_year])->whereRaw('year(date) <= ?', [$to_year])->where('type',1)->sum('amount');
        }
      } else {
          $pro = Balance::where('id','-1')->get();
          $in = 0;
          $out = 0;
      }
        return view('backend.report.balance')->withdata($pro)->within($in)->without($out);
  }



 #Cash
  public function report_cash()
  {
      $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');
    
    if(!empty($from_month))   {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all') {
         $pro = Cash::whereRaw('year(created_at) = ?', [$from_year])->get();
      } else if ($from_month == 'all' && $to_month != 'all') {
          $pro = Cash::whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) = ?', [$from_year])->get();
      } else {
          $pro = \DB::table('cashes')
                            ->whereRaw('month(created_at) >= ?', [$from_month])
                            ->whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) >= ?', [$from_year])
                            ->whereRaw('year(created_at) <= ?', [$to_year])
                            ->get();
         }
      } else {
           $pro = Product::where('year','2015')->get();
      }

     return view('backend.report.cash')->withdata($pro);
  }

  public function report_cash_check($id)
  {
     $order = Cash::find($id);
       $data = [];
       $total = 0;
     foreach(json_decode($order->cash_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
    return view('backend.report.check_cash')->withrows($order)->withdata($data);
  }



  
#Order
  public function report_order()
  {
      $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');
    
    if(!empty($from_month))   {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all') {
         $pro = Order::whereRaw('year(created_at) = ?', [$from_year])->get();
      } else if ($from_month == 'all' && $to_month != 'all') {
          $pro = Order::whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) = ?', [$from_year])->get();
      } else {
          $pro = \DB::table('orders')
                            ->whereRaw('month(created_at) >= ?', [$from_month])
                            ->whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) >= ?', [$from_year])
                            ->whereRaw('year(created_at) <= ?', [$to_year])
                            ->get();
          }
      } else {
           $pro = Product::where('year','2015')->get();
      }

     return view('backend.report.order')->withdata($pro);
  }



  public function report_order_check($id)
  {
      $order = Order::find($id);
       $data = [];
     foreach(json_decode($order->order_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
     return view('backend.report.check_order')->withrows($order)->withdata($data);
  }
  



#Installment
  public function report_installment()
  {
      $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');
    
    if(!empty($from_month))   {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all') {
         $pro = Installment::whereRaw('year(created_at) = ?', [$from_year])->get();
      } else if ($from_month == 'all' && $to_month != 'all') {
          $pro = Installment::whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) = ?', [$from_year])->get();
      } else {
          $pro = \DB::table('installments')
                            ->whereRaw('month(created_at) >= ?', [$from_month])
                            ->whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) >= ?', [$from_year])
                            ->whereRaw('year(created_at) <= ?', [$to_year])
                            ->get();
         }
      } else {
           $pro = Product::where('year','2015')->get();
      }

     return view('backend.report.installment')->withdata($pro);
  }



  public function report_installment_check($id)
  {
      $order = Installment::find($id);
       $data = [];
     foreach(json_decode($order->order_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
     return view('backend.report.check_installment')->withrows($order)->withdata($data);
  }
  


 
 #Customer
  public function report_customer()
  {
     $from_month = Input::get('from_month');
      $from_year = Input::get('from_year');
      $to_month = Input::get('to_month');
      $to_year = Input::get('to_year');
    
    if(!empty($from_month))   {
      if($from_month != 'all' && $from_month > $to_month && $from_year == $to_year){
            Session::flash('error','"من شهر" اكبر من "إلي شهر"');
            return Redirect::back();
         }

      if($from_month == 'all' && $to_month == 'all') {
         $pro = Customer::whereRaw('year(created_at) = ?', [$from_year])->get();
      } else if ($from_month == 'all' && $to_month != 'all') {
          $pro = Customer::whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) = ?', [$from_year])->get();
      } else {
          $pro = \DB::table('customers')
                            ->whereRaw('month(created_at) >= ?', [$from_month])
                            ->whereRaw('month(created_at) <= ?', [$to_month])
                            ->whereRaw('year(created_at) >= ?', [$from_year])
                            ->whereRaw('year(created_at) <= ?', [$to_year])
                            ->get();
         }
      } else {
           $pro = Product::where('year','2015')->get();
      }

     return view('backend.report.customer')->withdata($pro);
  }





}
