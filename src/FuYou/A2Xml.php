<?php

namespace RadishesFlight\Pay\FuYou;
use XMLWriter;

class A2Xml
{
    private $xml = null;

   public function __construct()
    {
        $this->xml = new XmlWriter();
    }

    //数组转xml
    public function toXml($data, $eIsArray = FALSE)
    {
        if (!$eIsArray) {
            $this->xml->openMemory();
        }
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->xml->startElement($key);
                $this->toXml($value, TRUE);
                $this->xml->endElement();
                continue;
            }
            $this->xml->writeElement($key, $value);
        }
        if (!$eIsArray) {
            $this->xml->endElement();
            return $this->xml->outputMemory(true);
        }
    }

    //签名加密流程
public function sign($data,$pemPath)
    {
        //读取密钥文件
        $pem = file_get_contents($pemPath);
        //获取私钥
        $pkeyid = openssl_pkey_get_private($pem);
        //MD5WithRSA私钥加密
        openssl_sign($data, $sign, $pkeyid, OPENSSL_ALGO_MD5);
        //返回base64加密之后的数据
        $t = base64_encode($sign);
        //解密-1:error验证错误 1:correct验证成功 0:incorrect验证失败
        // $pubkey = openssl_pkey_get_public($pem);
        // $ok = openssl_verify($data,base64_decode($t),$pubkey,OPENSSL_ALGO_MD5);
        // var_dump($ok);
        return $t;
    }

    //通过curl模拟post的请求；
    public  function SendDataByCurl($url, $data)
    {
        //对空格进行转义
        $url = str_replace(' ', '+', $url);
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //定义超时3秒钟
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //所需传的数组用http_bulid_query()函数处理一下，就ok了
        //执行并获取url地址的内容
        $output = curl_exec($ch);
        $errorCode = curl_errno($ch);
        //释放curl句柄
        curl_close($ch);
        if (0 !== $errorCode) {
            return false;
        }
        return $output;
    }

}
