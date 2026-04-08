<?php

namespace RadishesFlight\Pay\TongLian;

class SignUtil
{
    /**
     * 生成签名
     * @param array $params 待签名参数
     * @param string $privateKey 私钥
     * @return string 签名结果
     */
    public static function sign(array $params, string $privateKey): string {
        // 移除空值参数和数组参数
        $params = array_filter($params, function($v) {
            return $v !== '' && !is_array($v);
        });

        // 按键名升序排序
        ksort($params);

        // 拼接参数字符串
        $signStr = self::toUrlParams($params);

        // 格式化私钥
        $privateKey = chunk_split($privateKey, 64, "\n");
        $key = "-----BEGIN RSA PRIVATE KEY-----\n" . $privateKey . "-----END RSA PRIVATE KEY-----";

        // 生成签名
        openssl_sign($signStr, $signature, $key, OPENSSL_ALGO_SHA1);

        // Base64编码
        return base64_encode($signature);
    }

    /**
     * 将参数拼接为URL格式
     * @param array $params 参数数组
     * @return string 拼接结果
     */
    private static function toUrlParams(array $params): string {
        $buff = "";
        foreach ($params as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}