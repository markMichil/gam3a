<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


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


use App\Http\Requests;
use App\Http\Controllers\Controller;








class ReportsDailyController extends Controller
{

#Products
    public function report_products()
    {

        $pro = DB::table('products')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();

//        $pro = Product::where('created_at', DB::raw('CURDATE()'))->get();
//        dd($pro);
        return view('backend.reportDaily.products')->withdata($pro);
    }



    #Invoices
    public function report_invoices()
    {

        $pro = DB::table('invoices')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();
//        dd($pro);

        return view('backend.reportDaily.invoices')->withdata($pro);
    }




    #Expenses
    public function report_expenses()
    {

        $pro = DB::table('expenses')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();
        return view('backend.reportDaily.expenses')->withdata($pro);
    }


#Balance
    public function report_balance()
    {
        $in = \DB::table('balances') ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->where('type',0)->sum('amount');
        $out = \DB::table('balances') ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->where('type',1)->sum('amount');
        $pro = DB::table('balances')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();


        return view('backend.reportDaily.balance')->withdata($pro)->within($in)->without($out);
    }



    #Cash
    public function report_cash()
    {

        $pro = DB::table('cashes')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();
        return view('backend.reportDaily.cash')->withdata($pro);
    }

    public function report_cash_check($id)
    {
        $order = Cash::find($id);
        $data = [];
        $total = 0;
        foreach(json_decode($order->cash_id) as $orders){
            $data[] = Cash_other::where('id',$orders)->first();
        }
        return view('backend.reportDaily.check_cash')->withrows($order)->withdata($data);
    }




#Order
    public function report_order()
    {

        $pro = DB::table('orders')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();

        return view('backend.reportDaily.order')->withdata($pro);
    }



    public function report_order_check($id)
    {
        $order = Order::find($id);
        $data = [];
        foreach(json_decode($order->order_id) as $orders){
            $data[] = Cash_other::where('id',$orders)->first();
        }
        return view('backend.reportDaily.check_order')->withrows($order)->withdata($data);
    }




#Installment
    public function report_installment()
    {

        $pro = DB::table('installments')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();

        return view('backend.reportDaily.installment')->withdata($pro);
    }



    public function report_installment_check($id)
    {
        $order = Installment::find($id);
        $data = [];
        foreach(json_decode($order->order_id) as $orders){
            $data[] = Cash_other::where('id',$orders)->first();
        }
        return view('backend.reportDaily.check_installment')->withrows($order)->withdata($data);
    }




    #Customer
    public function report_customer()
    {

        $pro = DB::table('customers')->select(DB::raw('*'))
            ->whereRaw('date(created_at) = ?', [date('Y-m-d')])->get();

        return view('backend.reportDaily.customer')->withdata($pro);
    }


}
