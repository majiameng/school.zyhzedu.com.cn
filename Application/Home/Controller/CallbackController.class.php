<?php
namespace Home\Controller;

class CallbackController extends PublicController {

    /**
     * 支付回调
     * @return void
     */
    public function pay(){
        $result = [
            'code' => 200,
            'msg' => ''
        ];
        $user_id = I('get.user_id',0,'intval');
        if(empty($user_id)){
            $result['code'] = 400;
            $result['msg'] = '参数缺失';
            $this->jsonSuccess($result);
        }

        $where = array('user_id'=>$user_id);
        $info = D('Resume')->where($where)->find();
        if(empty($info)) {
            $result['code'] = 400;
            $result['msg'] = '数据不存在';
            $this->jsonSuccess($result);
        }

        $where = array('user_id'=>$user_id);
        $data = [
            'is_pay' =>1,
        ];
        $res = D('Resume')->where($where)->save($data);
        var_dump($res);die;
        $this->jsonSuccess($result);
    }

    public function jsonSuccess($result){
        echo json_encode($result);exit();
    }
   
}