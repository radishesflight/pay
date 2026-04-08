<?php

namespace RadishesFlight\Pay\TongLian;

use Exception;
use GuzzleHttp\Client;

class TlPay
{

    private $config;
    public $data;

    public function __construct(array $config)
    {
        $this->config = $config;
    }


    public function setRefundParams($filed, $value)
    {
        $this->data[$filed] = $value;
    }

    public function pay()
    {
        // 构建请求参数
        $params = [
            'cusid' => $this->config['cusid'],
            'appid' => $this->config['appid'],
            'version' => $this->config['version'],
            'randomstr' => $this->generateRandomString(32), // 随机字符串
            'signtype' => 'RSA',             // 签名类型
        ];
        $params = array_merge($params, $this->data);

        // 生成签名
        $params['sign'] = SignUtil::sign($params, $this->config['private_key']);

        // 发送GET请求获取对账单
        $url = $this->config['api_host'] . '/apiweb/unitorder/pay';
        $client = new Client(['timeout' => 3]);
        $rsp = $client->post($url, [
            'form_params' => $params,
            'verify' => false
        ])->getBody();
        $rsp = (string)$rsp;
        return $rsp ? json_decode($rsp, true) : [];
    }

    /**
     * 退款
     * @return string
     * @throws Exception
     */
    public function getRefund(): string
    {
        // 构建请求参数
        $params = [
            'cusid' => $this->config['cusid'],
            'appid' => $this->config['appid'],
            'version' => $this->config['version'],
            'randomstr' => $this->generateRandomString(32), // 随机字符串
            'signtype' => 'RSA',             // 签名类型
        ];
        $params = array_merge($params, $this->data);

        // 生成签名
        $params['sign'] = SignUtil::sign($params, $this->config['private_key']);

        // 发送GET请求获取对账单
        $url = $this->config['api_host'] . '/apiweb/tranx/refund';
        $response = HttpUtil::get($url, $params);

        return $response['data'];
    }

    /**
     * 获取对账单
     * @param string $billDate 对账单日期，格式：YYYYMMDD
     * @param int $billType 对账单类型，BILL:交易对账单，REFUND:退款对账单
     * @return string 对账单内容
     * @throws Exception
     */
    public function getReconciliationBill(string $billDate, int $billType = 1): string
    {
        // 参数验证
        if (!preg_match('/^\d{8}$/', $billDate)) {
            throw new Exception("对账单日期格式错误，应为YYYYMMDD格式");
        }

        if (!in_array($billType, [1, 2, 3, 4, 5])) {
            throw new Exception("对账单类型错误");
        }

        // 构建请求参数
        $params = [
            'cusid' => $this->config['cusid'],
            'appid' => $this->config['appid'],
            'version' => $this->config['version'],
            'date' => $billDate,        // 对账单日期
            'filetype' => $billType,        // 对账单类型
            'randomstr' => $this->generateRandomString(32), // 随机字符串
            'signtype' => 'RSA',             // 签名类型
        ];

        // 生成签名
        $params['sign'] = SignUtil::sign($params, $this->config['private_key']);

        // 发送GET请求获取对账单
        $url = $this->config['api_host'] . '/cusapi/trxfile/get';
        $response = HttpUtil::get($url, $params);

        return $response['data'];
    }

    /**
     * 生成随机字符串
     * @param int $length 长度
     * @return string 随机字符串
     */
    private function generateRandomString(int $length = 32): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
}