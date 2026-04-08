<?php

namespace RadishesFlight\Pay\Hwc;

class PayHttpClient
{

    //请求内容
    //var $reqContent;
    public $url;
    public $data;
    //应答内容
    public $resContent;

    //错误信息
    public $errInfo;

    //超时时间
    public $timeOut;

    //http状态码
    public $responseCode;

    public function __construct()
    {
        $this->PayHttpClient();
    }


    public function PayHttpClient()
    {
        //$this->reqContent = "";
        $this->url = "";
        $this->data = "";
        $this->resContent = "";

        $this->errInfo = "";

        $this->timeOut = 120;

        $this->responseCode = 0;

    }

    //设置请求内容
    public function setReqContent($url, $data)
    {
        $this->url = $url;
        $this->data = $data;
    }

    //获取结果内容
    public function getResContent()
    {
        return $this->resContent;
    }

    //获取错误信息
    public function getErrInfo()
    {
        return $this->errInfo;
    }

    //设置超时时间,单位秒
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
    }

    //执行http调用
    public function call()
    {
        //启动一个CURL会话
        $ch = curl_init();

        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);

        // 执行操作
        $res = curl_exec($ch);
        $this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($res == NULL) {
            $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch);
            curl_close($ch);
            return false;
        } else if ($this->responseCode != "200") {
            $this->errInfo = "call http err httpcode=" . $this->responseCode;
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        $this->resContent = $res;


        return true;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }
}