<?php
namespace Admin2\Model;
use Think\Model;

class ArticleCategoryModel extends Model{
	protected $tableName = 'article_cat';//表名可以和类名不一样，这里可以指定表名
    protected $_validate=array(
        array('name','require','分类名称不能为空',1,'regex',3),
        array('name','','分类名称已经存在！',0,'unique',1),
    );
    
    /**
     * 查询指定id的下一级分类，默认查询所有顶级分类
     * 暂时未使用
     */
	public function getCate($parent_id=0){
	
        $cat = $this->where('parent_id='.$parent_id)->select();
        return $cat;
		
    }
	


    /**
     * 查询三级分类
     * 暂时没用
     * @return 三级分类的数组
     */
   /* public function getAllCate(){
        $cat = $this->where('parent_id=0')->select();
        foreach($cat as $k=>$v){
            $cat[$k]['son'] = $category->where('parent_id='.$v['id'])->select();
            foreach($cat[$k]['son'] as $k1=>$v1){
            	$cat[$k]['son'][$k1]['sonson'] = $category->where('parent_id='.$v1['id'])->select();
            }
        }

        //print_r($cat);exit;
        return $cat;
    }*/
    
    
    //递归读取分类 得到下拉列表 $current表示哪个被默认选中
    //该方法在商品管理里面使用
	 function getTree($parent_id=0,$current=0,$tag='├',$html=''){
	         $child = $this->where('parent_id='.$parent_id)->select();
	         foreach($child as $v){
	         	$tag1 = ($parent_id>0) ? $tag : '';
			 if($v['id'] == $current){
			   $html.='<option value="'.$v['id'].'" selected="selected">'.$tag1.$v['name'].'</option>';
			 }else{
			   $html.='<option value="'.$v['id'].'">'.$tag1.$v['name'].'</option>';
			 }
			 $html.=$this->getTree($v['id'],$current,'&nbsp;&nbsp;&nbsp;'.$tag);
			 }
	         return $html; 
	 }
	
	
	
	
	
    
    //得到列表需要的分类
    //在分类管理列表使用
    function getAlltable($parent_id=0,$tag = '┗',$thml = ''){
    	$child = $this ->where('parent_id='.$parent_id)-> select();
    	$show = array('否','是');
    	foreach($child as $v){
    		$tag1 = ($parent_id>0) ? $tag : '';
    		$html.= '<tr>
                                <td>'.$v['id'].'</td>
                                <td align="left" style="text-align:left;padding-left:30px;">'.$tag1.$v['name'].'</td>
                                <td>'.$v['sort'].'</td>
                                <td>'.$show[$v['show']].'</td>
                                <td>
                                    <a href="'.U('ArticleCate/edit',array('id'=>$v['id'])).'" class="btn btn-primary btn-mini">修改</a>
                                    <a href="'.U('ArticleCate/del',array('id'=>$v['id'])).'" class="btn btn-danger btn-mini del">删除</a>
                                </td>
                            </tr>';

    		$html.= $this->getAlltable($v['id'],'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$tag);
    	}
    	return $html;
    }





}