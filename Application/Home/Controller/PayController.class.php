<?php
namespace Home\Controller;
class PayController extends PublicController {

    public $api_url = "http://payment.zyhzedu.com.cn/index.php";

    /**
     * 发送短信
     * http://hpyy2022.zyhzedu.com.cn/index.php?m=Home&c=pay&a=sms
     */
    public function sms(){
        //短信发送
        $url = $this->api_url."/Sms/index";
        $params = [
            'mobile'=>13146662737,
            'type'=>'template2',//定义的模版名字
        ];
        $result = $this->httpPost($url,$params);//{"code":200,"msg":"success","data":[]}
        $result = json_decode($result,true);
        if($result['code'] != 200){
            $this->error("短信发送失败：".$result['msg']);
        }
    }

    /**
     * 支付链接
     * http://hpyy2022.zyhzedu.com.cn/index.php?m=Home&c=Pay&a=pay
     */
    public function pay(){
        $url = $this->api_url."/UnionPay/index";
        $params = [
            'user_id'=>1,
            'total_amount'=>1,
        ];
        $result = $this->httpPost($url,$params);
        $result = json_decode($result,true);
        if($result['code']!=200){
            $this->error("调用支付系统出错：".$result['msg']);
        }

        $qrUrl = $this->api_url."/UnionPay/qrCode?data=".urlencode($result['data']['qr_code']);
        var_dump($qrUrl);
        die;
    }

    /**
     * Description: POST 请求
     * Author: JiaMeng <666@majiameng.com>
     * @param string $url 请求链接
     * @param array $param 请求参数
     * @param array $httpHeaders 添加请求头
     * @param string $proxy 代理ip
     * @param int $http_code 相应正确的状态码
     * @return mixed
     * @throws \Exception
     */
    private function httpPost($url, $param = array(), $httpHeaders = array(),$proxy='', $http_code = 200)
    {
        /** 参数检测,object或者array进行http_build_query */
        if(!empty($param) && is_array($param)){
            $flag = false;
            foreach ($param as $value){
                //判断参数是否是一个对象 或者 是一个数组
                if(is_array($value) || (is_string($value) && is_object($value))){
                    $flag = true;
                    break;
                }
            }
            if($flag == true){
                $param = http_build_query($param);
            }
        }

        $curl = curl_init();

        /** 设置请求链接 */
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        /** 设置请求参数 */
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);

        /** 设置请求headers */
        if(empty($httpHeaders)){
            $httpHeaders = array(
                "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36"
            );
        }
        if (is_array($httpHeaders)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        /** gzip压缩 */
        curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, "gzip,deflate");

        /** 不验证https证书和hosts */
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        }

        /** http代理 */
        if(!empty($proxy)){
            $proxy = explode(':',$proxy);
            curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
            curl_setopt($curl, CURLOPT_PROXY, "$proxy[0]"); //代理服务器地址
            curl_setopt($curl, CURLOPT_PROXYPORT,$proxy[1]); //代理服务器端口
        }

        /** 请求 */
        $content = curl_exec($curl);
        /** 获取请求信息 */
        $info = curl_getinfo($curl);
        /** 关闭请求资源 */
        curl_close($curl);

        /** 验证网络请求状态 */
        return $content;
    }

}