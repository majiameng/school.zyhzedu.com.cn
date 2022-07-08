<?php
namespace Home\Controller;

class CallbackController extends PublicController {

    public function pay(){
        $user_id = I('get.user_id',0,'intval');
        var_dump($user_id);

        $info = D('Resume')->where(array('user_id'=>$user_id))->find();
        var_dump($info);
        if(!empty($info)) {

        }

        var_dump(111);
        die;
    }
   
}