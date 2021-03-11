<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Product;
use App\Invoice_attribute;
use App\Balance;

use DB;
use Input;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
   
    public function index()
    {
        $data = Invoice::orderby('id','ASC')->get();
        return view('backend.invoices.list')->withdata($data);
    }

    public function create()
    {
        return view('backend.invoices.create');
    }

    public function store()
    {

 try {
        return DB::transaction(function()
        {
           $row = new Invoice;
           $row->name = Input::get('name');
           $row->phone = Input::get('phone');
           $row->pro_code = Input::get('pro_code');
           $row->price = Input::get('price');
           $row->qty = Input::get('qty');
           $row->content = Input::get('content');
           $row->save();

           $pro = Product::where('pro_code',$row->pro_code)->first();
           $pro->qty = $pro->qty + $row->qty;
           $pro->save();
         
    if(!empty(Input::get('pro_codes'))) {
        $features = [];
            foreach(Input::get('pro_codes') as $key => $feature){
                $features[] = [
                    'invoice_id' => $row->id,
                    'pro_code' => Input::get('pro_codes')[$key],
                    'price' => Input::get('prices')[$key],
                    'qty' => Input::get('qtys')[$key],
                    'content' => Input::get('contents')[$key]
                ];
                $pps = Product::where('pro_code',Input::get('pro_codes')[$key])->first();
                $pps->qty = $pps->qty + Input::get('qtys')[$key];
                $pps->save();
            }
            Invoice_attribute::insert($features);
        }
		
		   if(Input::get('paid') > 0) {
             /* Balance */
             $rows = new Balance;
             $rows->type = 1;
             $rows->date = date('Y-m-d');
             $rows->amount = $row->paid;
	         $rows->reason = 'فاتورة';
             $rows->save();	
			 }


			 

        Session::flash('success','تم حفظ الفاتورة بنجاح');
        return Redirect::to('invoices');
   });

    
}catch(\Exception $e) {
       Session::flash('error','لم يتم حفظ الفاتورة كود المنتج غير صحيح ');
       return Redirect::back();
       }
    
    }


    public function edit($id)
    {
        $row = Invoice::find($id);
		$rows = Invoice_attribute::where('invoice_id',$id)->get();
		      $reset_price = 0;
		   foreach($rows as $roo){
			  $reset_price += $roo->price*$roo->qty;   
		   }
		   
		$basic = $row->price*$row->qty;
		$total = $reset_price + $basic;
        return view('backend.invoices.edit')->withrow($row)->withgetid($id)->withtotal($total);
    }

    public function update($id)
    {

  try {
          $row = Invoice::find($id);

          $pro = Product::where('pro_code',$row->pro_code)->first();
          if(Input::get('qty') > $row->qty){
               $new_qty = Input::get('qty') - $row->qty;
               $pro->qty = $pro->qty + $new_qty;
            } else {
               $new_qty = $row->qty - Input::get('qty');
               $pro->qty = $pro->qty - $new_qty;
            }
           $pro->save();
       
          $row->name = Input::get('name');
          $row->phone = Input::get('phone');
          $row->pro_code = Input::get('pro_code');
          $row->price = Input::get('price');
          $row->qty = Input::get('qty');
          $row->content = Input::get('content');

      $rows = Invoice_attribute::where('invoice_id',$row->id)->get();
          $reset_price = 0;
      foreach($rows as $roo){
        $reset_price += $roo->price*$roo->qty;   
       }
     $basic = $row->price*$row->qty;
     $total = $reset_price + $basic;

          $row->total = $total;
          $row->paid = Input::get('paid');
          $row->remain = Input::get('remain');
          $row->save();

     if(!empty(Input::get('pro_codes'))) {
        $features = [];
          foreach(Input::get('pro_codes') as $key => $feature){

            if(!empty(Input::get('pro_codes')[$key])) {
                $ips = Invoice_attribute::find(Input::get('ids')[$key]);
                $pro = Product::where('pro_code',Input::get('pro_codes')[$key])->first();
			
			  if(Product::where('pro_code',Input::get('pro_codes')[$key])->count() > 0 ) {
					
                 if(Input::get('qtys')[$key] > $ips->qty){
                     $new_qty = Input::get('qtys')[$key] - $ips->qty;
                     $pro->qty = $pro->qty + $new_qty;
                   } else {
                     $new_qty = $ips->qty - Input::get('qtys')[$key];
                     $pro->qty = $pro->qty - $new_qty;
                   }
				   
                 $pro->save();
				 
				 }
            
                $features[] = [
                    'invoice_id' => $id,
                    'pro_code' => Input::get('pro_codes')[$key],
                    'price' => Input::get('prices')[$key],
                    'qty' => Input::get('qtys')[$key],
                    'content' => Input::get('contents')[$key]
                ];
              }

            }
            Invoice_attribute::where('invoice_id',$id)->delete();
            Invoice_attribute::insert($features);
        }
            
            $mexs = Input::get('paid') - Input::get('re_paid');
              /* Balance */
             $rows = new Balance;
             $rows->type = 1;
             $rows->date = date('Y-m-d');
             $rows->amount = $mexs;
             $rows->reason = 'فاتورة';
             $rows->save(); 

        Session::flash('success','تم تعديل الفاتورة بنجاح');
} catch(\Exception $e) {
        Session::flash('error','لم يتم تعديل الفاتورة ');
  }

      return Redirect::back();
}




    public function destroy($id)
    {
        $row = Invoice::find($id);

    if(Invoice_attribute::where('invoice_id',$id)->count() > 0) {
        $sub = Invoice_attribute::where('invoice_id',$id)->get();
          foreach ($sub as $sb) {
             $pro = Product::where('pro_code',$sb->pro_code)->first();
               if($pro->qty >= $sb->qty) {
                   $pro->qty = $pro->qty - $sb->qty;
                   $pro->save();
               }
            $sb->delete();
          } 
        }

       $ipo = Product::where('pro_code',$row->pro_code)->first();
          if($ipo->qty >= $row->qty) {
            $ipo->qty = $ipo->qty - $row->qty;
            $ipo->save();
          }

        try {
             $row->delete();
             Session::flash('success','تم حذف الفاتورة بنجاح');
        } catch(\Exception $e) { 
              Session::flash('error','لم يتم حذف الفاتورة'); 
        }
          return Redirect::back();
    }

    public function destroy_pro($id)
    {
        $row = Invoice_attribute::find($id);
        $pro = Product::where('pro_code',$row->pro_code)->first();
        
          if($pro->qty >= $row->qty) 
             $pro->qty = $pro->qty - $row->qty;
          else 
             $pro->qty = $row->qty - $pro->qty;

        $pro->save();
        $row->delete();
        Session::flash('success','تم حذف المنتج بنجاح');
        return Redirect::back();
    }

}
