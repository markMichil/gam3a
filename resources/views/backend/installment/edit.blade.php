@extends('backend.layouts.layout')
@section('content')

<section id="min-wrapper">
  <div id="main-content">
    <div class="container-fluid">
       <div class="row">
          <div class="col-md-12">
          <h3 class="ls-top-header">تعديل المبيعات القسط</h3>


@if(Session::has('success'))
 <p class="alert alert-success">{{Session::get('success')}}</p>
@elseif(Session::has('error'))
 <p class="alert alert-danger">{{Session::get('error')}}</p>
@endif

  
   <div class="col-md-8">
      <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">المشتريات</h3>
            </div>
      <div class="panel-body">

        <div class="col-md-12">
          <div class="ls-editable-table table-responsive ls-table">
              <table class="table table-bordered  table-bottomless">
                <thead>
                    <tr>
                     <th class="text-center" style='width:5%;font-weight:bold'>#</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>كود المنتج</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>السعر</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>الكمية</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>الإجمالي</th>
                     <th class="text-center" style='width:10%;font-weight:bold'>حذف</th>
                    </tr>
               </thead>
               <tbody>
                    <?php $total = 0; ?>
                  @foreach($data as $key => $row)
                   @foreach(App\Product::where('pro_code',$row->pro_code)->get() as $pro) @endforeach
                      <tr>
                          <td class='text-center'>{{$key+1}}</td>
                          <td class='text-center'>{{$row->pro_code}}</td>
                          <td class='text-center' id="unit_price_{{$pro->id}}">{{$row->price}}</td>
                          <td class='text-center'>
                            <input type="number" step="any" onchange="submit_qty_{{$pro->id}}({{$row->id}});" id="qty_{{$pro->id}}" style="width:80px;padding:3px;" max="{{$pro->qty}}" value="{{$row->qty}}">
                            <p style='color:red;font-size:11px;'>الكمية المتاحة {{$pro->qty}} فقط</p>
                          </td>
                          <td class="text-center" id="total_price_{{$pro->id}}">{{$row->price*$row->qty}}</td>
                          <td class='text-center'>
                              {!! Form::Open(['url'=>'sales/installment/remove-cart/edit/'.$row->id.'/'.$getid]) !!}
                                 <button class="btn btn-danger" @if($key == 0) disabled @endif><i class="fa fa-trash"></i> حذف</button>
                              {!! Form::Close() !!}
                          </td>    

                      </tr>
                       <?php $total+= $row->price*$row->qty; ?>
                  @endforeach

                </tbody>
               </table>

            
            <div class="row col-md-6 form-group">
               <button id="calc_total" class="btn btn-success" type="button"><i class="fa fa-dollar"></i> أحسب إجمالي المشتريات</button>
               <br/><br/><a href="{{ url('sales/installment/invoice/'.$getid) }}" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> الفاتورة</a>
            </div>
            <div calss="col-md-6 form-group">
               <p style='font-size:18px;margin-top:25px;' id="total_amount">{{$total}} جنيه</p>
            </div>

            </div>

        </div>
 
      </div>
     </div>
   </div>







   <div class="col-md-4">
      <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">بحث عن المنتجات</h3>
            </div>
      <div class="panel-body">

        <div class="col-md-12">

            <div class="input-group">
               <input type="text" class="form-control" placeholder="أدخل كود المنتج" name="pro_code" id="pro_code">
              <span class="input-group-btn">
                <button id="search_btn" class="btn btn-primary" ><i class="fa fa-search"></i></button>
              </span>
            </div>

            <br/><br/>
            <div id="search_loading" style='text-align:center;display:none;'><img src="{{ url('elixir/images/loading.gif') }}" style="width:30px;height:30px;"></div>
            <div id="search_error" style='text-align:center;display:none;'></div>
            <div id="search_result" class="form-group"  style="display:none;height:auto">
                <div class="col-md-12">
                     <img id="search_img" src="" style="width:100%;height:200px;border:1px solid #ddd">
                    <h5 id="search_proname"></h5>
                    <p>سعر <span  id="search_price"></span> <span style='font-size:11px;'>جنيه</span></p>
                    <p><label class="label label-success"> الكمية <span id="search_qty"></span></label> <input id="just_qty" readonly type='hidden' name='sub_qty'></p>
                  {!! Form::Open(['url'=>'sales/installment/add-to-cart/edit/'.$getid]) !!}  
                     <input type='hidden' name='procode' id="search_procode">
                     <input type='hidden' name='price' id="search_pprice">
                    <p><button class="btn btn-danger"><i class="fa fa-plus-circle"></i> أضف الي  المشتريات</button></p>
                  {!! Form::Close() !!}
                </div>
            </div>


        </div>
 
      </div>
     </div>
   </div>










   <div class="col-md-12">
      <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">بيانات الفاتورة</h3>
            </div>
      
      <div class="panel-body">

      {!! Form::Open() !!}

        <div class="col-md-12">
           <div class="col-md-3 form-group">
              <label>الأسم</label>
              <input type="text" class="form-control" value="{{$rows->name}}" name="name" required>
           </div>

           <div class="col-md-3 form-group">
              <label>رقم البطاقة</label>
              <input type="text" class="form-control" value="{{$rows->national_id}}" name="national_id">
           </div>

           <div class="col-md-3 form-group">
              <label>رقم الهاتف</label>
              <input type="number" class="form-control" value="{{$rows->phone}}" name="phone">
           </div>

           <div class="col-md-3 form-group">
              <label>العنوان</label>
              <input type="text" class="form-control" value="{{$rows->address}}" name="address">
           </div>
        </div>  



        <div class="col-md-12" style="margin-top:20px;">

           <div class="col-md-3 form-group">
              <label>الإجمالي</label>
              <input type="number" step="any" id="total" min="0" style="padding:1px;padding-right:10px;" class="form-control" name="total" value="{{$rows->total}}" required>
           </div>

           <div class="col-md-3 form-group">
              <label>المبلغ المدفوع</label>
              <input type="number" step="any" id="paid" min="0" style="padding:1px;padding-right:10px;" onkeyup="calc_remain();" value="{{$rows->paid}}" class="form-control" name="paid" required>
           </div>

           <div class="col-md-3 form-group">
              <label>المبلغ المتبقي</label>
              <input type="number" step="any" id="remain" min="0" style="padding:1px;padding-right:10px;" class="form-control" name="remain" value="{{$rows->remain}}" required>
           </div>

           <div class="col-md-3 form-group">
              <label>إجمالي مدة القسط</label>
              <input type="number" step="any" min="0" style="padding:1px;padding-right:10px;" class="form-control" name="count" value="{{$rows->count}}" required>
           </div>
        </div>  

        <div class="col-md-12" style="margin-top:20px;">
           <div class="col-md-2 form-group">
              <label>نوع القسط</label>
              <select id="count_type"  class="form-control" name="per_type" style="padding:1px;">
                     <option value="1" @if($rows->each_type == 1) selected="selected" @endif>أيام</option>
                     <option value="2" @if($rows->each_type == 2) selected="selected" @endif>شهور</option>
              </select>
           </div>

           <div class="col-md-2 form-group">
              <label>مدة القسط</label>
              <select id="per_day"  class="form-control" name="per_day" style="padding:1px;">
                     <option value="1" @if($rows->each_inst == 1) selected="selected" @endif>1</option>
                     <option value="2" @if($rows->each_inst == 2) selected="selected" @endif>2</option>
                     <option value="3" @if($rows->each_inst == 3) selected="selected" @endif>3</option>
                     <option value="4" @if($rows->each_inst == 4) selected="selected" @endif>4</option>
                     <option value="5" @if($rows->each_inst == 5) selected="selected" @endif>5</option>
                     <option value="6" @if($rows->each_inst == 6) selected="selected" @endif>6</option>
                     <option value="7" @if($rows->each_inst == 7) selected="selected" @endif>7</option>
              </select>
              <select id="per_month" class="form-control" name="per_month" style="display:none;padding:1px;">
                     <option value="1" @if($rows->each_inst == 1) selected="selected" @endif>1</option>
                     <option value="2" @if($rows->each_inst == 2) selected="selected" @endif>2</option>
                     <option value="3" @if($rows->each_inst == 3) selected="selected" @endif>3</option>
                     <option value="4" @if($rows->each_inst == 4) selected="selected" @endif>4</option>
                     <option value="5" @if($rows->each_inst == 5) selected="selected" @endif>5</option>
                     <option value="6" @if($rows->each_inst == 6) selected="selected" @endif>6</option>
                     <option value="7" @if($rows->each_inst == 7) selected="selected" @endif>7</option>
                     <option value="8" @if($rows->each_inst == 8) selected="selected" @endif>8</option>
                     <option value="9" @if($rows->each_inst == 9) selected="selected" @endif>9</option>
                     <option value="10" @if($rows->each_inst == 10) selected="selected" @endif>10</option>
                     <option value="11" @if($rows->each_inst == 11) selected="selected" @endif>11</option>
                     <option value="12" @if($rows->each_inst == 12) selected="selected" @endif>12</option>
              </select>
           </div>

           <div class="col-md-2 form-group">
              <label>مبلغ القسط</label>
              <input type="number" step="any" min="0" style="padding:1px;padding-right:10px;" class="form-control" value="{{$rows->each_amount}}" name="inst_cost" required>
           </div>

           <div class="col-md-3 form-group">
              <label>تاريخ بدأ القسط</label>
              <input type="date" class="form-control" value="{{$rows->start_date}}" name="start" required>
           </div>

           <div class="col-md-3 form-group">
              <label>تاريخ إنتهاء القسط</label>
              <input type="date" class="form-control" value="{{$rows->end_date}}" name="end">
           </div>

           <div class="col-md-12 form-group">
              <label>ملاحظات مع الفاتورة</label>
              <textarea name="comment" rows="5" class="form-control">{{$rows->comment}}</textarea>
           </div>

        </div> 

        <div class="col-md-12" style="margin-top:30px;">
          <button class="btn btn-primary"><i class="fa fa-edit"></i> تعديل الفاتورة</button>
          <a href="{{ url('sales/installment/published') }}" class="btn btn-danger"> رجوع <i class="fa fa-undo"></i></a>
        </div>

    {!! Form::Close() !!}
    </div>

     </div>
   </div>















@if(1==0)
@foreach(json_decode($rows->others_id) as $key => $otr)
@foreach(App\Installment_other::where('id',$otr)->get() as $other)@endforeach
   <div class="col-md-12">
      <div class="panel panel-default">

            <div class="panel-heading">
              <h3 class="panel-title">مبلغ القسط ( {{$key+1}} ) </h3>
            </div>
      
       <div class="panel-body">
         {!! Form::Open(['url'=>'sales/installment/update/inst/'.$other->id]) !!}
		 <input type="hidden" name="_token" value="{{ csrf_token() }}">
      
          <div class="col-md-4 col-md-offset-2 form-group">
            <label>مبلغ القسط</label>
            <input type='number' style="padding:1px;padding-right:10px;" class="form-control" name="remain_amount" value="{{$other->amount}}">
          </div>

          <div class="col-md-4 form-group">
            <label>تاريخ القسط</label>
            <input type='date' class="form-control" @if($other->status == 0 && date('Y-m-d') >= $other->amount_date)  style='background:#ffe0e0;' @endif readonly value="{{$other->amount_date}}">
          </div>
		  
		  <div class="col-md-3 form-group">
            <label>المدفوع</label>
            <input type='number' step="any" style="padding:1px;padding-right:10px;" class="form-control" name="paid" value="{{$other->paid}}">
          </div>
		  <div class="col-md-3 form-group">
            <label>المتبقي</label>
            <input type='number' step="any" style="padding:1px;padding-right:10px;" class="form-control" name="remains" value="{{$other->remain}}">
          </div>
		  <div class="col-md-6 form-group">
            <label>ملاحظة</label>
            <input type='text' style="padding:1px;padding-right:10px;" class="form-control" name="note" value="{{$other->note}}">
          </div>
		  
		  
		
		  <div class="col-md-2 col-md-offset-3" style="margin-top:20px;">
		    @if($other->paid != $other->amount)  
             <button class="btn btn-primary"><i class="fa fa-edit"></i> تعديل مبلغ القسط</button>&nbsp;&nbsp;&nbsp;&nbsp;
		    @endif
          </div>
		
		  
		  
      @if($other->status == 1)
          <div class="col-md-4" style="margin-top:20px;">
             &nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-success" type="button"><i class="fa fa-check-circle"></i> تم دفع مبلغ القسط ( {{$key+1}} ) بنجاح  </button>
          </div>
      @else
          <div class="col-md-4" style="margin-top:20px;">
             &nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ url('sales/installment/pay/'.$otr.'/'.$rows->id) }}" class="btn btn-warning"><i class="fa fa-question-circle"></i>   تأكيد دفع مبلغ القسط بالكامل  ( {{$key+1}} ) </a>
          </div>
      @endif
	  

        {!! Form::Close() !!}
       </div>

     </div>
   </div>
@endforeach
@endif

  <div class="col-md-12"><br/><br/><br/><br/></div>


  </div>
</div>
</section>
@stop


@section('jsCode')

<script>

$("#count_type").change(function(){
    var value = $("#count_type").val();
    if(value == 1) {
      $("#per_month").css({"display":"none"});
      $("#per_day").css({"display":"block"});
    } else {
       $("#per_day").css({"display":"none"});
       $("#per_month").css({"display":"block"});
    }

})

function calc_remain(){
   var total = $("#total").val();
   var paid = $("#paid").val();
   var remain = total-paid;
   $("#remain").val(remain);
}

$("#calc_total").on('click',function(){
  var Url = '{{ url("sales/installment/calc-total-cart/edit/") }}';

  $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

$.ajax({
        url : Url,
        type : "POST",
        dataType : "json",
        success : function(data){
            if(data.state == true){
                 $("#total_amount").css({"display":"block"});
                 $("#total_amount").text(data.total+' جنيه');
                 $("#just_amount").text(data.total);
            }
        }
    });
    return false;
    
  });






$('#search_btn').on('click', function(){
   var Url = '{{ url("sales/installment/search-pro") }}';
   var val = $("#pro_code").val();

   $("#total_amount").css({"display":"none"});

   if(!val) 
   {
      $("#pro_code").css({"border":"1px solid red"});

   } else {
    $("#pro_code").css({"border":"1px solid #ccc"});
    $("#search_loading").css({"display":"block"});
    $("#search_result").css({"display":"none"});
    $("#search_error").css({"display":"none"});
   
  $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

   $.ajax({
        url : Url,
        type : "POST",
        data : {val,val},
        dataType : "json",
        success : function(data){
          $("#search_loading").css({"display":"none"});
            if(data.state == true){
                 $("#search_result").css({"display":"block"});
                 $("#search_error").css({"display":"none"});
                 $("#search_procode").val(data.procode);
                 $("#search_img").attr("src", data.image);
                 $("#search_proname").text(data.proname)
                 $("#search_price").text(data.price);
                 $("#search_pprice").val(data.price);
                 $("#search_qty").text(data.qty);
                 $("#just_qty").val(data.qty);
            } else {
                 $("#search_result").css({"display":"none"});
                 $("#search_error").css({"display":"block"});
                 $("#search_error").text("لا يوجد منتجات");
          }
        }
    });
    return false;
    }
  });

</script>



@foreach($data as $jscode)
   @foreach(App\Product::where('pro_code',$jscode->pro_code)->get() as $jsfun)
<script>
$(document).ready(function(){
   var unit_price = $("#unit_price_{{$jsfun->id}}").text();
   var qty = $("#qty_{{$jsfun->id}}").val();
  $("#total_price_{{$jsfun->id}}").text(unit_price*qty); 
});

function submit_qty_{{$jsfun->id}}(id){
  $("#total_amount").css({"display":"none"});
  var unit_price = $("#unit_price_{{$jsfun->id}}").text();
  var qty = $("#qty_{{$jsfun->id}}").val();
  $("#total_price_{{$jsfun->id}}").text(unit_price*qty);
  
  var value = $("#qty_{{$jsfun->id}}").val();
  var Url = "{{ url('sales/installment/update-qty/edit/') }}/"+id+'/'+value+'';
  
  $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
        url : Url,
        type : "GET",
        dataType : "json",
        success : function(data){
        }
    }); 
return false;
};

</script>
   @endforeach
@endforeach


@stop