<?php

use RadishesFlight\Pay\ZhDg\Cmb;

require 'vendor/autoload.php';

# 签名方式为：SM3withSM2。（新增对接的API调用，原则上不再新增RSA非对称算法）
# API接口地址（此API接口需要与以上配置的appid，完成API订阅）
# 示例3，接口类型：（1）加验签：标准模式-非对称签名认证-带Body摘要；（2）加解密：无加解密；（3）算法：SM2国密算法密钥
#$apiUrl = "/openapi/demo3/backend/post-query";
$apiUrl = 'https://api.cmbchina.com/qyyh/cbl-v2.0/order-pay';

$requestBodyJson = [
    "head" => [
        "version" => "1.0",
        "datetime" => date("Y-m-d H:i:s"),
    ],
    "body" => [
        "orderInfoPayDto" => [
            "appId" => '9fb64fe9-ed22-459b-a2dc-dbd78c73cf8e',
            "uniPltNbr" => "30899915122002B",
            "uniMchNbr" => "30899915122002B",
            "mchOrderNbr" => "MchOrderNbr100000000011111111".mt_rand(1, 9999999999),
            "mchTransNbr" => "MchTransNbr101111111111121211".mt_rand(1, 9999999999),
            "orderType" => "PAY",
            "orderSubtype" => "DRT",
            "orderAmt" => "89.99",
            "orderCcyNbr" => "10",
            "orderPostTime" => date('YmdHis'),
            "urlTimeLimit" => "",
            "orderValidateTime" => "",
            "payerIssrId" => "",
            "pltRemark" => "pltRemark",
            "orderRemark" => "orderRemark",
            "frontNtfUrl" => "http://www.cmbchina.com/",
            "asyncNtfUrl" => "http://www.cmbchina.com/",
        ],
        "orderExtendPayDto" => [
            "transTerminalType" => "07",
            "transChannelType" => "01",
            "transIp" => "99.11.8.56",
        ],
    ],
];

$response = (new Cmb([
    "host" => "https://api.cmbchina.com/qyyh/cbl-v2.0/order-pay",//招行地址
    "appid" => "9fb64fe9-ed22-459b-a2dc-dbd78c73cf8e",//appid
    "secret" => "ace2af0d-4cb9-45dd-9f65-0c82b5d2926d",//secret
    "private_key" => "8FB09DC17A620E0D62F3ECF58E45C291F21F09B79701963CA6E34BC5AD0EB62D",//商户私钥
    "public_key" => "04C1FDD6390FDCA16A5D85D64EFCDA8568465FDBA8F08B5C3D0B970635C455367FF2B61562172C194EB2BCBC4C90AEA2B7E85CC4760A3907D6F760BC8681E95176",//招行公钥
    "merch_id" => "30899915122002B",//商户号
    "user_id" => "1234567812345678",//固定的user_id 无需更改
]))->post($requestBodyJson, $apiUrl);
print_r($response);
exit();

