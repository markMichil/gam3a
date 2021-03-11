@extends('backend.layouts.layout')
@section('content')


<section id="min-wrapper">
  <div id="main-content">
    <div class="container-fluid">
       <div class="row">
          <div class="col-md-12">
          <h3 class="ls-top-header">العملاء</h3>
              <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">جميع العملاء ( {{count($data)}} )</h3>
                 </div>

    <div class="panel-body">

@if(Session::has('success'))
 <p class="alert alert-success">{{Session::get('success')}}</p>
@elseif(Session::has('error'))
 <p class="alert alert-danger">{{Session::get('error')}}</p>
@endif

          <a href="{{ url('customer/create') }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> أضف عميل جديد</a><br/><br/>

        <div class="ls-editable-table table-responsive ls-table" style='font-family:Armata'>
              <table class="table table-bordered  table-bottomless" id="ls-editable-table">
                <thead>
                    <tr>
                     <th class="text-center" style='width:5%;font-weight:bold'>#</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>أسم العميل</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>تاريخ اخر قسط</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>الإجمالي</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>المدفوع</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>المتبقي</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>الحدث</th>
                    </tr>
               </thead>
               <tbody>
             
                  @foreach($data as $key => $row)
                      <tr>
                          <td class='text-center'>{{$key+1}}</td>
                          <td class='text-center'>{{$row->name}}</td>
                          <td class='text-center'>{{App\Customer::get_last_date($row->id)}}</td>
                          <td class='text-center'>{{$row->total}}</td>
                          <td class='text-center'>{{$row->paid}}</td>
                          <td class='text-center'>{{$row->remain}}</td>
                          <td class='text-center'>
                          	{!! Form::Open(['url'=>'customer/del/'.$row->id]) !!}
                                 <a href="{{ url('customer/edit/'.$row->id) }}" class="btn btn-success"><i class="fa fa-edit"></i> تعديل</a>
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