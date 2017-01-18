<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //加载添加表单
    public function getAdd(){
    	return view('user.add');
    
}
	public function postInsert(Request $request){
		//1.验证表单数据
		$this->validate($request,[
			'name'=>'required',
			'username'=>'required',
			'repass'=>'same:pass|required',
			'email'=>'required|email',
			'email'=>'required',
			'phone'=>'required|digits:11'
			],[
			'name.required'=>'姓名必须填写',
			'username.required'=>'账号必须填写',
			'repass.same'=>'两次密码不一致',
			'repass.required'=>'密码必须填写',
			'email.required'=>'邮箱必须填写',
			'email.email'=>'邮箱格式不正确',
			'phone.required'=>'手机号必须填写',
			'phone.digits:11'=>'手机号必须为十一位'
			]);
		// echo '执行添加';
		//2.数据插入
		$data = $request->except(['_token','repass']);
		// dd($data);
		//数据处理
		$data['pass'] = Hash::make($data['pass']);//Hash::check()
		// dd($data);
		$data['token'] = str_random(50);//注册的身份验证 邮箱
		$data['status']=0;// 0禁用 1启用
		//数据插入
		// dd($data);
		$res = DB::table('user')->insert($data);
		if($res){
			return redirect('/admin/user/index')->with('success','添加成功');
			// echo '添加成功,跳转到用户浏览页面';
		}else{
			return back()->withInput();
		}
		// dd($data);
	}
	public function getIndex(Request $request){
		//查询数据
		$data = DB::table('user')->where(function($query) use($request){//use给当前匿名函数引入外部的$request变量
			if($request->input('keyword')!=null){//$query 就是数据库user的模型
				$query->where('name','like','%'.$request->input('keyword').'%')
				->orWhere('email','like','%'.$request->input('keyword').'%');
			}
		})->paginate($request->input('num',5));
		return view('user.index',['list'=>$data,'request'=>$request->all()]);
		
	}
	public function getDel($id){
		$res = DB::table('user')->where('id',$id)->delete();
		if($res){
			return redirect('/admin/user/index')->with('success','删除成功');
		}else{
			return back()->with('error','删除失败');
		}
	}
}
