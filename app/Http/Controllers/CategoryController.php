<?php

namespace App\Http\Controllers;

use App\Category;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $data = Category::get();
        return view('backend.category.list')->withdata($data);
    }

    public function create()
    {
        return view('backend.category.create');
    }

    public function store(Request $request)
    {
        $row = new Category;
        $row->slug = $request->input('slug');
        $row->parent = $request->input('parent');
        try {
            $row->save();
            Session::flash('success','تم حفظ القسم بنجاح');
            return Redirect::to('categories');
        } catch (\Exception $e){
            Session::flash('error','لم يتم حفظ القسم');
            return Redirect::back();
         }
    }

    public function edit($id)
    {
        $row = Category::find($id);
        return view('backend.category.edit')->withrow($row);
    }

    public function update($id, Request $request)
    {
        $row = Category::find($id);
        $row->slug = $request->input('slug');
        $row->parent = $request->input('parent');
        try {
            $row->save();
            Session::flash('success','تم حفظ التعديل بنجاح');
            return Redirect::to('categories');
        } catch (\Exception $e){
            Session::flash('error','لم يتم حفظ التعديل');
            return Redirect::back();
         }
    }

    public function destroy($id)
    {
        $row = Category::find($id);
        $row->delete();
        Session::flash('success','تم حذف القسم بنجاح');
        return Redirect::back();
    }



}
