@extends('backend.layouts.layout')
@section('content')

<section id="min-wrapper">
  <div id="main-content">
    <div class="container-fluid">
       <div class="row">
          <div class="col-md-12">
          <h3 class="ls-top-header">الموردين</h3>

@if(Session::has('success'))
 <p class="alert alert-success">{{Session::get('success')}}</p>
@elseif(Session::has('error'))
 <p class="alert alert-danger">{{Session::get('error')}}</p>
@endif

  
  {!! Form::Open() !!}
   <div class="col-md-12">
      <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">تعديل المورد {{$row->name}}</h3>
            </div>
      <div class="panel-body">


           <div class="col-md-4 col-md-offset-4 form-group">
             <label>الإجمالي</label>
             <input id="total" type="number" step="any" min="0" style="padding:1px;padding-right:10px;"  class="form-control" name="total" value="{{$row->total}}">
           </div>
           <div class="col-md-12"></div>
           <div class="col-md-4 col-md-offset-2 form-group">
             <label>المدفوع</label>
             <input id="paid" type="number" step="any" min="0" style="padding:1px;padding-right:10px;"  class="form-control" name="paid" value="{{$row->paid}}">
           </div>
           <div class="col-md-4 form-group">
             <label>المتبقي</label>
             <input id="remain" type="number" step="any" min="0" style="padding:1px;padding-right:10px;"  class="form-control" name="remain" value="{{$row->remain}}">
           </div>
           <div class="col-md-12"></div>
           <div class="col-md-12 row form-group">
             <button class="btn btn-primary"><i class="fa fa-check-circle"></i> حفظ</button>
             <a href="{{ url('moward') }}" class="btn btn-danger"> رجوع <i class="fa fa-undo"></i></a>
           </div>
      </div>
     </div>
   </div>

 {!! Form::Close() !!}



   <div class="col-md-12">
      <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">جميع الدفعات</h3>
            </div>

      <div class="panel-body">
      <a href="{{ url('moward/'.$row->id.'/new/row') }}" class="btn btn-success"><i class="fa fa-plus-circle"></i> أضف دفعة جديد </a>

          <a href="{{ url('moward/'.$row->id.'/new/fatora') }}" class="btn btn-warning"><i class="fa fa-plus-circle"></i> أضف فاتورة جديد </a>
    

  @foreach(App\Mward_detail::where('mward_id',$row->id)->get() as $rom)
    {!! Form::Open(['url'=>'moward/'.$rom->id.'/update/row']) !!}
      <div class="col-md-12"><hr> <label style="color: #0000cc"><h2>دفعة</h2></label></div>
      <div class="col-md-3 row form-group">
         <label>التاريخ</label>
         <input type="date" name="date" class="form-control" value="{{$rom->inv_date}}" required @if($rom->paid != 0) readonly @endif>  
      </div>
      <div class="col-md-3 form-group">
         <label>المدفوع</label>
         <input type="number" step="any" min="0" style="padding:1px;padding-right:10px;" name="paid" class="form-control" value="{{$rom->paid}}" required @if($rom->paid != 0) readonly @endif>  
      </div>
      <div class="col-md-4 form-group">
         <label>الملاحظة</label>
         <input type="text" name="note" class="form-control" value="{{$rom->note}}" @if($rom->paid != 0) readonly @endif>  
      </div>
   
    @if($rom->paid == 0)
      <div class="col-md-1 form-group">
         <label style="color:transparent">.<br/></label>
         <button class="btn btn-primary"><i class='fa fa-check-circle'></i> حفظ </button>
      </div>
    @endif

      <div class="col-md-1 form-group">
         <label style="color:transparent">.<br/></label>
         <a href="{{ url('moward/'.$rom->id.'/delete/row') }}" class="btn btn-danger"><i class='fa fa-trash'></i> حذف</a>
      </div>
      {!! Form::Close() !!}
  @endforeach

  @foreach(App\Mward_fatora::where('mward_id',$row->id)->get() as $rom)
    {!! Form::Open(['url'=>'moward/'.$rom->id.'/update/fatora']) !!}
      <div class="col-md-12"><hr>  <label style="color: #0000cc"><h2>فاتورة</h2></label></div>
      <div class="col-md-3 row form-group">
         <label>التاريخ</label>
         <input type="date" name="date" class="form-control" value="{{$rom->inv_date}}" required @if($rom->paid != 0) readonly @endif>
      </div>
      <div class="col-md-3 form-group">
         <label>المدفوع</label>
         <input type="number" step="any" min="0" style="padding:1px;padding-right:10px;" name="paid" class="form-control" value="{{$rom->paid}}" required @if($rom->paid != 0) readonly @endif>
      </div>
      <div class="col-md-4 form-group">
         <label>الملاحظة</label>
         <input type="text" name="note" class="form-control" value="{{$rom->note}}" @if($rom->paid != 0) readonly @endif>
      </div>

    @if($rom->paid == 0)
      <div class="col-md-1 form-group">
         <label style="color:transparent">.<br/></label>
         <button class="btn btn-primary"><i class='fa fa-check-circle'></i> حفظ </button>
      </div>
    @endif

      <div class="col-md-1 form-group">
         <label style="color:transparent">.<br/></label>
         <a href="{{ url('moward/'.$rom->id.'/delete/fatora') }}" class="btn btn-danger"><i class='fa fa-trash'></i> حذف</a>
      </div>
      {!! Form::Close() !!}
  @endforeach

      
      
      </div>

      </div>
  </div>





  </div>
</div>
</section>

@stop





@section('jsCode')

<script>

$("#paid").on('keyup',function(){
  var total = $("#total").val();
  var paid = $("#paid").val();
  var remain = total-paid;
  $("#remain").val(remain);
})

$("#total").on('keyup',function(){
  var total = $("#total").val();
  var paid = $("#paid").val();
  var remain = total-paid;
  $("#remain").val(remain);
})

document.querySelector('.submit').addEventListener("click", function(){
    window.btn_clicked = true;
});

window.onbeforeunload = function(){
    if(!window.btn_clicked){
        return "Seems Like you wanna leave ?";
    }
};

$('body').on('click', '.remove_inst', function () {
    var didConfirm = confirm("Are you sure you want to delete");
      if (didConfirm == true) {
        $(this).parent().parent().remove();
        return true;
      } else {
          return false;
      }  
});




</script>

@stop