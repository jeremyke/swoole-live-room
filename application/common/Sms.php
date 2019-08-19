<?php
namespace app\common;

class Sms
{
    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    function juheCurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

    //发送短信
    function sendSms($mobile,$code)
    {
        $url = "http://v.juhe.cn/sms/send";
        $params = array(
            'key'       => 'bf586c9f7373a3bd4bc39728009ff5f6', //您申请的APPKEY
            'mobile'    => $mobile, //接受短信的用户手机号码
            'tpl_id'    => '123278', //您申请的短信模板ID，根据实际情况修改
            'tpl_value' => '#code#='.$code.'&#company#=腾浪' //您设置的模板变量，根据实际情况修改
        );
        $paramstring = http_build_query($params);
        $content = $this->juheCurl($url, $paramstring);
        $result = json_decode($content, true);
        //var_dump($result);
        $return_res = [];
        if ($result) {
            if($result['error_code']==0){//发送成功
                $return_res['success'] = true;
                $return_res['msg'] = "发送成功";
                $return_res['data'] = [];
            }else{
                $return_res['success'] = false;
                $return_res['msg'] = $result['reason'];
                $return_res['data'] = [];
            }
        } else {
            $return_res['success'] = false;
            $return_res['msg'] = "发送失败";
            $return_res['data'] = [];
        }
        return $return_res;
    }

}