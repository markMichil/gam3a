<?php

namespace App\Http\Controllers;

use App\Cash;
use App\Balance;
use App\Product;
use App\Cash_other;

use DB;
use Auth;
use Input;
use Session;
use Redirect;
use Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CashController extends Controller
{
  
    public function index()
    {
      $data = Cash::orderby('id','ASC')->get();
      return view('backend.cash.list')->withdata($data);
    }

    public function create()
    {
       $data = Cash_other::where('status',0)->get();
       return view('backend.cash.create')->withdata($data);
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

         $new_cash = new Cash;
         $new_cash->cash_id         =  json_encode($all_cash);
		 $new_cash->name            = $request->input('name');
		 $new_cash->phone           = $request->input('phone');
		 $new_cash->total           = $request->input('total');
		 $new_cash->discount        = $request->input('discount');
		 $new_cash->total_after_dis = $request->input('total_after_dis');
		 $new_cash->notes           = $request->input('note');
         $new_cash->save();
		 
		   /* Balance */
             $rows = new Balance;
             $rows->type = 0;
             $rows->date = date('Y-m-d');
             $rows->amount = $new_cash->total_after_dis;
			 $rows->reason = 'مبيعات نقدية';
             $rows->save();	
			 
         Cash_other::where('status', '=', 0)->update(['status' => '1']);
         Session::flash('success','تم إضافة الفاتورة بنجاح');
         return Redirect::to('sales/cash');
    }


   public function edit($id)
   {
     $rows = Cash::find($id);
     $data = [];
     foreach(json_decode($rows->cash_id) as $row){
         $data[] = Cash_other::where('id',$row)->first();
     }
     return view('backend.cash.edit')->withrows($rows)->withdata($data)->withgetid($id);
   }

   public function update($id, Request $request)
   {
         $new_cash = Cash::find($id);
		 $new_cash->name = $request->input('name');
		 $new_cash->phone = $request->input('phone');
		 $new_cash->total = $request->input('total');
		 $new_cash->discount = $request->input('discount');
		 $new_cash->total_after_dis = $request->input('total_after_dis');
		 $new_cash->notes = $request->input('note');
         $new_cash->save();
		 
		    /* Balance */
             $rows = new Balance;
             $rows->type = 0;
             $rows->date = date('Y-m-d');
             $rows->amount = $new_cash->total_after_dis;
			 $rows->reason = 'مبيعات نقدية';
             $rows->save();
		 
         Session::flash('success','تم تعديل الفاتورة بنجاح');
         return Redirect::to('sales/cash');
   }

    public function destroy($id)
   {
       $cash = Cash::find($id);
           foreach (json_decode($cash->cash_id) as $val) {
             Cash_other::where('id',$val)->delete();
        }
       $cash->delete();
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
        $row = Cash::find($id);
        foreach(json_decode($row->cash_id) as $cashs) {
           $data[] = $cashs;
       }
           $data[] = $nrow->id;

        $row->cash_id = json_encode($data);
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
        $cashs = Cash::where('id',$id)->first();
           $total = 0;
        foreach(json_decode($cashs->cash_id) as $cash){
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
    $app = Cash::find($getid);
    $decoded = json_decode($app->cash_id, true);
   if(($key = array_search($id, $decoded)) !== false) {
      unset($decoded[$key]);
   }
      $app->cash_id = json_encode($decoded);
      $app->save();
             Session::flash('success','تم حذف المشتريات بنجاح');
           } catch(\Exception $e) {
             Session::flash('error','لم يتم حذف المشتريات');
           }
        return Redirect::back();
    }




    






   


    


    
  #Invoice
    public function invoice()
    {
	  
      $data = Cash_other::where('status',0)->orderby('id','ASC')->get();
      return view('backend.cash.invoice')->withdata($data);
    }

    public function invoice_id($id)
    {
      $cashs = Cash::where('id',$id)->first();
        foreach(json_decode($cashs->cash_id) as $cash){
          $data[] = Cash_other::where('id',$cash)->first();
        }
      return view('backend.cash.invoice')->withdata($data)->withrow($cashs);
    }




   
}
