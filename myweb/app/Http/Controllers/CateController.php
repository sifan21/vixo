<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CateController extends Controller
{
    public function getAdd($id=''){//默认值的处理
    	// dd($id);
    	//select *,concat(path,id) as paths from cate order by paths
    	$cate = self::getCates();
    	return view('cate.add',['list'=>$cate,'id'=>$id]);

    }
    //获取格式化类别的数据
    public static function getCates(){
    	$cate = DB::table('cate')->select('*',DB::raw('concat(path,id) as paths'))->orderBy('paths')->get();
    	//修改类别样式 |---
    	foreach($cate as $k=>$v){
    		$num = count(explode(',',$v['path']))-2;
    		$cate[$k]['cate'] = str_repeat('|---',$num).$v['cate'];
    	}
    	// dd($cate);
    	return $cate;
    	// dd($cate);
    }
   
    public function postInsert(Request $request){
    	if($request->input('id')==0){
    		//添加顶级类
    		$data['cate']=$request->input('cate');
    		$data['pid']=0;
    		$data['path']='0,';
    	}else{
    		// 添加某一个类下面的子类
    		$data['cate']=$request->input('cate');
    		$data['pid']=$request->input('id');//子类的pid父类的id
    		$path= DB::table('cate')->where('id',$request->input('id'))->first()['path'];
    		$data['path']=$path.$request->input('id').',';//父类pat 父类的id
    	}
    	// dd($data);
    	$res = DB::table('cate')->insert($data);
    	// dd($res);
    	if($res){
    		// dd();
    		return redirect('/admin/cate/index')->with('success','添加成功');
    	}else{
    		return back()->with('error','添加失败');
    	}
    }
    public function getIndex(){
    	return view('cate.index',['list'=>self::getCates()]);
    }
    public static function funame($pid){
    	$funame=DB::table('cate')->where('id',$pid)->first()['cate'];
    	echo empty($funame)?'顶级分类':$funame;
    } 
    public function getDel($id){
    	//如果给分类有子类不能删除 只能删除没有子类的分类
    	$data = DB::table('cate')->where('pid',$id)->get();
    	if(count($data)>0){
    		return back()->with('error','该类别下有子类不能被直接删除');
    	}else{
    		//没有子类
    		$res = DB::table('cate')->where('id',$id)->delete();
    		if($res){
    			return redirect('/admin/cate/index')->with('success','删除成功');
    		}else{
    			return back()->with('error','删除失败');
    		}
    	}
    }
    public function getEdit($id){
    	//根据子类的id查询父类的名称
    	//左连接 有链接 内连 自连接
    	//select c1.*,c2.cate as funame from cate as c1 ,cate as c2 where c1.pid=c2.id and c1.id=12;
    	$funame = DB::table('cate as c1')
			    		->join('cate as c2','c1.pid','=','c2.id')
			    		->select('c2.cate as funame')
			    		->where('c1.id',$id)
			    		->first()['funame'];
    		$funame = empty($funame)?"顶级类":$funame;
    		// dd($data);
    	return view('cate.edit',[
    		'vo'=>DB::table('cate')->where('id',$id)->first(),
    		'funame'=>$funame
    		]);
    	}
    public function postUpdate(Request $request){
    	if(DB::table('cate')->where('id',$request->input('id'))->update($request->only('cate'))){
    		return redirect('/admin/cate/index')->with('success','修改成功');
    	}else{
    		return back()->with('error','修改失败');
    	}
    }
}

