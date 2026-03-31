<?php

use RadishesFlight\Pay\ZhDg\Cmb;
use think\Request;

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



//$requestBodyJson = [
//    "head" => [
//        "version" => "1.0",
//        "datetime" => date("Y-m-d H:i:s"),
//    ],
//    "body" => [
//        "orderInfoPayDto" => [
//            "appId" => $config['appid'],
//            "uniPltNbr" => $config['merch_id'],
//            "uniMchNbr" => $config['merch_id'],
//            "mchOrderNbr" =>  $mchOrderNbr,//商户订单号
//            "mchTransNbr" => $mchOrderNbr,//商户交易流水号
//            "orderType" => "PAY",
//            "orderSubtype" => "DRT",
//            "orderAmt" => $orderAmt,
//            "orderCcyNbr" => "10",
//            "orderPostTime" => date('YmdHis'),
//            "urlTimeLimit" => "5", //收银单 URL 跳转支付时限 单位分钟
//            "orderValidateTime" => "",
//            "payerIssrId" => "",
//            "pltRemark" => $data['orderNumber'],//平台备注
//            "orderRemark" => $data['orderNumber'],//订单备注
//            "frontNtfUrl" => $data['frontNtfUrl']??"",
//            "asyncNtfUrl" => $config['notify'],
//        ],
//        "orderExtendPayDto" => [
//            "transTerminalType" => $transTerminalType,
//            "transChannelType" => $transChannelType,
//            "transIp" => $this->getRealIp(),
//        ],
//    ],
//];
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


//验证签名
$data = $requestData['body'];
$header = $requestData['header'];

file_put_contents('cmbPay.log', json_encode(["header" => $header, "body" => $data], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES) . "\r\n", FILE_APPEND);
$payConfig = config("zhdg");

$sign = (new Cmb($payConfig))->verifySign(json_encode($data, JSON_UNESCAPED_UNICODE+ JSON_UNESCAPED_SLASHES), $header["cmb-bodysign"], $payConfig["public_key"]);
if (!$sign) {
    file_put_contents('sign.log', json_encode(["header" => $header, "body" => $data], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES) . "\r\n", FILE_APPEND);
    return json([
        "respCode" => "LX11C995",
        "respDesc" => "签名验证失败",
    ]);
}


//对账单下载
 function cmb(Request $request)
{
    $is_test= env('CMB.IS_TEST',false);
    if ($is_test){
        $config = config('zhdgCs');
    }else{
        $config = config('zhdg');
    }
    $billDate = $request->get('billDate', date('Ymd'));
    $list = [];
    $i = 1;
    while (true) {
        $pageNum = $request->get('pageNum', $i);
        $pageSize = $request->get('pageSize', 100);
        $requestBodyJson = [
            "head" => [
                "version" => "1.0",
                "datetime" => date("Y-m-d H:i:s"),
            ],
            "body" => [
                "billQryDto" => [
                    "appId" => $config['appid'],
                    "uniPltNbr" => $config['merch_id'],
                    "uniMchNbr" => $config['merch_id'],
                    "orderDate" => $billDate,//时间
                    "pageNum" => $pageNum,//
                    "pageSize" => $pageSize,
                ]
            ],
        ];
        $response = (new Cmb($config))->post($requestBodyJson, $config['host'] . $config['bill_info_url']);
        if (!empty($response['body']['billQryRtnDto'])) {
            $listOne = $response['body']['billQryRtnDto'];
            $list = array_merge($list, $listOne);
            $i++;
        } else {
            break;
        }
    }
    $PHPExcel = new \PHPExcel();
    $PHPSheet = $PHPExcel->getActiveSheet();
    $PHPSheet->setTitle("对账单"); //给当前活动sheet设置名称
    $PHPSheet->setCellValue("A1", "应用ID")
        ->setCellValue("B1", "订单号")
        ->setCellValue("C1", "订单交易序号")
        ->setCellValue("D1", "商户订单号")
        ->setCellValue("E1", "商户交易流水号")
        ->setCellValue("F1", "关联交易流水号")
        ->setCellValue("G1", "订单类型")
        ->setCellValue("H1", "订单子类")
        ->setCellValue("I1", "订单金额")
        ->setCellValue("J1", "订单币种")
        ->setCellValue("K1", "订单状态")
        ->setCellValue("L1", "请求结果")
        ->setCellValue("M1", "订单开始时间")
        ->setCellValue("N1", "订单完成时间")
        ->setCellValue("O1", "订单推送时间")
        ->setCellValue("P1", "订单无效时间")
        ->setCellValue("Q1", "订单无效标志")
        ->setCellValue("R1", "错误码")
        ->setCellValue("S1", "错误描述")
        ->setCellValue("T1", "对账状态")
        ->setCellValue("U1", "对账时间")
        ->setCellValue("V1", "对账金额")
        ->setCellValue("W1", "清算标志")
        ->setCellValue("X1", "清算时间")
        ->setCellValue("Y1", "清算金额")
        ->setCellValue("Z1", "清算对账失败原因")
        ->setCellValue("AA1", "清算模式")
        ->setCellValue("AB1", "确认标志")
        ->setCellValue("AC1", "确认金额")
        ->setCellValue("AD1", "退款标志")
        ->setCellValue("AE1", "退款金额")
        ->setCellValue("AF1", "关联订单号")
        ->setCellValue("AG1", "平台编号")
        ->setCellValue("AH1", "商户编号")
        ->setCellValue("AI1", "银联商户编号")
        ->setCellValue("AJ1", "网联商户编号")
        ->setCellValue("AK1", "统一平台商户编号")
        ->setCellValue("AL1", "统一收款商户编号")
        ->setCellValue("AM1", "商户简称")
        ->setCellValue("AN1", "平台备注")
        ->setCellValue("AO1", "订单备注")
        ->setCellValue("AP1", "付款方网关机构标识")
        ->setCellValue("AQ1", "付款方账户机构标识")
        ->setCellValue("AR1", "付款方账号")
        ->setCellValue("AS1", "付款方账户名")
        ->setCellValue("AT1", "付款方行内外标志")
        ->setCellValue("AU1", "付款方联行号")
        ->setCellValue("AV1", "付款方联行名")
        ->setCellValue("AW1", "收款方受理机构标识")
        ->setCellValue("AX1", "收款方账户机构标识")
        ->setCellValue("AY1", "收款方账号")
        ->setCellValue("AZ1", "收款方账户名")
        ->setCellValue("BA1", "收款方行内外标志")
        ->setCellValue("BB1", "收款方联行号")
        ->setCellValue("BC1", "收款方联行名")
        ->setCellValue("BD1", "二级商户编号")
        ->setCellValue("BE1", "二级商户类别")
        ->setCellValue("BF1", "二级商户名称");
    $i = 2;
    foreach ($list as $data) {
        $PHPSheet->setCellValue("A" . $i, " " . $data['appId'])
            ->setCellValue("B" . $i, $data['orderNbr'])
            ->setCellValue("C" . $i, $data['orderSubNbr'])
            ->setCellValue("D" . $i, $data['mchOrderNbr'])
            ->setCellValue("E" . $i, $data['mchTransNbr'])
            ->setCellValue("F" . $i, $data['rltTransNbr'])
            ->setCellValue("G" . $i, $data['orderType'])
            ->setCellValue("H" . $i, $data['orderSubtype'])
            ->setCellValue("I" . $i, $data['orderAmt'])
            ->setCellValue("J" . $i, $data['orderCcyNbr'])
            ->setCellValue("K" . $i, $data['status'])
            ->setCellValue("L" . $i, $data['rtnFlag'])
            ->setCellValue("M" . $i, $data['orderStrTime'])
            ->setCellValue("N" . $i, $data['orderEndTime'])
            ->setCellValue("O" . $i, $data['orderPostTime'])
            ->setCellValue("P" . $i, $data['orderInvalidTime'])
            ->setCellValue("Q" . $i, $data['orderInvalidFlag'])
            ->setCellValue("R" . $i, $data['errorCode'])
            ->setCellValue("S" . $i, $data['errorText'])
            ->setCellValue("T" . $i, $data['chkStatus'])
            ->setCellValue("U" . $i, $data['chkTime'])
            ->setCellValue("V" . $i, $data['chkAmt'])
            ->setCellValue("W" . $i, $data['clrFlag'])
            ->setCellValue("X" . $i, $data['clrTime'])
            ->setCellValue("Y" . $i, $data['clrAmt'])
            ->setCellValue("Z" . $i, $data['clrErrorText'])
            ->setCellValue("AA" . $i, $data['clrMode'])
            ->setCellValue("AB" . $i, $data['cfmFlag'])
            ->setCellValue("AC" . $i, $data['cfmAmt'])
            ->setCellValue("AD" . $i, $data['bckFlag'])
            ->setCellValue("AE" . $i, $data['bckAmt'])
            ->setCellValue("AF" . $i, $data['rltOrderNbr'])
            ->setCellValue("AG" . $i, $data['pltNbr'])
            ->setCellValue("AH" . $i, $data['mchNbr'])
            ->setCellValue("AI" . $i, $data['upayMchNbr'])
            ->setCellValue("AJ" . $i, $data['epccMchNbr'])
            ->setCellValue("AK" . $i, $data['uniPltNbr'])
            ->setCellValue("AL" . $i, $data['uniMchNbr'])
            ->setCellValue("AM" . $i, $data['mchAlias'])
            ->setCellValue("AN" . $i, $data['pltRemark'])
            ->setCellValue("AO" . $i, $data['orderRemark'])
            ->setCellValue("AP" . $i, $data['payerIssrId'])
            ->setCellValue("AQ" . $i, $data['payerAccIssrId'])
            ->setCellValue("AR" . $i, $data['payerAccNbr'])
            ->setCellValue("AS" . $i, $data['payerAccName'])
            ->setCellValue("AT" . $i, $data['payerBankFlag'])
            ->setCellValue("AU" . $i, $data['payerBankNbr'])
            ->setCellValue("AV" . $i, $data['payerBankName'])
            ->setCellValue("AW" . $i, $data['payeeIssrId'])
            ->setCellValue("AX" . $i, $data['payeeAccIssrId'])
            ->setCellValue("AY" . $i, $data['payeeAccNbr'])
            ->setCellValue("AZ" . $i, $data['payeeAccName'])
            ->setCellValue("BA" . $i, $data['payeeBankFlag'])
            ->setCellValue("BB" . $i, $data['payeeBankNbr'])
            ->setCellValue("BC" . $i, $data['payeeBankName'])
            ->setCellValue("BD" . $i, $data['subMchNbr'])
            ->setCellValue("BE" . $i, $data['subMchType'])
            ->setCellValue("BF" . $i, $data['subMchName']);
        $i++;
    }
    $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007");
    // 1. 不要直接输出到浏览器，先输出到内存
    ob_start();
    $PHPWriter->save("php://output");
    $excelData = ob_get_contents();
    ob_end_clean();

    $fileName = "招行对公对账单_{$billDate}_" . time() . ".xlsx";
    $localFilePath = public_path() . $fileName;

    file_put_contents($localFilePath, $excelData);
    try {
        $tempFile = tempnam(sys_get_temp_dir(), 'cmb_bill');
        file_put_contents($tempFile, $excelData);

        $extension = 'xlsx';
        $key = 'excel/' . date('Ym') . '/' . md5(uniqid(rand(), true)) . '.' . $extension;
        $url = AliOss::upload($key, $tempFile); // 假设这个方法接受本地文件路径
        $url = getSignedUrl($url);
        unlink($tempFile); // 上传完删除临时文件
        unlink($localFilePath);

        return  success('下载成功', ['url' => $url]);
    } catch (\Exception $e) {
        return  error('下载失败');
    }
}



