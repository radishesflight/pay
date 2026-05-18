<?php

namespace RadishesFlight\Pay\TongLian;

use Exception;

class HttpUtil
{
    /**
     * 发送GET请求
     * @param string $url 请求地址
     * @param array $params 请求参数（将拼接在URL后）
     * @return array 响应结果
     */
    public static function get(string $url, array $params): array {
        // 构建带参数的URL
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 对账单可能较大，设置较长超时时间
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/plain, */*',
            'User-Agent: Allinpay-PHP-SDK'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("CURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: " . $httpCode);
        }

        // 对账单接口返回的是纯文本格式，不是JSON
        return [
            'data' => $response,
            'http_code' => $httpCode
        ];
    }
}