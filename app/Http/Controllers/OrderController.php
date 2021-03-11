<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use App\Cash_other;
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

class OrderController extends Controller
{
    

   public function index_published()
   {
      $title = "مبيعات الآجل";
      $res = 1;
      $data = Order::orderby('id','ASC')->where('remain','>',0)->get();
      return view('backend.order.list')->withdata($data)->withtitle($title)->withres($res);
   }

   public function index_unpublished()
   {
      $title = "مبيعات الآجل المنتهية";
      $res = 2;
      $data = Order::orderby('id','ASC')->where('remain',0)->get();
      return view('backend.order.list')->withdata($data)->withtitle($title)->withres($res);
   }

   public function create()
   {
     $data = Cash_other::where('status',0)->get();
     return view('backend.order.create')->withdata($data);
   }

   public function store(Request $request)
   {
        $cashs = Cash_other::where('status',0)->get();
         foreach($cashs as $cash){
           $all_cash[] = $cash->id;
            $pro = Product::where('pro_code',$cash->pro_code)->first();
            $pro->qty = $pro->qty - $cash->qty;
            $pro->save();
         }

         $order = new Order;
         $order->order_id =  json_encode($all_cash);
         $order->national_id = $request->input('national_id');
         $order->name = $request->input('name');
         $order->phone = $request->input('phone');
         $order->address = $request->input('address');
         $order->total = $request->input('total');
         $order->paid = $request->input('paid');
         $order->remain = $request->input('remain');
         $order->remain_date = $request->input('remain_date');
         $order->comment = $request->input('comment');
         $order->save();
		 
		    /* Balance */
             $rows = new Balance;
             $rows->type = 0;
             $rows->date = date('Y-m-d');
             $rows->amount = $order->total;
			 $rows->reason = 'مبيعات آجل';
             $rows->save();	

         Cash_other::where('status', '=', 0)->update(['status' => '1']);
         Session::flash('success','تم إضافة الفاتورة بنجاح');
         return Redirect::to('sales/order/published');
   }

   public function edit($id)
   {
       $order = Order::find($id);
        $data = [];
     foreach(json_decode($order->order_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
     return view('backend.order.edit')->withrows($order)->withdata($data)->withgetid($id);
   }


   public function update($id, Request $request)
   {
      $order = Order::find($id);
         
          $all_orders = [];
         foreach(json_decode($order->order_id) as $orders){
           $all_orders[] = Cash_other::where('id',$orders)->first();
         }

         $order->national_id = $request->input('national_id');
         $order->name = $request->input('name');
         $order->phone = $request->input('phone');
         $order->address = $request->input('address');
         $order->total = $request->input('total');
         $order->paid = $request->input('paid');
         $order->remain = $request->input('remain');
         $order->remain_date = $request->input('remain_date');
         $order->comment = $request->input('comment');
         $order->save();

         Session::flash('success','تم تعديل الفاتورة بنجاح');
         return Redirect::back();
   }


   public function destroy($id)
   {
      $order = Order::find($id);
        foreach (json_decode($order->order_id) as $val) {
            Cash_other::where('id',$val)->delete();
        }
      $order->delete();
       Session::flash('success','تم حذف الفاتورة بنجاح');
      return Redirect::back();
   }

   public function destroy_unyet($id)
   {
     $order = Order::find($id);
        foreach (json_decode($order->order_id) as $val) {
          if( Cash_other::where('id',$val)->count() > 0 ){
             $cash = Cash_other::where('id',$val)->first();
             $pro = Product::where('pro_code',$cash->pro_code)->first();
             $pro->qty = $pro->qty + $cash->qty;
             $pro->save();
          }
        }
        foreach (json_decode($order->order_id) as $del) {
           if( Cash_other::where('id',$val)->count() > 0 ){
              $cash = Cash_other::where('id',$val)->delete();
           }
        }
       $order->delete();
       Session::flash('success','تم حذف الفاتورة بنجاح');
       return Redirect::back();
   }










    #Search
    public function search_pro(Request $request)
    {
       $procode = $request->input('val');

     if(Product::where('pro_code',$procode)->count() > 0 ) 
     {
       $pro = Product::where('pro_code',$procode)->first();
       return Response::json([
          'state'=>true,
          'procode' => $pro->pro_code,
          'image' => url($pro->image),
          'proname' => $pro->slug,
          'price' => $pro->price,
          'qty' => $pro->qty
          ]);
       } else
        return Response::json(['state'=>false]); 
    }
   

   #Add To Cart
    public function add_to_cart(Request $request)
    {
        $row = new Cash_other;
        $row->pro_code = $request->input('procode');
        $row->qty = 1;
        $row->price = $request->input('price');
      try
      {
        $row->save();
        Session::flash('success','تم إضافة المشتريات بنجاح');
      }catch(\Exception $e) {
        Session::flash('error','لم يتم إضافة المشتريات');
      }
      return Redirect::back();
    }


    public function add_to_cart_update($id,Request $request)
    {
        $nrow = new Cash_other;
        $nrow->pro_code = $request->input('procode');
        $nrow->qty = 1;
        $nrow->price = $request->input('price');
        $nrow->status = 1;

        $spro = Product::where('pro_code',$nrow->pro_code)->first();
        $spro->qty = $spro->qty - 1;
        $spro->save();
      try
      {
        $nrow->save();
        $row = Order::find($id);
        foreach(json_decode($row->order_id) as $cashs) {
           $data[] = $cashs;
       }
           $data[] = $nrow->id;

        $row->order_id = json_encode($data);
        $row->save();

        Session::flash('success','تم إضافة المشتريات بنجاح');
      }catch(\Exception $e) {
        Session::flash('error','لم يتم إضافة المشتريات');
      }
      return Redirect::back();
    }






  # Calc Total Cart
  public function calc_total_cart()
  {
    $cashs = Cash_other::where('status',0)->get();
       $total = 0;
      foreach($cashs as $cash){
           $total += $cash->price*$cash->qty;
      }
      return Response::json([
          'state'=>true,
          'total' => $total,
      ]);
  }


  public function calc_total_cart_update($id)
    {
        $cashs = Order::where('id',$id)->first();
           $total = 0;
        foreach(json_decode($cashs->order_id) as $cash){
          $pro = Cash_other::where('id',$cash)->first();
          $total += $pro->price*$pro->qty;
        }

        return Response::json([
          'state'=>true,
          'total' => $total,
          ]);
    }



    # Update Qty
   public function update_qty($id,$value, Request $request)
    {
       $cash = Cash_other::find($id);
       $cash->qty = $value;
     try{
       $cash->save();
         Session::flash('success','تم تعديل المبيعات بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم تعديل المبيعات');
        }
    }

    public function update_qty_update($id,$value, Request $request)
    {
       $cash = Cash_other::find($id);
       $pro = Product::where('pro_code',$cash->pro_code)->first();

          if($value > $cash->qty) {
            $new_qty = $value - $cash->qty;
            $pro->qty = $pro->qty - $new_qty;
          } else {
            $new_qty = $cash->qty - $value;
            $pro->qty = $pro->qty + $new_qty;
          }
       $pro->save();
       $cash->qty = $value;
     try{
       $cash->save();
         Session::flash('success','تم تعديل المبيعات بنجاح');
      }catch(\Exception $e){
         Session::flash('error','لم يتم تعديل المبيعات');
        }
    }



    # Remove Form Cart
     public function remove_from_cart($id)
    {
       $cash = Cash_other::find($id);
        if(!$cash) {
          return Redirect::back();}
       try{
             $cash->delete();
             Session::flash('success','تم حذف المشتريات بنجاح');
           } catch(\Exception $e) {
             Session::flash('error','لم يتم حذف المشتريات');
           }
        return Redirect::back();
    }

    public function remove_from_cart_update($id,$getid)
    {
       $cash = Cash_other::find($id);
        if(!$cash) {
          return Redirect::back();}
       try{
           $pro = Product::where('pro_code',$cash->pro_code)->first();
           $pro->qty = $pro->qty + $cash->qty;
           $pro->save();
             $cash->delete();

       // remove from table json
    $app = Order::find($getid);
    $decoded = json_decode($app->order_id, true);
   if(($key = array_search($id, $decoded)) !== false) {
      unset($decoded[$key]);
   }
      $app->order_id = json_encode($decoded);
      $app->save();
             Session::flash('success','تم حذف المشتريات بنجاح');
           } catch(\Exception $e) {
             Session::flash('error','لم يتم حذف المشتريات');
           }
        return Redirect::back();
    }







   #View
   public function view_order($id)
   {
       $order = Order::find($id);
       $data = [];
     foreach(json_decode($order->order_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
     return view('backend.order.view')->withrows($order)->withdata($data)->withgetid($id);
   }

    # Pay Remain
   public function order_pay_remain($id)
   {   
   
         /* Balance */
             $rows = new Balance;
             $rows->type = 0;
             $rows->date = date('Y-m-d');
             $rows->amount = $order->remain;
			 $rows->reason = 'الآجل';
             $rows->save();	
			 
       $order = Order::find($id);
       $order->paid = $order->paid + $order->remain;
       $order->remain = 0;
       $order->status = 1;
       $order->save();
	   
       Session::flash('success','تم دفع المبلغ المتبقي بنجاح');
       return Redirect::to('sales/order/unpublished');
   }

    # Invoice
   public function order_invoice($id)
   {
     $app = Order::find($id);
     $data = [];
     foreach(json_decode($app->order_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
     return view('backend.order.invoice')->withdata($data)->withraw($app);
   }
   
}
