<?php

use RadishesFlight\Pay\XyWft\Requset;

require_once __DIR__ . '/../vendor/autoload.php';


$payHandle = new Requset([
    'url' => 'https://pay.swiftpass.cn/pay/gateway',     /*支付接口请求地址，请联系技术支持确认 */
    'mchId' => '403560168892',   /* 商户号，建议用正式的，于申请成功后的开户邮件中获取，若未开户需用测试的请联系技术支持 */
    'version' => '2.0',
    'sign_type' => 'RSA_1_256',
    'notify_url' => 'https://mall.styy1997.com/api/pay/xinYePayNotify',
    'key' => '',
    'public_rsa_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnmpxXjTJVhVXr2rPs9uBfWhAH9cLhBT+KJ5UiLUd0uxrPVec8WCQyZPQnnL0JOgQSBr0rWLcGVxGAdQUt748DfDGn6G/fhdFGJaHwCRfqxM7ymJ/xPm3UQNJeCF1qjDffdRilYwP4FSyiI+T8eAIeSABUTBDx7kkwvkbVi14znZMmDpLx6QNZlAclAZdHemqB/sSSsgfB7yCBJ4K0Fv4uK4qU3EQeOxhXLkitkhPm3uL5O3jgf89B1ZFpiCpI5qyysmPzJuZbvIBfa9OkVeHqINTPCWYCdx3ZD+k9+H5hoXKFDXddvxHptXrPU3APiBcDkgViYbfnFxWjYwaDQDkYQIDAQAB',   /* RSA验签平台公钥，建议用正式的，登录商户后台查看，若未开户需用测试的请联系技术支持 */
    'private_rsa_key' => 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCCgy6MDcdNzJ1qGlXEOQAMyZ/XfrxmcFlyOmQwscsmIjpDnSniIfdm3Hfs/BsMusUbsCZZ3eIjQ9csMQpAoSBu40PCfmptsWtNc3O0RR6tEb1LKtSLp5SNtSTGLZCfCJm10UfomkZUDIQ4XEO6OobBGQOmyjA7ZN51u1Gcg84G//fEwaT8jli9yw9YiOvLtdrrt4VbWKoVxfPiOVLwb00troMt1fCJl92QtMNyc03ZgaZ8ayYfH7qcu0Pi7TLOEeSNHViyyHxa5vX1YbXycOh0KHSkbyHdVhqCUBYzmSyH1kJ9EWtn93zH9G0FgynQAzn5Fd+mT6kaNYHirTYs+QCZAgMBAAECggEAdGCTdhGnQemOCJnZFrMZJ032+UqqptHSALiutHklxChLOhV/zoQpPxCi47BeUniM4MavO/1N89I/oclM8hp7eEWxG1Jshsno+9RSPVJRK1ShLdDQXIOfRMldNFZXGmip3+XxMCm5QqMyl6s2PW6I0NEEX4r1fVDRybux51Xktnnmx6T97A5aCk5yOCOASBYREvBE0Dp4/o6wMvPsYxDHmxaL7GO782jzG2zb8n3ZQLTJejaL0T/LxRlxe31LGICsuJr5fc9oc6QO9z130QjSZF61nxqdnFAUCTGlIDCW2xVhGHW3wU209aXWQZZsgHfnQJRufBKRRbrR4Khsj3KrvQKBgQDe1eHJHKyCjU8r9gc9BiePmx0uelmVgUuj5+ikt0pVjzNLsRn0QUe/tftRHmQkh2SeEiJkbT4WVnbck0/oETTpI0Ya1+6F47YpGO6U3d0AtySWnftZRB829jDj5Uo2o0WuAaZ5aUB5oVfe3Lr364a4kOOagxJ036Ve9WVwrwT4AwKBgQCV78GGXmKOjwiQz8YlVL+g0ylwNb4xhLIcEZ6ejcFzDGdpbmBHnZq4kWxhfNbm6qj+Tx2KoHPaptrbvEbvFAV3iZxJXj6iDO6Cgd1yb3OFjeaipYDdjAjQ8Qx6BaKK+MEL7NwKC2mOn9VZxheRNzRBw1UfQ8zUluOQIBKQM76IMwKBgQDQrx6vGCJadPnjIpouxRfBfjN69mv0/kwXKLUPpPOBYwVX6nhy7bvCxyugEUUZjI5nFnaM9F2Dz9+qvG7F129ksnsR4oznaJSMsmOkmI5DAEDMqRDdzVUqRK8OjgnNV2SHC9aatz4Bal7/QFn8md7l9BKi/gMH3vZhEpG4UL/nfwKBgQCJd8vQXYMxP3TUCJucKIqVcmVgyvV5QzdlwsXSixedWvcJVDiUEK0FoddjvmjuSKHuoCzup9Pw2eB5bLMAijPE+HdBUVZNj1uybkzRmdupzIN0BhgTiEug/hC5Y6c2kYG1ZFIOJ459RJABAj0jWCDiVqwZwTjwhPNZdf5vFfIPvwKBgBTf6Xmq9EqH1MFuhafEh7/ZYC6n+yZDbymJlBV+UCFShT+s2Pj6RvoLsXZNSxgZJ3kEfTce110+v2vS6CistNM8cltk+Kq6Dovji/LLxVrqtRnysrLBAg1aaNUUiphpV/wmcMumgXXjpsHNbzBVPiOaqAGx4hSZmBlSvWeTbHQu'
]);
//回调
$xml = file_get_contents('php://input');
$back =  $payHandle->callback($xml);

//下单
$data = [
    'out_trade_no' => mt_rand(10000000000, 99999999999),
    'total_fee' => 100, //单位为分
    //unified.trade.native 表示统一扫码
    //pay.alipay.native  表示支付宝扫码
    //pay.unionpay.native   表示银联钱包扫码
    //pay.weixin.raw.app 表示微信
    'service' => 'unified.trade.native',
    'body' => '顺泰药业',
    'mch_create_ip' => $data['ip'] ?? '127.0.0.1',
    'attach' => '备注'
];
//文档地址https://open.swiftpass.cn/openapi/doc?index_1=187&index_2=1&chapter_1=2328&chapter_2=2329
$result = $payHandle->submitOrderInfo($data);
$result = json_decode($result, true);
if ($result['status'] == 200) {
    print_r($result);
    exit;
}