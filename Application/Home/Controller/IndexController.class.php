<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends PublicController {
    public function index(){
    	//读取新闻
		$this->display();

    }
   
}