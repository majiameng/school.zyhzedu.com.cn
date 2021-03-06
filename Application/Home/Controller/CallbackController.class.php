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
        $id = I('get.id',0,'intval');
        $user_id = I('get.userid',0,'intval');
        if(empty($id) || empty($user_id)){
            $result['code'] = 400;
            $result['msg'] = '参数缺失';
            $this->jsonSuccess($result);
        }

        $where = array(
            'id'=>$id,
            'userid'=>$user_id,
        );
        $info = D('Resume')->where($where)->find();
        if(empty($info)) {
            $result['code'] = 400;
            $result['msg'] = '数据不存在';
            $this->jsonSuccess($result);
        }

        $data = [
            'is_pay' =>1,
        ];
        D('Resume')->where($where)->save($data);
        $result['mobile'] = $info['mobile'];
        $this->jsonSuccess($result);
    }

    public function jsonSuccess($result){
        echo json_encode($result);exit();
    }
   
}