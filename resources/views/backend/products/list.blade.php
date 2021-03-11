@extends('backend.layouts.layout')
@section('content')


<section id="min-wrapper">
  <div id="main-content">
    <div class="container-fluid">
       <div class="row">
          <div class="col-md-12">
          <h3 class="ls-top-header">المنتجات</h3>
              <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">جميع المنتجات ( {{count($data)}} )</h3>
                 </div>

    <div class="panel-body">

@if(Session::has('success'))
 <p class="alert alert-success">{{Session::get('success')}}</p>
@elseif(Session::has('error'))
 <p class="alert alert-danger">{{Session::get('error')}}</p>
@endif

          <a href="{{ url('products/create') }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> أضف منتج جديد</a><br/><br/>

        <div class="ls-editable-table table-responsive ls-table" style='font-family:Armata'>
              <table class="table table-bordered  table-bottomless" id="ls-editable-table">
                <thead>
                    <tr>
                     <th class="text-center" style='width:5%;font-weight:bold'>#</th>
                     <th class="text-center"  style='width:10%;font-weight:bold'>التاريخ</th>
                     <th class="text-center"  style='width:10%;font-weight:bold'>كود المنتج</th>
                     <th class="text-center"  style='width:10%;font-weight:bold'>صورة</th>
                     <th class="text-center"  style='width:10%;font-weight:bold'>الأسم</th>
                     <th class="text-center"  style='width:10%;font-weight:bold'>القسم</th>
                     <th class="text-center"  style='width:10%;font-weight:bold'>الكمية</th>
                     <th class="text-center"  style='width:10%;font-weight:bold'>السعر</th>
                     <th class="text-center"  style='width:15%;font-weight:bold'>الحدث</th>
                    </tr>
               </thead>
               <tbody>
             
                  
                  @foreach($data as $key => $row)
                       @if($row->qty <= 0)
                         <tr style="background:#ffe0e0">
                      @else
                         <tr>
                      @endif

                          <td class='text-center'>{{$key+1}}</td>
                          <td class='text-center'>{{explode(' ',$row->created_at)[0]}}</td>
                          <td class='text-center'>{{$row->pro_code}}</td>
                          <td class='text-center'><img src="{{url($row->image)}}" style="width:70px;height:70px;border:1px solid #ddd"></td>
                          <td class='text-center'>{{$row->slug}}</td>
                          <td class='text-center'>
                            @if(App\Category::where('id',$row->cat_id)->count() > 0 )
                               @foreach(App\Category::where('id',$row->cat_id)->get() as $sub) @endforeach
                              <span class="label label-warning">{{$sub->slug}}</span>
                            @endif  
                          </td>
                          <td class='text-center'>{{$row->qty}}</td>
                          <td class='text-center'>{{$row->price}}</td>
                          <td class='text-center'>
                          	 {!! Form::Open(['url'=>'products/del/'.$row->id]) !!}
                                 <a href="{{ url('products/edit/'.$row->id) }}" class="btn btn-success"><i class="fa fa-edit"></i> تعديل</a>
                                 <button class="btn btn-danger confirmClickAction"><i class="fa fa-trash"></i> حذف</button>
                              {!! Form::Close() !!}
                          </td>                       
                      </tr>
                  @endforeach


                  
                </tbody>
               </table>
            </div>



         </div>
      </div>
  </div>
</div>
</section>
@stop

