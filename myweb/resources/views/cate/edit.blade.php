@extends('layout.adminindex')
@section('con')
<div class="mws-panel grid_8">
<div class="mws-panel-header">
<span>分类修改</span>
</div>
<div class="mws-panel-body no-padding">

<form action="/admin/cate/update" class="mws-form" method="post">
{{csrf_field()}}
<!--添加隐藏域传递用户的id-->
<input type="hidden" name="id" value='{{$vo['id']}}'>
     <div class="mws-form-inline">
          <div class="mws-form-row">
               <label class="mws-form-label">父分类</label>
               <div class="mws-form-item">
                    <input type="text" class="small" value='{{$funame}}' readonly name="cate" >
               </div>
          </div>
          <div class="mws-form-row">
               <label class="mws-form-label">子分类</label>
               <div class="mws-form-item">
                    <input type="text" class="small" name="cate" value="{{$vo['cate']}}">
               </div>
          </div>
     </div>
	<div class="mws-button-row">
		<input type="submit" value="添加" class="btn btn-danger">
		<input type="reset" value="重置" class="btn ">
	</div>
</form>
</div>    	
</div>
@endsection