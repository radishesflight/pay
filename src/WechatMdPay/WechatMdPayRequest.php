<?php

namespace RadishesFlight\Pay\WechatMdPay;

use app\Common\BasisCommon;

class WechatMdPayRequest
{
    public static function login($code, $wechatConfig)
    {
        $body = [
            'appid' => $wechatConfig['appid'],
            'secret' => $wechatConfig['secret'],
            'grant_type' => 'authorization_code',
            'js_code' => $code,
        ];
        $url = $wechatConfig['host'] . $wechatConfig['url']['login'] . '?' . http_build_query($body);
        return self::curlGet($url);
    }

    public static function curlGet($url, $header = [])
    {
        $headerArray = array_merge(array("Content-type:application/json;charset='utf-8'", "Accept:application/json"), $header);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output ? json_decode($output, true) : [];
    }

    public static function calc_pay_sig($uri, $post_body, $appkey)
    {
        $need_sign_msg = $uri . '&' . $post_body;
        return hash_hmac('sha256', $need_sign_msg, $appkey, false);
    }

    public static function calc_signature($post_body, $session_key)
    {
        return hash_hmac('sha256', $post_body, $session_key, false);
    }
}