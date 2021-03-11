<?php

namespace App\Http\Controllers;

use App\Product;
use Input;
use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
   
   public function index()
    {
        $data = Product::get();
        return view('backend.products.list')->withdata($data);
    }

    public function create()
    {
        return view('backend.products.create');
    }

    public function store(Request $request)
    {
       $rules =
         [
          'pro_code' => 'unique:products,pro_code',
         ];

       $validate = Validator::make(Input::all(),$rules);
      if($validate->fails())
        {
          return Redirect::back()->withInput()->withErrors($validate);
        }

        $row = new Product;
        $row->pro_code = $request->input('pro_code');
        $row->cat_id = $request->input('cat_id');
      
	if(Input::hasFile('image'))
      {
        $file = Input::file('image');
        $path = 'public/uploads/';
        $filename = date('Y-m-d-h-s-i').'.'.$file->getClientOriginalName();
        $file->move($path,$filename);
        $row->image = $path.$filename;
      }

        $row->slug = $request->input('slug');
        $row->content = $request->input('content');
        $row->cost_price = $request->input('cost_price');
        $row->price = $request->input('price');
        $row->qty = $request->input('qty');
        $row->month = date('n');
        $row->year = date('Y');
     try{
        $row->save();
        Session::flash('success','تم إضافة المنتج بنجاح');
          return Redirect::to('products');
     } catch(\Exception $e)
       {
          Session::flash('error','لم يتم إضافة المنتج'); 
          return Redirect::back();
       }
    }

    public function edit($id)
    {
        $row = Product::find($id);
        return view('backend.products.edit')->withrow($row);
    }

    public function update($id,Request $request)
    {
        $row = Product::find($id);
        $row->pro_code = $request->input('pro_code');
        $row->cat_id = $request->input('cat_id');
       
       if(Input::hasFile('image'))
        {
           $file = Input::file('image');
           $path = 'public/uploads/';
           $filename = date('Y-m-d-h-s-i').'.'.$file->getClientOriginalName();
           $file->move($path,$filename);
           $row->image = $path.$filename;
        }

        $row->slug = $request->input('slug');
        $row->content = $request->input('content');
        $row->cost_price = $request->input('cost_price');
        $row->price = $request->input('price');
        $row->qty = $request->input('qty');
     try{
        $row->save();
            Session::flash('success','تم تعديل المنتج بنجاح');
            return Redirect::to('products');
     } catch(\Exception $e){ 
            Session::flash('error','لم يتم تعديل المنتج'); 
            return Redirect::back(); 
        }
    }

    public function destroy($id)
    {
        $row = Product::find($id);
        try{
            $row->delete();
            Session::flash('success','تم حذف المنتج بنجاح');
        }catch(\Exception $e){ 
            Session::flash('error','لم يتم حذف المنتج'); 
         }
           return Redirect::back();
    }



}
