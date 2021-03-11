<?php

namespace App\Http\Controllers;


use App\Product;
use App\Installment;
use App\Cash_other;
use App\Installment_other;
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

class InstallmentController extends Controller
{
    

   public function index_published()
   {
      $title = "مبيعات القسط";
      $res = 1;
      $data = Installment::orderby('id','ASC')->where('remain','>',0)->get();
      return view('backend.installment.list')->withdata($data)->withres($res)->withtitle($title);
   }

   public function index_unpublished()
   {
      $title = "مبيعات القسط المنتهية";
      $res = 2;
      $data = Installment::orderby('id','ASC')->where('remain',0)->get();
      return view('backend.installment.list')->withdata($data)->withres($res)->withtitle($title);
   }


   public function create()
   {
      $data = Cash_other::where('status',0)->get();
      return view('backend.installment.create')->withdata($data);
   }

   public function store(Request $request)
   {
       $cashs = Cash_other::where('status',0)->get();
         foreach($cashs as $cash){
          $all_cash[] = $cash->id;
            $pro = Product::where('pro_code',$cash->pro_code)->first();
             $remain_qty = $pro->qty - $cash->qty;
            $pro->qty = $remain_qty;
            $pro->save();
         }

         $order = new Installment;
         $order->order_id =  json_encode($all_cash);
         $order->national_id = $request->input('national_id');
         $order->name = $request->input('name');
         $order->phone = $request->input('phone');
         $order->address = $request->input('address');
         $order->total = $request->input('total');
         $order->paid = $request->input('paid');
         $order->remain = $request->input('remain');
         $order->start_date = $request->input('start');
         $order->end_date = $request->input('end');
         $order->count = $request->input('count');
         $order->each_amount = $request->input('inst_cost');
         $order->comment = $request->input('comment');
		 
		 /* Balance */
             $rows = new Balance;
             $rows->type = 0;
             $rows->date = date('Y-m-d');
             $rows->amount = $order->total;
		$rows->reason = 'مبيعات قسط';
             $rows->save();	

          if($request->input('per_type') == 1){
              $each_st = $request->input('per_day');
              $per = 'day';
            } else{
              $each_st = $request->input('per_month');
              $per = 'month';
            }

         $order->each_inst = $each_st;
         $order->each_type = $request->input('per_type');
         
         $start_date = $request->input('start');
             
             $all_others = [];
             $per_date = 0;
         for ($i=1; $i <= $request->input('count'); $i++) { 
             $per_date += $each_st;
             $other = new Installment_other;
             $other->amount = $request->input('inst_cost');
             $date = strtotime($per_date." ".$per, strtotime($start_date));
             $other->amount_date = date("Y-m-d", $date);
             $other->save();
             $all_others[] = $other->id;
         }
          
         $order->others_id = json_encode($all_others);
         $order->save();

         Cash_other::where('status', '=', 0)->update(['status' => '1']);
         Session::flash('success','تم إضافة الفاتورة بنجاح');
         return Redirect::to('sales/installment/published');
   }

   public function edit($id)
   {
     $order = Installment::find($id);
       $data = [];
     foreach(json_decode($order->order_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
     return view('backend.installment.edit')->withrows($order)->withdata($data)->withgetid($id);
   }


   public function update($id,Request $request)
   {
         $order = Installment::find($id);
         $order->national_id = $request->input('national_id');
         $order->name = $request->input('name');
         $order->phone = $request->input('phone');
         $order->address = $request->input('address');
         $order->total = $request->input('total');
         $order->paid = $request->input('paid');
         $order->remain = $request->input('remain');
         $order->start_date = $request->input('start');
         $order->end_date = $request->input('end');
         $order->count = $request->input('count');
         $order->each_amount = $request->input('inst_cost');
         $order->comment = $request->input('comment');
        
         foreach(json_decode($order->others_id) as $others) {
            Installment_other::where('id',$others)->delete();
          }

          if($request->input('per_type') == 1){
              $each_st = $request->input('per_day');
              $per = 'day';
            } else{
              $each_st = $request->input('per_month');
              $per = 'month';
            }

         $order->each_inst = $each_st;
         $order->each_type = $request->input('per_type');
         
         $start_date = $request->input('start');
             
             $all_others = [];
             $per_date = 0;
         for ($i=1; $i <= $request->input('count'); $i++) { 
             $per_date += $each_st;
             $other = new Installment_other;
             $other->amount = $request->input('inst_cost');
             $date = strtotime($per_date." ".$per, strtotime($start_date));
             $other->amount_date = date("Y-m-d", $date);
             $other->save();
             $all_others[] = $other->id;
         }
          
         $order->others_id = json_encode($all_others);
         $order->save();

         Cash_other::where('status', '=', 0)->update(['status' => '1']);
         Session::flash('success','تم تعديل الفاتورة بنجاح');
         return Redirect::back();
   }




   public function destroy_unyet($id)
   {
      $order = Installment::find($id);
         $data = [];
        foreach(json_decode($order->order_id) as $orders) {
          if( Cash_other::where('id',$orders)->count() > 0 ){
            $cash = Cash_other::where('id',$orders)->first();
            $pro = Product::where('pro_code',$cash->pro_code)->first();
            $pro->qty = $pro->qty + $cash->qty;
            $pro->save();
          }
        }
        foreach(json_decode($order->order_id) as $orderz) {
          if( Cash_other::where('id',$orderz)->count() > 0 ){
            $cash = Cash_other::where('id',$orderz)->delete();
          }
        }
        foreach (json_decode($order->others_id) as $others) {
          if( Cash_other::where('id',$others)->count() > 0 ){
               $oth = Installment_other::where('id',$others)->delete();
           }
        }
      $order->delete();
      Session::flash('success','تم حذف المنتج بنجاح');
      return Redirect::back();
   }


   public function destroy($id)
   {
     $order = Installment::find($id);
        foreach(json_decode($order->order_id) as $orderz) {
          if( Cash_other::where('id',$orderz)->count() > 0 ){
            $cash = Cash_other::where('id',$orderz)->delete();
          }
        }
        foreach (json_decode($order->others_id) as $others) {
          if( Cash_other::where('id',$others)->count() > 0 ){
               $oth = Installment_other::where('id',$others)->delete();
           }
        }
      $order->delete();
      Session::flash('success','تم حذف المنتج بنجاح');
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
        $row = Installment::find($id);
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
        $cashs = Installment::where('id',$id)->first();
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
    $app = Installment::find($getid);
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




   

    #View Order
   public function view_order($id)
    {
       $order = Installment::find($id);
       $data = [];
     foreach(json_decode($order->order_id) as $orders){
         $data[] = Cash_other::where('id',$orders)->first();
     }
     return view('backend.installment.view')->withrows($order)->withdata($data)->withgetid($id);
    }

    #Pay
    public function pay_inst($id,$main)
   {
      //$insty = Installment_other::find($id);
	  //$insty->amount = Input::get('remain_amount');
      //$insty->status = 1;
      //$insty->save();

      //$insto = Installment::find($main);
      //$insto->paid = $insto->paid + Input::get('remain_amount');
      //$insto->remain = $insto->remain - Input::get('remain_amount');
      //$insto->save();

	  
	  $insto = Installment::find($main);
	  $insty = Installment_other::find($id);
	  $insto->remain = $insto->remain - $insty->amount;
	  $insto->paid = $insto->paid + $insty->amount;
      $insto->status = 1;
      $insto->save();
	  
	  $insty->status = 1;
	  $insty->save();
	  
      Session::flash('success','تم دفع القسط بنجاح');
      return Redirect::back();
   }
   
   public function updated_inst($id)
   {
	    
		$insty = Installment_other::find($id);
	   
	        /* Balance */
             $rows = new Balance;
             $rows->type = 0;
             $rows->date = date('Y-m-d');
             $rows->amount = Input::get('paid') - $insty->paid;
			 $rows->reason = 'قسط';
             $rows->save();	
			 
	   
	   $insty->amount = Input::get('remain_amount');
	   $insty->paid = Input::get('paid');
	   $insty->remain = Input::get('remains');
	   $insty->note = Input::get('note');
	   
	 if(Input::get('remains') == 0)  
		  $insty->status = 1;
	  
	  
	   $insty->save();
	   
	   	    
	   
	   Session::flash('success','تم التعديل بنجاح');
       return Redirect::back();
   }


  #Invoice
    public function invoice($id)
   {
       $cashs = Installment::where('id',$id)->first();
        foreach(json_decode($cashs->order_id) as $cash){
          $data[] = Cash_other::where('id',$cash)->first();
        }
      return view('backend.order.invoice')->withdata($data)->withraw($cashs);
   }






}
